<?php
// Model/Entity/Usuario.php

class Usuario {
    public int $id_usuario;
    public int $id_rol;
    public string $email;
    public string $clave_hash;
    public int $activo;

    public static function fromRow(array $row): self {
        $u = new self();
        $u->id_usuario = (int)($row["id_usuario"] ?? 0);
        $u->id_rol     = (int)($row["id_rol"] ?? 0);
        $u->email      = (string)($row["email"] ?? "");
        $u->clave_hash = (string)($row["clave_hash"] ?? "");
        $u->activo     = (int)($row["activo"] ?? 0);
        return $u;
    }
}
