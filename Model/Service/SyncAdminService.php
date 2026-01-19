<?php
// Model/Service/SyncAdminService.php
// Servicio para ejecutar sincronización desde el panel (sin exponer token en la vista).

declare(strict_types=1);

require_once __DIR__ . '/../DB/db.php';
require_once __DIR__ . '/../DB/ExternalDBConnection.php';
require_once __DIR__ . '/../Sync/SyncManager.php';

class SyncAdminService
{
    /**
     * Ejecuta sincronización para una entidad.
     *
     * @param string $entidad  Ej: 'productos'
     * @param string $mode     'import' | 'export' | 'both'
     * @return array           Resultado del SyncManager
     */
    public function ejecutar(string $entidad, string $mode = 'import'): array
    {
        $mode = strtolower(trim($mode));
        if (!in_array($mode, ['import', 'export', 'both'], true)) {
            throw new InvalidArgumentException('mode inválido. Usa: import|export|both');
        }

        $local = obtenerConexion();
        $external = ExternalDBConnection::getInstance();
        $manager = new SyncManager($local, $external);

        return $manager->sync($entidad, $mode);
    }
}
