<?php
// Model/DAO/Admin/AdminProductoDAO.php

class AdminProductoDAO {
    public function __construct(private PDO $pdo) {}
    
    private function assertIdProducto(int $idProducto): void
{
    if ($idProducto <= 0) {
        throw new Exception("❌ ID de producto inválido: " . $idProducto);
    }
}


   public function listar(?string $q = null): array {
    $sql = "
        SELECT
            p.id_producto,
            p.nombre,
            p.sku,
            p.precio,
            p.precio_oferta,
            p.activo,
            IFNULL(i.stock_actual, 0) AS stock,
            img.url_imagen
        FROM productos p
        LEFT JOIN inventario i ON i.id_producto = p.id_producto
        LEFT JOIN producto_imagenes img
            ON img.id_producto = p.id_producto AND img.es_principal = 1
    ";

    $params = [];

    if ($q !== null && trim($q) !== '') {
        $sql .= " WHERE (p.nombre LIKE :q1 OR p.sku LIKE :q2) ";
        $like = "%" . trim($q) . "%";
        $params[":q1"] = $like;
        $params[":q2"] = $like;
    }

    $sql .= " ORDER BY p.id_producto DESC ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}


    public function obtenerPorId(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.id_producto,
                p.id_categoria,
                p.nombre,
                p.descripcion_corta,
                p.descripcion_larga,
                p.precio,
                p.precio_oferta,
                p.sku,
                p.aplica_iva,
                p.activo,
                p.stock_minimo,
                IFNULL(i.stock_actual, 0) AS stock_actual,
                img.url_imagen
            FROM productos p
            LEFT JOIN inventario i ON i.id_producto = p.id_producto
            LEFT JOIN producto_imagenes img 
                ON img.id_producto = p.id_producto AND img.es_principal = 1
            WHERE p.id_producto = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function categoriasActivas(): array {
        return $this->pdo->query("
            SELECT id_categoria, nombre
            FROM categorias
            WHERE activo = 1
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

public function insertarProducto(array $params): int
{
    $stmt = $this->pdo->prepare("
        INSERT INTO productos (
            id_categoria,
            nombre,
            descripcion_corta,
            descripcion_larga,
            precio,
            precio_oferta,
            sku,
            aplica_iva,
            stock_minimo
        ) VALUES (
            :id_categoria,
            :nombre,
            :descripcion_corta,
            :descripcion_larga,
            :precio,
            :precio_oferta,
            :sku,
            :aplica_iva,
            :stock_minimo
        )
    ");

    $stmt->execute([
        ":id_categoria"        => $params["id_categoria"],
        ":nombre"              => $params["nombre"],
        ":descripcion_corta"   => $params["descripcion_corta"],
        ":descripcion_larga"   => $params["descripcion_larga"],
        ":precio"              => $params["precio"],
        ":precio_oferta"       => $params["precio_oferta"] ?? null,
        ":sku"                 => $params["sku"],
        ":aplica_iva"          => $params["aplica_iva"],
        ":stock_minimo"        => $params["stock_minimo"],
    ]);

    $id = (int)$this->pdo->lastInsertId();

    if ($id <= 0) {
        throw new Exception("❌ No se pudo obtener el id_producto");
    }

    return $id;
}




 
    
public function actualizarInventario(int $idProducto, array $data): void {
    $this->assertIdProducto($idProducto);

    $stmt = $this->pdo->prepare("
        UPDATE inventario
        SET stock_actual = :stock_actual,
            ubicacion_almacen = :ubicacion_almacen,
            ultima_actualizacion = :ultima_actualizacion
        WHERE id_producto = :id
    ");
    $stmt->execute([
        ":stock_actual" => $data["stock_actual"],
        ":ubicacion_almacen" => $data["ubicacion_almacen"],
        ":ultima_actualizacion" => $data["ultima_actualizacion"],
        ":id" => $idProducto
    ]);
}




    public function resetImagenPrincipal(int $idProducto): void {
        $stmt = $this->pdo->prepare("
            UPDATE producto_imagenes
            SET es_principal = 0
            WHERE id_producto = ?
        ");
        $stmt->execute([$idProducto]);
    }

    public function insertarImagenPrincipal(int $idProducto, string $urlImagen): void {
    $this->assertIdProducto($idProducto);

    $stmt = $this->pdo->prepare("
        INSERT INTO producto_imagenes (id_producto, url_imagen, es_principal)
        VALUES (?, ?, 1)
    ");
    $stmt->execute([$idProducto, $urlImagen]);
}


    public function actualizarProducto(int $idProducto, array $data): void {
    $data[":id_producto"] = $idProducto;

    $stmt = $this->pdo->prepare("
        UPDATE productos SET
            id_categoria = :id_categoria,
            nombre = :nombre,
            descripcion_corta = :descripcion_corta,
            descripcion_larga = :descripcion_larga,
            precio = :precio,
            precio_oferta = :precio_oferta,
            sku = :sku,
            aplica_iva = :aplica_iva,
            stock_minimo = :stock_minimo
        WHERE id_producto = :id_producto
    ");

    $stmt->execute($data);
}


    public function setActivo(int $idProducto, int $activo): void {
        $stmt = $this->pdo->prepare("UPDATE productos SET activo = ? WHERE id_producto = ?");
        $stmt->execute([$activo, $idProducto]);
    }

    // Promociones (tal como está en tu implementación actual)
    public function crearPromocionParaProducto(int $idProducto, string $nombrePromo, float $valorDescuento): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO promociones
                (nombre, tipo_descuento, valor_descuento, activo, fecha_inicio)
            VALUES
                (:nombre, 'porcentaje', :valor, 1, NOW())
        ");
        $stmt->execute([":nombre" => $nombrePromo, ":valor" => $valorDescuento]);
        $idPromocion = (int)$this->pdo->lastInsertId();

        $stmt2 = $this->pdo->prepare("INSERT INTO promocion_productos (id_promocion, id_producto) VALUES (?, ?)");
        $stmt2->execute([$idPromocion, $idProducto]);
    }

    public function desactivarPromocionesDeProducto(int $idProducto): void {
        $stmt = $this->pdo->prepare("
            UPDATE promociones pr
            JOIN promocion_productos pp ON pp.id_promocion = pr.id_promocion
            SET pr.activo = 0
            WHERE pp.id_producto = ?
        ");
        $stmt->execute([$idProducto]);
    }

    public function borrarVinculosPromocionProducto(int $idProducto): void {
        $stmt = $this->pdo->prepare("DELETE FROM promocion_productos WHERE id_producto = ?");
        $stmt->execute([$idProducto]);
    }
    
    
public function insertarInventarioConStock(int $idProducto, int $stock): void
{
    // DEBUG: registrar el id que llega a inventario
    file_put_contents(
        __DIR__ . "/debug_inventario.log",
        date("Y-m-d H:i:s") . " | insertarInventarioConStock | idProducto=" . $idProducto . " | stock=" . $stock . PHP_EOL,
        FILE_APPEND
    );

    if ($idProducto <= 0) {
        throw new Exception("❌ ID de producto inválido para inventario: " . $idProducto);
    }

    $stmt = $this->pdo->prepare("
        INSERT INTO inventario (id_producto, stock_actual, ubicacion_almacen, ultima_actualizacion)
        VALUES (?, ?, 'Bodega principal', NOW())
    ");
    $stmt->execute([$idProducto, $stock]);
}

    // ==========================
    // Colores (variantes)
    // ==========================
    public function listarColoresActivos(): array
    {
        // IMPORTANTE:
        // En algunas exportaciones la tabla `colores` viene como MyISAM (sin FK) y/o con definiciones
        // sin AUTO_INCREMENT. Este método solo lista, así que lo hacemos robusto y sin romper el panel.
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_color, nombre, codigo_hex, activo\n" .
                "FROM `colores`\n" .
                "WHERE activo = 1\n" .
                "ORDER BY nombre ASC"
            );
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return is_array($rows) ? $rows : [];
        } catch (Throwable $e) {
            // Si falla por tabla inexistente o esquema diferente, devolvemos [] para no romper UI.
            return [];
        }
    }

    /** @return int[] */
    public function obtenerIdsColoresDeProducto(int $idProducto): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id_color FROM producto_color WHERE id_producto = ?");
            $stmt->execute([$idProducto]);
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!is_array($ids)) return [];
            return array_values(array_map('intval', $ids));
        } catch (Throwable $e) {
            return [];
        }
    }

    public function reemplazarColoresDeProducto(int $idProducto, array $idsColor): void
    {
        // Si no existen tablas, no debe romper el panel
        try {
            $this->assertIdProducto($idProducto);
            $this->pdo->prepare("DELETE FROM producto_color WHERE id_producto = ?")->execute([$idProducto]);

            $idsColor = array_values(array_unique(array_filter(array_map('intval', $idsColor), fn($v) => $v > 0)));
            if (!count($idsColor)) return;

            $stmt = $this->pdo->prepare("INSERT INTO producto_color (id_producto, id_color) VALUES (?, ?)");
            foreach ($idsColor as $idColor) {
                $stmt->execute([$idProducto, $idColor]);
            }
        } catch (Throwable $e) {
            // ignore
        }
    }

    public function crearColorSiNoExiste(string $nombre, string $hex): ?int
    {
        $nombre = trim($nombre);
        $hex = strtoupper(trim($hex));
        if ($nombre === "") return null;
        if ($hex === "") $hex = "#000000";
        if ($hex[0] !== '#') $hex = '#' . $hex;

        // Validar hex simple #RRGGBB
        if (!preg_match('/^#[0-9A-F]{6}$/', $hex)) {
            // si no es válido, no crear
            return null;
        }

        try {
            // Buscar por nombre (case-insensitive)
            $stmt = $this->pdo->prepare("SELECT id_color FROM `colores` WHERE LOWER(nombre) = LOWER(?) LIMIT 1");
            $stmt->execute([$nombre]);
            $id = $stmt->fetchColumn();
            if ($id) return (int)$id;

            // 1) Intento normal (asume AUTO_INCREMENT)
            try {
                $stmt = $this->pdo->prepare("INSERT INTO `colores` (nombre, codigo_hex, activo) VALUES (?, ?, 1)");
                $stmt->execute([$nombre, $hex]);
                $newId = (int)$this->pdo->lastInsertId();
                if ($newId > 0) return $newId;
            } catch (Throwable $ignored) {
                // 2) Fallback: en dumps donde id_color NO es auto_increment, calculamos MAX(id)+1
            }

            // Fallback seguro
            $maxStmt = $this->pdo->query("SELECT IFNULL(MAX(id_color),0) AS mx FROM `colores`");
            $mxRow = $maxStmt ? $maxStmt->fetch(PDO::FETCH_ASSOC) : null;
            $nextId = (int)($mxRow["mx"] ?? 0) + 1;
            if ($nextId <= 0) return null;

            $stmt2 = $this->pdo->prepare("INSERT INTO `colores` (id_color, nombre, codigo_hex, activo) VALUES (?, ?, ?, 1)");
            $stmt2->execute([$nextId, $nombre, $hex]);
            return $nextId;

        } catch (Throwable $e) {
            return null;
        }
    }

    
    
public function obtenerPromocionPorProducto(int $idProducto): ?array
{
    $sql = "
        SELECT pr.id_promocion
        FROM promociones pr
        INNER JOIN promocion_productos pp
            ON pp.id_promocion = pr.id_promocion
        WHERE pp.id_producto = ?
        LIMIT 1
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$idProducto]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}


public function actualizarPromocionProducto(
    int $idPromocion,
    string $nombrePromo,
    float $descuento
): bool {
    $sql = "
        UPDATE promociones
        SET nombre = ?,
            tipo_descuento = 'porcentaje',
            valor_descuento = ?,
            activo = 1
        WHERE id_promocion = ?
    ";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$nombrePromo, $descuento, $idPromocion]);
}




    public function obtenerImagenesProducto(int $id_producto): array
    {
        $sql = "SELECT url_imagen, es_principal, orden FROM producto_imagenes WHERE id_producto = ? ORDER BY es_principal DESC, orden ASC, id_imagen ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute([$id_producto]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function borrarImagenesProducto(int $id_producto): void
    {
        $sql = "DELETE FROM producto_imagenes WHERE id_producto = ?";
        $st = $this->pdo->prepare($sql);
        $st->execute([$id_producto]);
    }

    public function insertarImagen(int $id_producto, string $url_imagen, int $es_principal = 0, int $orden = 0): void
    {
        $sql = "INSERT INTO producto_imagenes (id_producto, url_imagen, es_principal, orden) VALUES (?, ?, ?, ?)";
        $st = $this->pdo->prepare($sql);
        $st->execute([$id_producto, $url_imagen, $es_principal, $orden]);
    }
}
