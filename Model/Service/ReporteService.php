<?php
// Model/Service/ReporteService.php

class ReporteService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // 💰 Total ventas (solo pagados)
    public function ventasTotales(): float
    {
        $stmt = $this->pdo->query("
            SELECT COALESCE(SUM(total_pagar), 0)
            FROM pedidos
            WHERE estado = 'pagado'
        ");
        return (float)$stmt->fetchColumn();
    }

    // 📦 Total pedidos pagados
    public function totalPedidos(): int
    {
        $stmt = $this->pdo->query("
            SELECT COALESCE(COUNT(*), 0)
            FROM pedidos
            WHERE estado = 'pagado'
        ");
        return (int)$stmt->fetchColumn();
    }

    // 👤 Total clientes (ajusta id_rol si en tu BD es otro)
    public function totalClientes(): int
    {
        $stmt = $this->pdo->query("
            SELECT COALESCE(COUNT(*), 0)
            FROM usuarios
            WHERE id_rol = 3
        ");
        return (int)$stmt->fetchColumn();
    }

    // 📊 Promedio por pedido pagado
    public function promedioPorPedido(): float
    {
        $stmt = $this->pdo->query("
            SELECT COALESCE(AVG(total_pagar), 0)
            FROM pedidos
            WHERE estado = 'pagado'
        ");
        return round((float)$stmt->fetchColumn(), 2);
    }

    // 💵 Total IVA (solo pagados)
    public function totalIVA(): float
    {
        $stmt = $this->pdo->query("
            SELECT COALESCE(SUM(total_iva), 0)
            FROM pedidos
            WHERE estado = 'pagado'
        ");
        return round((float)$stmt->fetchColumn(), 2);
    }

    // 📅 Ventas por día
    public function ventasPorDia(): array
    {
        $stmt = $this->pdo->query("
            SELECT DATE(fecha_pedido) AS fecha,
                   SUM(total_pagar) AS total
            FROM pedidos
            WHERE estado = 'pagado'
            GROUP BY DATE(fecha_pedido)
            ORDER BY fecha ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📆 Ventas por mes
    public function ventasPorMes(): array
    {
        $stmt = $this->pdo->query("
            SELECT DATE_FORMAT(fecha_pedido, '%Y-%m') AS mes,
                   SUM(total_pagar) AS total
            FROM pedidos
            WHERE estado = 'pagado'
            GROUP BY mes
            ORDER BY mes ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
   PRODUCTOS MÁS VENDIDOS
============================ */
    public function productosMasVendidos(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            pr.id_producto,
            pr.nombre,
            SUM(pd.cantidad) AS total_vendido
        FROM pedido_detalle pd
        JOIN pedidos p ON p.id_pedido = pd.id_pedido
        JOIN productos pr ON pr.id_producto = pd.id_producto
        WHERE p.estado = 'pagado'
        GROUP BY pr.id_producto, pr.nombre
        ORDER BY total_vendido DESC
        LIMIT ?
    ");

        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /* ============================
PRODUCTOS MENOS VENDIDOS
============================ */
    public function productosMenosVendidos(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            pr.id_producto,
            pr.nombre,
            SUM(pd.cantidad) AS total_vendido
        FROM pedido_detalle pd
        JOIN productos pr ON pr.id_producto = pd.id_producto
        JOIN pedidos pe ON pe.id_pedido = pd.id_pedido
        WHERE pe.estado = 'pagado'
        GROUP BY pr.id_producto, pr.nombre
        ORDER BY total_vendido ASC
        LIMIT :limit
    ");

        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /* ============================
   👥 CLIENTES CON MÁS COMPRAS
============================ */
    public function clientesTop(): array
    {
        $stmt = $this->pdo->query("
        SELECT 
            u.email AS cliente,
            COUNT(p.id_pedido) AS total_pedidos,
            SUM(p.total_pagar) AS total_gastado
        FROM pedidos p
        JOIN usuarios u ON u.id_usuario = p.id_usuario
        WHERE p.estado = 'pagado'
        GROUP BY p.id_usuario
        ORDER BY total_pedidos DESC
        LIMIT 5
    ");

        return $stmt->fetchAll();
    }
    
    public function productosSinStock(): array
{
    $stmt = $this->pdo->query("
        SELECT 
            p.id_producto,
            p.nombre,
            i.stock_actual
        FROM productos p
        LEFT JOIN inventario i ON i.id_producto = p.id_producto
        WHERE p.activo = 1
          AND COALESCE(i.stock_actual, 0) = 0
        ORDER BY p.nombre
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function productosStockBajo(): array
{
    // ✅ Reporte por SUCURSAL (inventario_sucursal)
    // - Un producto puede estar bajo de stock en una sucursal y normal en otra.
    // - No rompe tu UI: simplemente devuelve más columnas.

    $sql = "
        SELECT
            s.id_sucursal,
            s.nombre        AS sucursal,
            p.id_producto,
            p.nombre,
            i.stock_actual,
            i.stock_minimo
        FROM inventario_sucursal i
        JOIN sucursales s ON s.id_sucursal = i.id_sucursal
        JOIN productos  p ON p.id_producto = i.id_producto
        WHERE p.activo = 1
          AND COALESCE(s.activo, 1) = 1
          AND COALESCE(i.stock_actual, 0) > 0
          AND COALESCE(i.stock_actual, 0) <= COALESCE(i.stock_minimo, 0)
        ORDER BY s.nombre ASC, i.stock_actual ASC, p.nombre ASC
    ";

    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    // ✅ TOP productos más vendidos pero con datos para el HOME (id, nombre, precio, imagen, etc.)
public function productosMasVendidosPublicos(int $limit = 4): array
{
    $limit = max(1, (int)$limit);

    $sql = "
        SELECT 
            pr.id_producto AS id,
            pr.nombre,
            pr.descripcion_corta,
            pr.precio,
            pr.precio_oferta,
            c.slug AS categoria,
            (
                SELECT url_imagen
                FROM producto_imagenes i
                WHERE i.id_producto = pr.id_producto
                ORDER BY i.es_principal DESC, i.orden ASC, i.id_imagen ASC
                LIMIT 1
            ) AS imagen,
            SUM(pd.cantidad) AS total_vendido
        FROM pedido_detalle pd
        JOIN pedidos p      ON p.id_pedido = pd.id_pedido
        JOIN productos pr   ON pr.id_producto = pd.id_producto
        JOIN categorias c   ON c.id_categoria = pr.id_categoria
        WHERE p.estado = 'pagado'
          AND pr.activo = 1
        GROUP BY pr.id_producto, pr.nombre, pr.descripcion_corta, pr.precio, pr.precio_oferta, c.slug
        ORDER BY total_vendido DESC
        LIMIT ?
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
