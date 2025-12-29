<?php
// Controller/AuthController.php

header("Content-Type: application/json; charset=utf-8");

// ===============================
// DEPENDENCIAS
// ===============================
require_once __DIR__ . "/../Model/db.php";
require_once __DIR__ . "/../Model/Service/AuthService.php";

// ===============================
// INICIALIZACIÓN
// ===============================
$pdo = obtenerConexion();
$service = new AuthService($pdo);

$accion = $_GET["accion"] ?? "";

// Leer body JSON de forma segura
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$data = is_array($data) ? $data : [];

// ===============================
// HELPER DE RESPUESTA
// ===============================
function responder(array $payload, int $code = 200): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

// ===============================
// ROUTES (SIN SWITCH)
// ===============================
$routes = [

    // -------- REGISTRO CLIENTE --------
    "registrar" => function () use ($service, $data) {
        $email = trim($data["email"] ?? "");
        $clave = trim($data["clave"] ?? "");

        $res = $service->registrarCliente($email, $clave);

        if (isset($res["error"])) {
            $code = 400;
            if (str_contains($res["error"], "ya está registrado")) {
                $code = 409;
            }
            responder($res, $code);
        }

        responder($res, 200);
    },

    // -------- REGISTRO EMPRESA --------
    "registrarEmpresa" => function () use ($service, $data) {
        $res = $service->registrarEmpresa($data);

        if (isset($res["error"])) {
            $code = 400;

            if (str_contains($res["error"], "ya está registrado")) {
                $code = 409;
            }

            if (str_contains($res["error"], "Error al registrar empresa")) {
                $code = 500;
            }

            responder($res, $code);
        }

        responder($res, 200);
    },

    // -------- LOGIN --------
    "login" => function () use ($service, $data) {
        $email = trim($data["email"] ?? "");
        $clave = trim($data["clave"] ?? "");

        $res = $service->login($email, $clave);

        if (isset($res["error"])) {
            $code = 400;

            if ($res["error"] === "Credenciales incorrectas") {
                $code = 401;
            }

            responder($res, $code);
        }

        responder($res, 200);
    },

];

// ===============================
// EJECUCIÓN
// ===============================
if (!isset($routes[$accion])) {
    responder(["error" => "Acción no válida"], 400);
}

$routes[$accion]();
