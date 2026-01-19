<?php
// Model/Service/Admin/AdminProductoService.php

require_once __DIR__ . "/../../DAO/Admin/AdminProductoDAO.php";
require_once __DIR__ . "/../../Entity/Producto.php";

class AdminProductoService {

    private $pdo;
    private $dao;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->dao = new AdminProductoDAO($pdo);
    }

    public function listar($q = "") {
        $rows = $this->dao->listar($q);
        $resultado = [];

        foreach ($rows as $r) {
            if (is_array($r)) {
                $resultado[] = Producto::fromRow($r)->toArray();
            }
        }

        return $resultado;
    }

    public function categorias() {
        return $this->dao->categoriasActivas();
    }

    public function colores(): array {
        return $this->dao->listarColoresActivos();
    }

    /** @return int[] */
    public function coloresDelProducto(int $idProducto): array {
        return $this->dao->obtenerIdsColoresDeProducto($idProducto);
    }

    public function obtener($id) {
        $id = (int)$id;
        $prod = $this->dao->obtenerPorId($id);
        if ($prod) {
            $prod['imagenes'] = $this->dao->obtenerImagenesProducto($id);
        }
        return $prod;
    }

    public function crear($post) {

        $idCategoria = (int)($post["id_categoria"] ?? 0);
        $nombre      = trim($post["nombre"] ?? "");
        $descCorta   = trim($post["descripcion_corta"] ?? "");
        $descLarga   = trim($post["descripcion_larga"] ?? "");
        $precio      = (float)($post["precio"] ?? 0);
        $sku         = trim($post["sku"] ?? "");
        $aplicaIva   = isset($post["aplica_iva"]) ? 1 : 0;
        $imagen      = trim($post["imagen"] ?? "");
        $stock = (int)($post["stock"] ?? 0);
        $stockMinimo = (int)($post["stock_minimo"] ?? 0);

if ($stockMinimo < 0) {
  throw new Exception("Stock mínimo inválido.");
}


        $precioOferta = null;
        if (isset($post["precio_oferta"]) && $post["precio_oferta"] !== "") {
            $precioOferta = (float)$post["precio_oferta"];
            if ($precioOferta < 1 || $precioOferta > 90) {
                throw new Exception("El descuento debe estar entre 1% y 90%");
            }
        }

        if ($idCategoria <= 0 || $nombre === "" || $precio <= 0 || $sku === "") {
            throw new Exception("Faltan datos obligatorios del producto.");
        }

        $this->pdo->beginTransaction();

        try {
            $idProducto = $this->dao->insertarProducto([
    "id_categoria"        => $idCategoria,
    "nombre"              => $nombre,
    "descripcion_corta"   => $descCorta,
    "descripcion_larga"   => $descLarga,
    "precio"              => $precio,
    "precio_oferta"       => $precioOferta,
    "sku"                 => $sku,
    "aplica_iva"          => $aplicaIva,
    "stock_minimo"        => $stockMinimo,
]);


            if ((int)$idProducto <= 0) {
                throw new Exception("No se pudo obtener el ID del producto.");
            }
            
            

            $this->dao->insertarInventarioConStock($idProducto, $stock);

            // ✅ Colores seleccionados / nuevo color (no rompe si tablas no existen)
            $idsColores = [];
            if (isset($post["colores"])) {
                $idsColores = is_array($post["colores"]) ? $post["colores"] : [$post["colores"]];
            }

            $nuevoNombre = trim($post["nuevo_color_nombre"] ?? "");
            $nuevoHex    = trim($post["nuevo_color_hex"] ?? "");
            if ($nuevoNombre !== "") {
                $idNuevo = $this->dao->crearColorSiNoExiste($nuevoNombre, $nuevoHex);
                if ($idNuevo) $idsColores[] = $idNuevo;
            }
            $this->dao->reemplazarColoresDeProducto($idProducto, $idsColores);
            // Imagenes (principal + adicionales)
            $imagenesJson = trim($post["imagenes_json"] ?? "");
            if ($imagenesJson !== "") {
                $arr = json_decode($imagenesJson, true);
                if (is_array($arr) && count($arr) > 0) {
                    $tienePrincipal = false;
                    foreach ($arr as $it) {
                        if (!empty($it["principal"])) { $tienePrincipal = true; break; }
                    }
                    $orden = 0;
                    foreach ($arr as $idx => $it) {
                        $url = trim($it["url"] ?? "");
                        if ($url === "") continue;
                        $esPrincipal = !empty($it["principal"]) || (!$tienePrincipal && $idx === 0);
                        if ($esPrincipal) {
                            $this->dao->insertarImagen($idProducto, $url, 1, 0);
                        } else {
                            $orden++;
                            $this->dao->insertarImagen($idProducto, $url, 0, $orden);
                        }
                    }
                }
            } elseif ($imagen !== "") {
                // Compatibilidad: si solo enviaron imagen principal
                $this->dao->insertarImagenPrincipal($idProducto, $imagen);
            }

            if ($precioOferta !== null) {
                $this->dao->crearPromocionParaProducto(
                    $idProducto,
                    "Promo - " . $nombre,
                    $precioOferta
                );
            }

            $this->pdo->commit();
            return $idProducto;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    public function editar($idProducto, $post) {
    $idProducto = (int)$idProducto;
    if ($idProducto <= 0) throw new Exception("ID inválido.");

    $idCategoria  = (int)($post["id_categoria"] ?? 0);
    $nombre       = trim($post["nombre"] ?? "");
    $descCorta    = trim($post["descripcion_corta"] ?? "");
    $descLarga    = trim($post["descripcion_larga"] ?? "");
    $precio       = (float)($post["precio"] ?? 0);
    $sku          = trim($post["sku"] ?? "");
    $aplicaIva    = isset($post["aplica_iva"]) ? 1 : 0;
    $imagen       = trim($post["imagen"] ?? "");

    $stock        = (int)($post["stock"] ?? 0);
    $stockMinimo  = (int)($post["stock_minimo"] ?? 0);

    if ($stockMinimo < 0) throw new Exception("Stock mínimo inválido.");
    if ($idCategoria <= 0 || $nombre === "" || $precio <= 0 || $sku === "") {
        throw new Exception("Faltan datos obligatorios del producto.");
    }

    $precioOferta = null;
    if (isset($post["precio_oferta"]) && $post["precio_oferta"] !== "") {
        $precioOferta = (float)$post["precio_oferta"];
        if ($precioOferta < 1 || $precioOferta > 90) {
            throw new Exception("El descuento debe estar entre 1% y 90%");
        }
    }

    $this->pdo->beginTransaction();
    try {
        // 1) Actualiza producto (incluyendo stock_minimo)
        $this->dao->actualizarProducto($idProducto, [
            ":id_categoria" => $idCategoria,
            ":nombre" => $nombre,
            ":descripcion_corta" => $descCorta,
            ":descripcion_larga" => $descLarga,
            ":precio" => $precio,
            ":precio_oferta" => $precioOferta,
            ":sku" => $sku,
            ":aplica_iva" => $aplicaIva,
            ":stock_minimo" => $stockMinimo,
        ]);

        // 2) Actualiza inventario
        $this->dao->actualizarInventario($idProducto, [
            "stock_actual" => $stock,
            "ubicacion_almacen" => "Bodega principal",
            "ultima_actualizacion" => date("Y-m-d H:i:s"),
        ]);

        // 3) Imagenes (principal + adicionales)
        $imagenesJson = trim($post["imagenes_json"] ?? "");
        if ($imagenesJson !== "") {
            $arr = json_decode($imagenesJson, true);
            if (is_array($arr) && count($arr) > 0) {
                $this->dao->borrarImagenesProducto($idProducto);
                $tienePrincipal = false;
                foreach ($arr as $it) {
                    if (!empty($it["principal"])) { $tienePrincipal = true; break; }
                }
                $orden = 0;
                foreach ($arr as $idx => $it) {
                    $url = trim($it["url"] ?? "");
                    if ($url === "") continue;
                    $esPrincipal = !empty($it["principal"]) || (!$tienePrincipal && $idx === 0);
                    if ($esPrincipal) {
                        $this->dao->insertarImagen($idProducto, $url, 1, 0);
                    } else {
                        $orden++;
                        $this->dao->insertarImagen($idProducto, $url, 0, $orden);
                    }
                }
            }
        } elseif ($imagen !== "") {
            // Compatibilidad: si solo mandan imagen principal
            $this->dao->resetImagenPrincipal($idProducto);
            $this->dao->insertarImagenPrincipal($idProducto, $imagen);
        }

        
        
// 4) Promos (CORRECTO Y FINAL)
if ($precioOferta !== null) {

    $promoExistente = $this->dao->obtenerPromocionPorProducto($idProducto);

    if ($promoExistente) {
        // UPDATE si ya existe promo
        $this->dao->actualizarPromocionProducto(
            (int)$promoExistente["id_promocion"],
            "Promo - " . $nombre,
            $precioOferta
        );
    } else {
        // INSERT solo si NO existe
        $this->dao->crearPromocionParaProducto(
            $idProducto,
            "Promo - " . $nombre,
            $precioOferta
        );
    }

} else {
    // Si ya no hay descuento, desactivar promo
    $this->dao->desactivarPromocionesDeProducto($idProducto);
}


        // 5) Colores (permitir varios) + opcional nuevo color
        $idsColores = [];
        if (isset($post["colores"])) {
            $idsColores = is_array($post["colores"]) ? $post["colores"] : [$post["colores"]];
        }
        $nuevoNombre = trim($post["nuevo_color_nombre"] ?? "");
        $nuevoHex    = trim($post["nuevo_color_hex"] ?? "");
        if ($nuevoNombre !== "") {
            $idNuevo = $this->dao->crearColorSiNoExiste($nuevoNombre, $nuevoHex);
            if ($idNuevo) $idsColores[] = $idNuevo;
        }
        $this->dao->reemplazarColoresDeProducto($idProducto, $idsColores);

        $this->pdo->commit();
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}


    public function desactivar($idProducto) {
        $this->dao->setActivo((int)$idProducto, 0);
    }

    public function activar($idProducto) {
        $this->dao->setActivo((int)$idProducto, 1);
    }
}
