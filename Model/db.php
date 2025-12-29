<?php
// Siempre incluye usando __DIR__ para que no falle la ruta
include __DIR__ . '/credenciales.php';

function obtenerConexion() {
    global $host, $port, $dbname, $user, $pass;

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "error" => "Error de conexión a la BD",
            "detalle" => $e->getMessage() // 👈 para ver el error real
        ]);
        exit;
    }
}

