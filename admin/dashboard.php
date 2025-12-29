<?php
session_start();

//Esto es el nivel de seguridad para que solo administrador pueda ver el dashboard
// Si no hay sesión o no es administrador → fuera
if (
    !isset($_SESSION["usuario"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== 1
) {
    header("Location: /MegaSantiagoFront/index.html");
    exit;
}
?>



<?php
require_once __DIR__ . "/../Model/db.php";

$pdo = obtenerConexion();

/* ===============================
   FILA 1 – RESUMEN GENERAL
================================ */

// Total productos
$totalProductos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();

// Total usuarios
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();


// Productos con descuento / ofertas
$productosOferta = 0;
try {
    $productosOferta = $pdo->query("
        SELECT COUNT(*) 
        FROM productos 
        WHERE descuento > 0
    ")->fetchColumn();
} catch (Exception $e) {
    $productosOferta = 0;
}


/* ===============================
   FILA 2 – CATÁLOGO
================================ */

// Productos activos
$productosActivos = $pdo->query("
    SELECT COUNT(*) FROM productos WHERE activo = 1
")->fetchColumn();

// Productos sin stock
$sinStock = $pdo->query("
    SELECT COUNT(*) FROM inventario WHERE stock_actual <= 0
")->fetchColumn();

// Productos con stock bajo (≤ 5)
$stockBajo = $pdo->query("
    SELECT COUNT(*) FROM inventario WHERE stock_actual <= 5
")->fetchColumn();


/* ===============================
   FILA 3 – USUARIOS
================================ */

// Administradores
$admins = $pdo->query("
    SELECT COUNT(*) FROM usuarios WHERE id_rol = 1
")->fetchColumn();

// Clientes
$clientes = $pdo->query("
    SELECT COUNT(*) FROM usuarios WHERE id_rol = 3
")->fetchColumn();


/* ===============================
   FILA 4 – ALERTAS
================================ */

// Último usuario registrado
$ultimoUsuario = $pdo->query("
    SELECT email 
    FROM usuarios 
    ORDER BY fecha_registro DESC 
    LIMIT 1
")->fetchColumn();

// Último producto agregado
$ultimoProducto = $pdo->query("
    SELECT nombre 
    FROM productos 
    ORDER BY id_producto DESC 
    LIMIT 1
")->fetchColumn();

?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | MegaSantiago</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos personalizados muy sencillos para la barra lateral y asegurar que el contenido se vea */
        .sidebar {
            /* Asegura que el color de fondo de la barra lateral sea consistente */
            background-color: #212529 !important;
            /* Usando bg-dark */
        }

        .nav-link.active {
            /* Estilo para el enlace activo */
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--bs-info);
            /* Línea de color para indicar activo */
        }

        .nav-link {
            padding-left: 1.5rem;
            /* Pequeño ajuste para el padding de los enlaces */
        }

        .card-body h2 {
            font-size: 2.5rem;
            /* Ajuste para las métricas clave */
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse min-vh-100 p-0 shadow-lg">
                <div class="position-sticky pt-4">
                    <div class="d-flex align-items-center justify-content-center mb-4 pb-2 border-bottom border-light opacity-50 mx-3">
                        <h4 class="text-white fw-bolder my-0">
                            MegaSantiago
                        </h4>
                    </div>
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link text-white active rounded-2"
                                href="/MegaSantiagoFront/admin/dashboard.php">
                                🏠 Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-white rounded-2"
                                href="/MegaSantiagoFront/admin/productos/">
                                📦 Productos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-white rounded-2"
                                href="/MegaSantiagoFront/admin/usuarios/">
                                👥 Usuarios
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-white rounded-2"
                                href="/MegaSantiagoFront/admin/pedidos/">
                                🛒 Pedidos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-white rounded-2"
                                href="/MegaSantiagoFront/admin/reportes/">
                                📈 Reportes
                            </a>
                        </li>
                    </ul>


                    <div class="px-3 mt-5">
                        <a class="nav-link text-white bg-danger bg-opacity-75 hover-bg-danger rounded-3 p-2 text-center fw-semibold" href="/MegaSantiagoFront/index.html">
                            <span class="me-2">↩️</span> Volver al sitio
                        </a>
                    </div>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">

                <div class="d-flex justify-content-between align-items-center mb-5 bg-white p-3 rounded-4 shadow-sm border-bottom border-primary border-3">
                    <h1 class="h2 text-primary fw-bold mb-0">Panel de Administración</h1>
                    <small class="text-muted">Bienvenido, Admin</small>
                </div>


                <h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Resumen General</h4>
                <div class="row g-4 mb-5">

                    <div class="col-md-2">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Productos</small>
                                <h2 class="fw-bolder text-primary mt-2">
                                    <?= $totalProductos ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Usuarios</small>
                                <h2 class="fw-bolder text-success mt-2">
                                    <?= $totalUsuarios ?>
                                </h2>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Productos en Oferta</small>
                                <h2 class="fw-bolder text-info mt-2">
                                    <?= $productosOferta ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                </div>


                <h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Catálogo</h4>
                <div class="row g-4 mb-5">

                    <div class="col-md-4">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Productos Activos</small>
                                <h2 class="fw-bolder text-success mt-2">
                                    <?= $productosActivos ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Sin Stock</small>
                                <h2 class="fw-bolder text-danger mt-2">
                                    <?= $sinStock ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Stock Bajo</small>
                                <h2 class="fw-bolder text-warning mt-2">
                                    <?= $stockBajo ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                </div>


                <h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Usuarios</h4>
                <div class="row g-4 mb-5">

                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Administradores</small>
                                <h2 class="fw-bolder text-primary mt-2">
                                    <?= $admins ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Clientes</small>
                                <h2 class="fw-bolder text-secondary mt-2">
                                    <?= $clientes ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                </div>



                <h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Alertas</h4>
                <div class="row g-4 mb-4">

                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border border-info border-3 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Último Usuario Registrado</small>
                                <h5 class="fw-bolder mt-2 text-info">
                                    <?= $ultimoUsuario ?: 'N/A' ?>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border border-info border-3 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Último Producto Añadido</small>
                                <h5 class="fw-bolder mt-2 text-info">
                                    <?= $ultimoProducto ?: 'N/A' ?>
                                </h5>
                            </div>
                        </div>
                    </div>

                </div>


            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>