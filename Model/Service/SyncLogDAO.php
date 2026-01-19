<?php
/**
 * Model/Sync/SyncLogDAO.php
 *
 * DAO encargado de registrar el historial de sincronizaciones.
 * Permite saber cuándo se ejecutó un sync, si fue exitoso y cuántos
 * registros se importaron/exportaron.
 */

class SyncLogDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Registra el inicio de una sincronización.
     */
    public function start(string $requestId, string $entidad, string $modo): void
    {
        $sql = "
            INSERT INTO sync_log (
                request_id,
                entidad,
                mode,
                started_at,
                ok
            ) VALUES (
                :request_id,
                :entidad,
                :mode,
                NOW(),
                0
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':request_id' => $requestId,
            ':entidad'    => $entidad,
            ':mode'       => $modo
        ]);
    }

    /**
     * Marca la sincronización como exitosa y guarda estadísticas.
     */
    public function finishOk(string $requestId, array $stats): void
    {
        $sql = "
            UPDATE sync_log
            SET
                finished_at        = NOW(),
                ok                 = 1,
                imported_fetched   = :imp_fetched,
                imported_applied   = :imp_applied,
                imported_conflicts = :imp_conflicts,
                exported_fetched   = :exp_fetched,
                exported_applied   = :exp_applied,
                exported_conflicts = :exp_conflicts,
                message            = NULL
            WHERE request_id = :request_id
            ORDER BY id DESC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':imp_fetched'   => $stats['import']['fetched']   ?? 0,
            ':imp_applied'   => $stats['import']['applied']   ?? 0,
            ':imp_conflicts' => $stats['import']['conflicts'] ?? 0,
            ':exp_fetched'   => $stats['export']['fetched']   ?? 0,
            ':exp_applied'   => $stats['export']['applied']   ?? 0,
            ':exp_conflicts' => $stats['export']['conflicts'] ?? 0,
            ':request_id'    => $requestId
        ]);
    }

    /**
     * Marca la sincronización como fallida y guarda el mensaje de error.
     */
    public function finishError(string $requestId, string $errorMessage): void
    {
        $sql = "
            UPDATE sync_log
            SET
                finished_at = NOW(),
                ok          = 0,
                message     = :message
            WHERE request_id = :request_id
            ORDER BY id DESC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':message'    => $errorMessage,
            ':request_id' => $requestId
        ]);
    }

    /**
     * Obtiene el historial de sincronizaciones de una entidad.
     */
    public function getHistory(string $entidad, int $limit = 20): array
    {
        $sql = "
            SELECT
                request_id,
                entidad,
                mode,
                started_at,
                finished_at,
                ok,
                imported_fetched,
                imported_applied,
                imported_conflicts,
                exported_fetched,
                exported_applied,
                exported_conflicts,
                message
            FROM sync_log
            WHERE entidad = :entidad
            ORDER BY started_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
