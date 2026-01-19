<?php $seccionActiva = "promociones"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones | MegaSantiago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-fluid">
    <div class="row">

        <?php include __DIR__ . "/../partials/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">

            <!-- TÍTULO + BOTÓN -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary mb-0">📢 Promociones</h2>

                <a href="<?= PROJECT_BASE ?>/panel/promociones/crear"
                   class="btn btn-primary">
                    ➕ Nueva promoción
                </a>
            </div>

            <!-- TABLA -->
            <div class="card shadow-sm rounded-4">
                <div class="card-body">

                    <?php if (empty($promociones)): ?>
                        <div class="alert alert-info mb-0">
                            No hay promociones registradas.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Banner</th>
                                        <th>Inicio</th>
                                        <th>Fin</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($promociones as $p): ?>
<tr>
    <td style="width:160px">
        <?php if ($p->imagen_banner): ?>
            <img src="<?= htmlspecialchars($p->imagen_banner) ?>"
                 class="img-fluid rounded"
                 style="max-height:90px">
        <?php else: ?>
            <span class="text-muted">—</span>
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($p->fecha_inicio) ?></td>

    <td>
        <?php
        if (!$p->fecha_fin || $p->fecha_fin === '0000-00-00') {
            echo '<span class="text-muted">Sin fin</span>';
        } else {
            echo htmlspecialchars($p->fecha_fin);
        }
        ?>
    </td>

    <td>
        <?php if ($p->activo): ?>
            <span class="badge bg-success">Activa</span>
        <?php else: ?>
            <span class="badge bg-secondary">Inactiva</span>
        <?php endif; ?>
    </td>
    
    <td>
    <!-- Activar / Desactivar -->
    <form action="<?= PROJECT_BASE ?>/panel/promociones/acciones"
          method="POST"
          class="d-inline">

        <input type="hidden" name="accion" value="estado">
        <input type="hidden" name="id_promocion" value="<?= $p->id_promocion ?>">

        <?php if ($p->activo): ?>
            <input type="hidden" name="estado" value="0">
            <button class="btn btn-sm btn-outline-danger">
                Desactivar
            </button>
        <?php else: ?>
            <input type="hidden" name="estado" value="1">
            <button class="btn btn-sm btn-outline-success">
                Activar
            </button>
        <?php endif; ?>
    </form>

    <!-- 🔴 ELIMINAR -->
    <form action="<?= PROJECT_BASE ?>/panel/promociones/acciones"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('¿Seguro que deseas eliminar este banner de forma permanente?');">

        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id_promocion" value="<?= $p->id_promocion ?>">

        <button class="btn btn-sm btn-outline-dark">
            Eliminar
        </button>
    </form>
</td>


</tr>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>
