<?php

class PromocionesController
{
public function index()
{
    require_once __DIR__ . '/../../Model/DAO/Admin/PromocionDAO.php';

    $dao = new PromocionDAO();
    $promociones = $dao->listarBanners();


    require __DIR__ . '/../../View/admin/promociones/index.php';
}

    public function crear()
    {
        // Formulario de creación
        require __DIR__ . '/../../View/admin/promociones/crear.php';
    }

public function acciones()
{
    if (!isset($_POST['accion'])) {
        header('Location: ' . PROJECT_BASE . '/panel/promociones');
        exit;
    }

    require_once __DIR__ . '/../../Model/Entity/Promocion.php';
    require_once __DIR__ . '/../../Model/DAO/Admin/PromocionDAO.php';

    $dao = new PromocionDAO();

    switch ($_POST['accion']) {

        // =========================
        // CREAR BANNER
        // =========================
        case 'crear':

            $promo = new Promocion();
            $promo->nombre = 'Banner ' . date('Y-m-d H:i');
            $promo->descripcion = '';
            $promo->imagen_banner = $_POST['imagen_banner'];
            $promo->fecha_inicio = $_POST['fecha_inicio'];
            $promo->fecha_fin = $_POST['fecha_fin'] ?? '';
            $promo->tipo_descuento = '';
            $promo->valor_descuento = 0.0;
            $promo->activo = 1;

            $dao->crear($promo);
            break;

        // =========================
        // ACTIVAR / DESACTIVAR
        // =========================
        case 'estado':

            $dao->cambiarEstado(
                (int)$_POST['id_promocion'],
                (int)$_POST['estado']
            );
            break;
            
            case 'eliminar':
    if (isset($_POST['id_promocion'])) {
        $dao->eliminar((int) $_POST['id_promocion']);
    }
    break;

    }

    // Volver siempre al listado
    header('Location: ' . PROJECT_BASE . '/panel/promociones');
    exit;
}

}
