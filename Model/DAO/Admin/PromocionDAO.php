<?php
// Model/DAO/Admin/PromocionDAO.php

require_once __DIR__ . '/../../DB/db.php';

require_once __DIR__ . '/../../Entity/Promocion.php';


class PromocionDAO
{
    private PDO $db;

    public function __construct()
    {
        // Usa la infraestructura real del proyecto
        $this->db = obtenerConexion();
    }

    /**
     * Crear promoción (dashboard)
     */
    public function crear(Promocion $p): bool
    {
        $sql = "INSERT INTO promociones
                (nombre, descripcion, imagen_banner, fecha_inicio, fecha_fin, activo)
                VALUES
                (:nombre, :descripcion, :imagen_banner, :fecha_inicio, :fecha_fin, :activo)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre'        => $p->nombre,
            ':descripcion'   => $p->descripcion,
            ':imagen_banner' => $p->imagen_banner,
            ':fecha_inicio'  => $p->fecha_inicio,
            ':fecha_fin' => $p->fecha_fin !== '' ? $p->fecha_fin : null,
            ':activo'        => $p->activo
        ]);
    }

    /**
     * Listar promociones (dashboard)
     */
    public function listar(): array
    {
        $sql = "SELECT *
                FROM promociones
                ORDER BY id_promocion DESC";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => Promocion::fromRow($row),
            $rows
        );
    }

    /**
     * Listar promociones activas para carrusel público
     */
    public function listarActivasCarrusel(): array
    {
        $sql = "SELECT *
                FROM promociones
                WHERE activo = 1
                  AND imagen_banner IS NOT NULL
                  AND fecha_inicio <= CURDATE()
                  AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
                ORDER BY id_promocion DESC";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => Promocion::fromRow($row),
            $rows
        );
    }
    
    /**
 * Listar SOLO promociones tipo banner (dashboard)
 */
public function listarBanners(): array
{
    $sql = "SELECT *
            FROM promociones
            WHERE imagen_banner IS NOT NULL
            ORDER BY id_promocion DESC";

    $stmt = $this->db->query($sql);
    $rows = $stmt->fetchAll();

    return array_map(
        fn($row) => Promocion::fromRow($row),
        $rows
    );
}
    
    /**
 * Cambiar estado activo/inactivo
 */
public function cambiarEstado(int $id, int $estado): bool
{
    $sql = "UPDATE promociones
            SET activo = :estado
            WHERE id_promocion = :id";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':estado' => $estado,
        ':id'     => $id
    ]);
}
    
    public function eliminar(int $id): bool
{
    $sql = "DELETE FROM promociones WHERE id_promocion = :id";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':id' => $id
    ]);
}



}
