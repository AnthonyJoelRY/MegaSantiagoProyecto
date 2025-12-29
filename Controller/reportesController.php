<?php
// Controller/reportesController.php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/../Model/db.php";
require_once __DIR__ . "/../Model/Service/ReporteService.php";

$pdo = obtenerConexion();
$service = new ReporteService($pdo);

$accion = $_GET["accion"] ?? "";

function responder($data)
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($accion) {

    case "kpis":
        responder([
            "ventasTotales"      => $service->ventasTotales(),
            "totalPedidos"       => $service->totalPedidos(),
            "totalClientes"      => $service->totalClientes(),
            "promedioPorPedido"  => $service->promedioPorPedido(),
            "totalIVA"           => $service->totalIVA()
        ]);
        break;

    case "ventasDia":
        responder($service->ventasPorDia());
        break;

    case "ventasMes":
        responder($service->ventasPorMes());
        break;

    case "productosMasVendidos":
        responder(
            $service->productosMasVendidos(5)
        );
        break;

    case "productosMenosVendidos":
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 5;
        responder($service->productosMenosVendidos($limit));
        break;

    case "clientesTop":
        responder($service->clientesTop());
        break;



    default:
        responder(["error" => "Acción no válida"]);
}
