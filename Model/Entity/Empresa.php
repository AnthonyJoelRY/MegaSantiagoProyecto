<?php
// Model/Entity/Empresa.php

class Empresa {
    public int $id_empresa;
    public string $nombre_legal;
    public string $ruc;
    public string $email_empresa;
    public string $telefono;
    public string $direccion_fiscal;
    public string $ciudad;
    public string $pais;
    public string $tipo_negocio;

    public static function fromRow(array $row): self {
        $e = new self();
        $e->id_empresa        = (int)($row["id_empresa"] ?? 0);
        $e->nombre_legal      = (string)($row["nombre_legal"] ?? "");
        $e->ruc               = (string)($row["ruc"] ?? "");
        $e->email_empresa     = (string)($row["email_empresa"] ?? "");
        $e->telefono          = (string)($row["telefono"] ?? "");
        $e->direccion_fiscal  = (string)($row["direccion_fiscal"] ?? "");
        $e->ciudad            = (string)($row["ciudad"] ?? "");
        $e->pais              = (string)($row["pais"] ?? "");
        $e->tipo_negocio      = (string)($row["tipo_negocio"] ?? "");
        return $e;
    }
}
