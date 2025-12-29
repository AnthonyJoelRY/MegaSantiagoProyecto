<?php
// Model/DAO/RolDAO.php

class RolDAO {
    public function __construct(private PDO $pdo) {}

    public function obtenerIdPorNombre(string $nombre, int $fallback): int {
        $sql = "SELECT id_rol FROM roles WHERE LOWER(nombre) = LOWER(?) LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nombre]);
        $fila = $stmt->fetch();
        return ($fila && isset($fila["id_rol"])) ? (int)$fila["id_rol"] : $fallback;
    }

    public function obtenerIdCliente(): int {
        return $this->obtenerIdPorNombre("cliente", 3);
    }

    public function obtenerIdVendedor(): int {
        return $this->obtenerIdPorNombre("vendedor", 2);
    }
}
