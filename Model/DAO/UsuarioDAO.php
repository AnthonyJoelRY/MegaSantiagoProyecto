<?php
declare(strict_types=1);

// Model/DAO/UsuarioDAO.php

require_once __DIR__ . "/../Entity/Usuario.php";

class UsuarioDAO
{
    public function __construct(private PDO $pdo) {}

    public function existePorEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM usuarios WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);

        // Más eficiente que traer id_usuario
        return (bool)$stmt->fetchColumn();
    }

    public function crear(int $idRol, string $email, string $claveHash): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (id_rol, email, clave_hash, activo)
            VALUES (?, ?, ?, 1)
        ");
        $stmt->execute([$idRol, $email, $claveHash]);

        return (int)$this->pdo->lastInsertId();
    }

    public function obtenerPorEmail(string $email): ?Usuario
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        // Fuerza array asociativo para que fromRow funcione siempre
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Usuario::fromRow($row) : null;
    }
}
