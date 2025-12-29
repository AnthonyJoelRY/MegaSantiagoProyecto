<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Controller/paypalController.php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

require_once __DIR__ . "/../Model/db.php";
require_once __DIR__ . "/../Model/Service/PayPalService.php";
require_once __DIR__ . "/../Model/Service/PedidoService.php";


$pdo = obtenerConexion();
$service = new PayPalService($pdo);

$accion = $_GET["accion"] ?? "";

function responder($payload, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function leerJsonBody(): array
{
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

$routes = [

    "config" => function () use ($service) {
        responder($service->getPublicConfig(), 200);
    },

    "create-order" => function () use ($service) {
        $data = leerJsonBody();
        $cart = $data["cart"] ?? [];
        if (!is_array($cart)) responder(["error" => "Body inválido: cart debe ser array."], 400);

        $res = $service->createOrderFromCart($cart);
        if (isset($res["error"])) responder($res, 400);
        responder($res, 200);
    },

    "capture-order" => function () use ($service, $pdo) {

        $data = leerJsonBody();
        $orderId   = $data["orderId"] ?? "";
        $carrito   = $data["cart"] ?? [];
        $idUsuario = (int)($data["id_usuario"] ?? 0);

        if (!$idUsuario || empty($carrito)) {
            responder(["error" => "Datos incompletos"], 400);
        }

        // 1️⃣ Capturar pago en PayPal
        $res = $service->captureOrder($orderId);

        // ⛔ SI NO SE COMPLETÓ, NO GUARDAMOS NADA
        if (!isset($res["status"]) || $res["status"] !== "COMPLETED") {
            responder([
                "error" => "El pago no fue completado",
                "paypal_status" => $res["status"] ?? "desconocido"
            ], 400);
        }

        // 2️⃣ GUARDAR PEDIDO SOLO SI ESTÁ COMPLETADO
        $pedidoService = new PedidoService($pdo);
        $idPedido = $pedidoService->crearPedido($idUsuario, $carrito);

        responder([
            "ok" => true,
            "pedido_id" => $idPedido
        ]);
    },


];

if (!isset($routes[$accion])) {
    responder(["error" => "Acción no válida"], 400);
}

$routes[$accion]();
