<?php
session_start();
$seccionActiva = 'usuarios';

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== 1) {
    header("Location: /MegaSantiagoFront/index.html");
    exit;
}

require_once __DIR__ . "/../../Model/db.php";
$pdo = obtenerConexion();

/* VALIDAR ID */
$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

/* USUARIO */
$stmt = $pdo->prepare("
    SELECT id_usuario, nombre, apellido, email, telefono, id_rol, activo
    FROM usuarios
    WHERE id_usuario = ?
");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: index.php");
    exit;
}

/* ROLES */
$roles = $pdo->query("SELECT id_rol, nombre FROM roles")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario | MegaSantiago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-fluid">
<div class="row">

<?php include __DIR__ . "/../PLANTILLAS/sidebar.php"; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
    <h2 class="fw-bold text-primary mb-0">✏️ Editar Usuario</h2>
    <a href="index.php" class="btn btn-outline-secondary">Volver</a>
</div>

<form action="acciones.php" method="POST"
      class="card shadow-sm rounded-4 border-0 bg-white p-4">

    <input type="hidden" name="accion" value="editar">
    <input type="hidden" name="id_usuario" value="<?= $usuario["id_usuario"] ?>">

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($usuario["nombre"]) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Apellido</label>
            <input type="text" name="apellido" class="form-control"
                   value="<?= htmlspecialchars($usuario["apellido"]) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($usuario["email"]) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Teléfono</label>
            <input type="text" name="telefono" class="form-control"
                   value="<?= htmlspecialchars($usuario["telefono"]) ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Rol</label>
            <select name="id_rol" class="form-select">
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r["id_rol"] ?>"
                        <?= $r["id_rol"] == $usuario["id_rol"] ? "selected" : "" ?>>
                        <?= $r["nombre"] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6 mb-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="activo"
                       <?= $usuario["activo"] ? "checked" : "" ?>>
                <label class="form-check-label fw-semibold">Usuario activo</label>
            </div>
        </div>

    </div>

    <button class="btn btn-primary fw-semibold">
        Guardar cambios
    </button>

</form>

</main>
</div>
</div>
</body>
</html>
