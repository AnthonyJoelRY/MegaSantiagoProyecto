<?php
/**
 * Model/Sync/SyncControlDAO.php
 *
 * Maneja la tabla sync_control en la BD local.
 */

class SyncControlDAO
{
    public function __construct(private PDO $pdo)
    {
    }

    public function ensureTable(): void
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS sync_control (
                id INT AUTO_INCREMENT PRIMARY KEY,
                entidad VARCHAR(50) NOT NULL UNIQUE,
                last_sync_local DATETIME NULL,
                last_sync_external DATETIME NULL,
                estado ENUM('OK','ERROR') DEFAULT 'OK',
                mensaje TEXT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public function getState(string $entidad): array
    {
        $st = $this->pdo->prepare("SELECT * FROM sync_control WHERE entidad = ? LIMIT 1");
        $st->execute([$entidad]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $this->pdo->prepare("INSERT INTO sync_control(entidad, estado) VALUES(?, 'OK')")
                ->execute([$entidad]);
            return [
                'entidad' => $entidad,
                'last_sync_local' => null,
                'last_sync_external' => null,
                'estado' => 'OK',
                'mensaje' => null,
            ];
        }
        return $row;
    }

    public function markOk(string $entidad, ?string $lastLocal, ?string $lastExternal): void
    {
        $st = $this->pdo->prepare(
            "UPDATE sync_control
             SET last_sync_local = ?, last_sync_external = ?, estado='OK', mensaje=NULL
             WHERE entidad = ?"
        );
        $st->execute([$lastLocal, $lastExternal, $entidad]);
    }

    public function markError(string $entidad, string $message): void
    {
        $st = $this->pdo->prepare(
            "UPDATE sync_control SET estado='ERROR', mensaje=? WHERE entidad = ?"
        );
        $st->execute([$message, $entidad]);
    }
}
