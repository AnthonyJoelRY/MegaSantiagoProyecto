<?php
// Model/DAO/PromocionDAO.php

require_once __DIR__ . '/../DB/db.php';

class PromocionDAO {

  public function obtenerPromocionesActivas() {
    $db = obtenerConexion();

    $sql = "
      SELECT imagen_banner
      FROM promociones
      WHERE activo = 1
        AND imagen_banner IS NOT NULL
        AND fecha_inicio <= CURDATE()
        AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
      ORDER BY id_promocion DESC
    ";

    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
}
