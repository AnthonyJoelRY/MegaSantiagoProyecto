<?php
// Model/Service/ProductoService.php

require_once __DIR__ . "/../DAO/ProductoDAO.php";
require_once __DIR__ . "/../DAO/BusquedaDAO.php";

class ProductoService
{
    private ProductoDAO $productoDAO;
    private BusquedaDAO $busquedaDAO;

    public function __construct(private PDO $pdo)
    {
        $this->productoDAO = new ProductoDAO($pdo);
        $this->busquedaDAO = new BusquedaDAO($pdo);
    }

    public function listarPorCategoria(string $categoria): array
    {
        if ($categoria === "") {
            return ["error" => "Falta parámetro categoria"];
        }
        return $this->productoDAO->listarPorCategoria($categoria);
    }

    public function buscar(string $termino, ?int $idUsuario): array
    {
        if ($termino === "") {
            return ["error" => "Falta parámetro q"];
        }

        $productos = $this->productoDAO->buscarPorNombre($termino);

        // registra historial (no rompe si idUsuario viene null)
        $this->busquedaDAO->registrar($termino, $idUsuario, count($productos));

        return $productos;
    }

    public function masVendidos(int $limit = 4): array
    {
        if ($limit <= 0) $limit = 4;
        return $this->productoDAO->masVendidos($limit);
    }

    public function ofertas(int $limit = 6): array
    {
        if ($limit <= 0) $limit = 6;
        return $this->productoDAO->ofertas($limit);
    }

    public function promociones(int $limit = 3): array
    {
        if ($limit <= 0) $limit = 3;
        return $this->productoDAO->promociones($limit);
    }
}
