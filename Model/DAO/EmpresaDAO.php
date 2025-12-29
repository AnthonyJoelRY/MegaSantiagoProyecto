<?php
// Model/DAO/EmpresaDAO.php

class EmpresaDAO {
    public function __construct(private PDO $pdo) {}

    public function crear(array $empresaData): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO empresas
            (nombre_legal, ruc, email_empresa, telefono, direccion_fiscal, ciudad, pais, tipo_negocio)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $empresaData["nombre_legal"],
            $empresaData["ruc"],
            $empresaData["email_empresa"],
            $empresaData["telefono"],
            $empresaData["direccion_fiscal"],
            $empresaData["ciudad"],
            $empresaData["pais"],
            $empresaData["tipo_negocio"],
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}
