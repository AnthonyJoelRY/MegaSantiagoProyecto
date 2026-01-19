<?php
declare(strict_types=1);

// Model/DAO/SucursalDAO.php
// DAO para tabla `sucursales`

class SucursalDAO
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, array<string,mixed>> */
    public function listarActivas(): array
    {
        $stmt = $this->pdo->query("SELECT id_sucursal, nombre, direccion, ciudad, telefono, horario FROM sucursales WHERE activo = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<int, array<string,mixed>> */
  public function listar(?string $q = null): array
{
    $sql = "
        SELECT
            s.*
        FROM sucursales s
    ";

    $params = [];

    if ($q !== null && trim($q) !== '') {
        $sql .= " WHERE (s.nombre LIKE :q1 OR s.direccion LIKE :q2) ";
        $like = "%" . trim($q) . "%";
        $params[":q1"] = $like;
        $params[":q2"] = $like;
    }

    $sql .= " ORDER BY s.id_sucursal DESC ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}


    public function crear(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO sucursales (nombre, direccion, ciudad, telefono, horario, activo)
             VALUES (:nombre, :direccion, :ciudad, :telefono, :horario, 1)"
        );
        $stmt->execute([
            "nombre" => (string)($data["nombre"] ?? ''),
            "direccion" => (string)($data["direccion"] ?? ''),
            "ciudad" => (string)($data["ciudad"] ?? ''),
            "telefono" => (string)($data["telefono"] ?? ''),
            "horario" => (string)($data["horario"] ?? ''),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE sucursales SET
                nombre = :nombre,
                direccion = :direccion,
                ciudad = :ciudad,
                telefono = :telefono,
                horario = :horario
             WHERE id_sucursal = :id"
        );
        $stmt->execute([
            "id" => $id,
            "nombre" => (string)($data["nombre"] ?? ''),
            "direccion" => (string)($data["direccion"] ?? ''),
            "ciudad" => (string)($data["ciudad"] ?? ''),
            "telefono" => (string)($data["telefono"] ?? ''),
            "horario" => (string)($data["horario"] ?? ''),
        ]);
    }

    public function activar(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE sucursales SET activo = 1 WHERE id_sucursal = :id");
        $stmt->execute(["id" => $id]);
    }

    public function desactivar(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE sucursales SET activo = 0 WHERE id_sucursal = :id");
        $stmt->execute(["id" => $id]);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM sucursales WHERE id_sucursal = :id LIMIT 1");
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
