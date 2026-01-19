<?php
// Controller/AdminSyncController.php
// Endpoint interno para el panel de administración.
// - No expone SYNC_API_KEY en la vista.
// - Valida sesión/rol admin.
// - Ejecuta SyncManager directo (sin HTTP).

declare(strict_types=1);

require_once __DIR__ . '/_helpers/Api.php';
api_send_json_headers(true);
api_handle_options();

require_once __DIR__ . '/../Model/Config/base.php';
require_once __DIR__ . '/../Model/Service/SyncAdminService.php';

function admin_require_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // El proyecto guarda: $_SESSION['usuario'] y $_SESSION['rol']
    if (!isset($_SESSION['usuario'], $_SESSION['rol'])) {
        api_error('No autorizado', 401);
    }

    $rol = (int)$_SESSION['rol'];
    if ($rol !== 1) {
        api_error('Solo administrador', 403);
    }
}

try {
    admin_require_session();

    $accion  = $_GET['accion'] ?? 'import';
    $entidad = $_GET['entidad'] ?? 'productos';
    $mode    = $_GET['mode'] ?? 'import';

    // Permite alias simple: ?accion=import
    if ($accion === 'import') $mode = 'import';
    if ($accion === 'export') $mode = 'export';
    if ($accion === 'both')   $mode = 'both';

    // Sanitiza entidad
    $entidad = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$entidad);
    if ($entidad === '') {
        api_error('Entidad inválida', 400);
    }

    $service = new SyncAdminService();
    $result = $service->ejecutar($entidad, (string)$mode);

    api_ok([
        'result' => $result,
    ]);

} catch (Throwable $e) {
    api_error('Error en AdminSyncController', 500, [
        'detalle' => $e->getMessage(),
    ]);
}
