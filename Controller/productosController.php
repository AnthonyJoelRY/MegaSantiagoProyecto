<?php
// Controller/productosController.php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/../Model/db.php";
require_once __DIR__ . "/../Model/Service/ProductoService.php";

$pdo = obtenerConexion();
$service = new ProductoService($pdo);

$accion = $_GET["accion"] ?? "";

function responder($payload, int $code = 200): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$routes = [

    "listarPorCategoria" => function () use ($service) {
        $categoria = trim($_GET["categoria"] ?? "");
        $res = $service->listarPorCategoria($categoria);
        if (isset($res["error"])) responder($res, 400);
        responder($res, 200);
    },

    "buscar" => function () use ($service) {
        $termino = trim($_GET["q"] ?? "");
        $idUsuario = isset($_GET["id_usuario"]) ? (int)$_GET["id_usuario"] : null;

        $res = $service->buscar($termino, $idUsuario);
        if (isset($res["error"])) responder($res, 400);
        responder($res, 200);
    },

    "masVendidos" => function () use ($service) {
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 4;
        $res = $service->masVendidos($limit);
        if (isset($res["error"])) responder($res, 400);
        responder($res, 200);
    },

    "ofertas" => function () use ($service) {
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 6;
        $res = $service->ofertas($limit);
        if (isset($res["error"])) responder($res, 400);
        responder($res, 200);
    },

    "promociones" => function () use ($service) {
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
        $res = $service->promociones($limit);
        if (isset($res["error"])) responder($res, 400);
        responder($res, 200);
    },

];

if (!isset($routes[$accion])) {
    responder(["error" => "Acción no válida"], 400);
}

$routes[$accion]();
