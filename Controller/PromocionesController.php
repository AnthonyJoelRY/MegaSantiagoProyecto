<?php

require_once __DIR__ . '/../Model/DAO/PromocionDAO.php';

class PromocionesController {

  public function publicas() {
    $dao = new PromocionDAO();
    $promos = $dao->obtenerPromocionesActivas();

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($promos);
  }
}
