<?php
/**
 * Model/Sync/SyncManager.php
 *
 * Sincronización incremental bidireccional (local <-> externa).
 *
 * Requisitos recomendados por tabla sincronizada:
 * - columna key (por defecto: sync_uuid) con índice UNIQUE en ambas BDs
 * - columna updated_at (DATETIME) en ambas BDs
 */

require_once __DIR__ . '/SyncControlDAO.php';
require_once __DIR__ . '/ConflictResolver.php';

class SyncManager
{
    private SyncControlDAO $control;
    private ConflictResolver $resolver;

    /** @var array<string, mixed> */
    private array $map;

    public function __construct(private PDO $localPdo, private PDO $externalPdo)
    {
        $this->control = new SyncControlDAO($this->localPdo);
        $this->resolver = new ConflictResolver();
        $this->map = require __DIR__ . '/../Config/sync_map.php';
    }

    /**
     * Sincroniza una entidad definida en sync_map.php
     * @param string $entidad e.g. 'productos'
     * @param string $mode 'both'|'import'|'export'
     */
    public function sync(string $entidad, string $mode = 'both'): array
    {
        if (!isset($this->map[$entidad])) {
            throw new InvalidArgumentException("Entidad no configurada en sync_map.php: {$entidad}");
        }

        if (defined('SYNC_AUTO_CREATE_CONTROL_TABLE') && SYNC_AUTO_CREATE_CONTROL_TABLE) {
            $this->control->ensureTable();
        }

        $cfg = $this->map[$entidad];
        $key = $cfg['key'];
        $updatedAt = $cfg['updated_at'];

        $state = $this->control->getState($entidad);
        $lastLocal = $state['last_sync_local'] ?? null;
        $lastExternal = $state['last_sync_external'] ?? null;

        $stats = [
            'entidad' => $entidad,
            'mode' => $mode,
            'import' => ['fetched'=>0,'applied'=>0,'conflicts'=>0],
            'export' => ['fetched'=>0,'applied'=>0,'conflicts'=>0],
            'last_local_before' => $lastLocal,
            'last_external_before' => $lastExternal,
        ];

        try {
            // IMPORT: externa -> local
            if ($mode === 'both' || $mode === 'import') {
                $r = $this->importToLocal($cfg, $key, $updatedAt, $lastExternal);
                $stats['import'] = $r;
                // si importamos, actualizamos last_sync_external (porque consumimos cambios externos)
                $lastExternal = date('Y-m-d H:i:s');
            }

            // EXPORT: local -> externa
            if ($mode === 'both' || $mode === 'export') {
                $r = $this->exportToExternal($cfg, $key, $updatedAt, $lastLocal);
                $stats['export'] = $r;
                $lastLocal = date('Y-m-d H:i:s');
            }

            $this->control->markOk($entidad, $lastLocal, $lastExternal);
            $stats['last_local_after'] = $lastLocal;
            $stats['last_external_after'] = $lastExternal;
            return $stats;

        } catch (Throwable $e) {
            $this->control->markError($entidad, $e->getMessage());
            throw $e;
        }
    }

    /** @return array{fetched:int,applied:int,conflicts:int} */
    private function importToLocal(array $cfg, string $key, string $updatedAt, ?string $sinceExternal): array
    {
        $extTable = $cfg['external_table'];
        $localTable = $cfg['local_table'];
        $columns = $cfg['columns']; // local => external

        $externalCols = array_values($columns);
        $select = "SELECT " . implode(',', array_map(fn($c)=>"`$c`", $externalCols)) . " FROM `{$extTable}`";
        $params = [];
        if ($sinceExternal) {
            $select .= " WHERE `{$updatedAt}` > ?";
            $params[] = $sinceExternal;
        }

        $st = $this->externalPdo->prepare($select);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $fetched = count($rows);
        $applied = 0;
        $conflicts = 0;

        foreach ($rows as $extRow) {
            $uuid = $extRow[$columns[$key] ?? $key] ?? null;
            if (!$uuid) continue;

            // ¿Existe en local?
            $localRow = $this->fetchOne($this->localPdo, $localTable, $key, $uuid);

            if ($localRow) {
                // Conflicto: ambos cambiaron desde su último sync
                $localChanged = $sinceExternal ? (isset($localRow[$updatedAt]) && strtotime($localRow[$updatedAt]) > strtotime($sinceExternal)) : false;
                $externalChanged = true; // porque lo estamos procesando
                if ($localChanged && $externalChanged) {
                    $conflicts++;
                    $winner = $this->resolver->decide($localRow, $this->mapExternalToLocalRow($columns, $extRow), $updatedAt);
                    if ($winner === 'local') {
                        // gana local: no hacemos nada
                        continue;
                    }
                }
            }

            $this->upsert($this->localPdo, $localTable, $key, $columns, $extRow, true);
            $applied++;
        }

        return ['fetched'=>$fetched,'applied'=>$applied,'conflicts'=>$conflicts];
    }

    /** @return array{fetched:int,applied:int,conflicts:int} */
    private function exportToExternal(array $cfg, string $key, string $updatedAt, ?string $sinceLocal): array
    {
        $extTable = $cfg['external_table'];
        $localTable = $cfg['local_table'];
        $columns = $cfg['columns']; // local => external

        $localCols = array_keys($columns);
        $select = "SELECT " . implode(',', array_map(fn($c)=>"`$c`", $localCols)) . " FROM `{$localTable}`";
        $params = [];
        if ($sinceLocal) {
            $select .= " WHERE `{$updatedAt}` > ?";
            $params[] = $sinceLocal;
        }

        $st = $this->localPdo->prepare($select);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $fetched = count($rows);
        $applied = 0;
        $conflicts = 0;

        foreach ($rows as $localRow) {
            $uuid = $localRow[$key] ?? null;
            if (!$uuid) continue;

            $extRow = $this->fetchOne($this->externalPdo, $extTable, $columns[$key] ?? $key, $uuid);
            if ($extRow) {
                $externalChanged = $sinceLocal ? (isset($extRow[$updatedAt]) && strtotime($extRow[$updatedAt]) > strtotime($sinceLocal)) : false;
                $localChanged = true;
                if ($externalChanged && $localChanged) {
                    $conflicts++;
                    $winner = $this->resolver->decide($localRow, $this->mapExternalToLocalRow($columns, $extRow, true), $updatedAt);
                    if ($winner === 'external') {
                        continue;
                    }
                }
            }

            $this->upsert($this->externalPdo, $extTable, $columns[$key] ?? $key, $this->invertMap($columns), $localRow, false);
            $applied++;
        }

        return ['fetched'=>$fetched,'applied'=>$applied,'conflicts'=>$conflicts];
    }

    private function fetchOne(PDO $pdo, string $table, string $keyCol, string $keyVal): ?array
    {
        $st = $pdo->prepare("SELECT * FROM `{$table}` WHERE `{$keyCol}` = ? LIMIT 1");
        $st->execute([$keyVal]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * UPSERT genérico.
     * @param array $mapCols local=>external (cuando $fromExternal=true) o external=>local (cuando false)
     */
    private function upsert(PDO $pdo, string $table, string $keyCol, array $mapCols, array $row, bool $fromExternal): void
    {
        // $mapCols: destCol => srcCol (para insertar en destino)
        $destCols = array_keys($mapCols);
        $placeholders = array_map(fn($c)=>":".$c, $destCols);

        $updates = [];
        foreach ($destCols as $c) {
            if ($c === $keyCol) continue;
            $updates[] = "`{$c}` = VALUES(`{$c}`)";
        }

        $sql = "INSERT INTO `{$table}` (" . implode(',', array_map(fn($c)=>"`$c`", $destCols)) . ")
                VALUES (" . implode(',', $placeholders) . ")
                ON DUPLICATE KEY UPDATE " . implode(',', $updates);

        $st = $pdo->prepare($sql);
        $params = [];
        foreach ($mapCols as $dest => $src) {
            $params[":".$dest] = $row[$src] ?? null;
        }
        $st->execute($params);
    }

    /** @return array<string,string> */
    private function invertMap(array $map): array
    {
        // local=>external  => external=>local
        $inv = [];
        foreach ($map as $local => $ext) {
            $inv[$ext] = $local;
        }
        return $inv;
    }

    /**
     * Convierte una fila externa a “formato local” para comparar updated_at y decidir conflictos.
     */
    private function mapExternalToLocalRow(array $columns, array $externalRow, bool $externalKeysAlready = false): array
    {
        // $columns: local=>external
        $out = [];
        foreach ($columns as $local => $ext) {
            $k = $externalKeysAlready ? $local : $ext;
            $out[$local] = $externalRow[$k] ?? null;
        }
        return $out;
    }
}
