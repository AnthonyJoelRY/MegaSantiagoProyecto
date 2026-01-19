<?php
// Controller/SyncController.php
// API de sincronización incremental bidireccional (local <-> externa)

declare(strict_types=1);

require_once __DIR__ . "/_helpers/Api.php";
api_send_json_headers(true);
api_handle_options();

require_once __DIR__ . "/_helpers/Bootstrap.php";
require_once __DIR__ . "/../Model/DB/db.php";
require_once __DIR__ . "/../Model/DB/ExternalDBConnection.php";
require_once __DIR__ . "/../Model/Sync/SyncManager.php";
require_once __DIR__ . "/../Model/Config/sync.php";

function require_sync_auth(): void
{
   // 1) Intentar Authorization Bearer (normal)
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
$token = '';

if (preg_match('/Bearer\s+(.*)$/i', $auth, $m)) {
  $token = trim($m[1]);
}

// 2) Fallback SOLO PARA PRUEBAS en hosting: ?token=...
if ($token === '' && isset($_GET['token'])) {
  $token = trim($_GET['token']);
}

    // 3) Validar token
    if (!defined('SYNC_API_KEY')) {
        require_once __DIR__ . '/../Model/Config/sync.php';
    }

    if ($token === '' || !hash_equals((string)SYNC_API_KEY, $token)) {
        api_error('No autorizado (falta Bearer token)', 401);
    }

}

$accion = $_GET["accion"] ?? "health";

// health es público (sin token). El resto, protegido.
if ($accion !== 'health') {
    require_sync_auth();
}

try {
    if ($accion === 'health') {
        api_ok([
            "service" => "sync",
            "status" => "up",
            "time" => date('c'),
        ]);
    }

    $local = obtenerConexion();
    $external = ExternalDBConnection::getInstance();
    $manager = new SyncManager($local, $external);

    if ($accion === 'sync') {
        // Soporta:
        // - GET ?accion=sync&entidad=productos&mode=both
        // - POST JSON {"entidad":"productos","mode":"both"}
        $body = api_read_json_body();
        $entidad = $_GET['entidad'] ?? ($body['entidad'] ?? null);
        $mode = $_GET['mode'] ?? ($body['mode'] ?? 'both');

        if (!$entidad) {
            api_error("Falta 'entidad' (ej: productos)", 400);
        }
        if (!in_array($mode, ['both','import','export'], true)) {
            api_error("mode inválido. Usa: both|import|export", 400);
        }

        $result = $manager->sync((string)$entidad, (string)$mode);
        api_ok(["result" => $result]);
    }

    if ($accion === 'sync-all') {
        // Ejecuta todas las entidades configuradas en sync_map.php
        $map = require __DIR__ . "/../Model/Config/sync_map.php";
        $mode = $_GET['mode'] ?? 'both';
        if (!in_array($mode, ['both','import','export'], true)) {
            api_error("mode inválido. Usa: both|import|export", 400);
        }

        $out = [];
        foreach (array_keys($map) as $ent) {
            $out[] = $manager->sync($ent, $mode);
        }
        api_ok(["results" => $out]);
    }

    api_error("Acción no válida", 400, ["accion" => $accion]);

} catch (Throwable $e) {
    api_error("Error en SyncController", 500, ["detalle" => $e->getMessage()]);
}
