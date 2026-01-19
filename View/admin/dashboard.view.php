<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | MegaSantiago</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #212529 !important;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--bs-info);
        }

        .nav-link {
            padding-left: 1.5rem;
        }

        .card-body h2 {
            font-size: 2.5rem;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            <?php $seccionActiva = "dashboard"; require __DIR__ . "/partials/sidebar.php"; ?>

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
                                <h2 class="fw-bolder text-primary mt-2"><?= $totalProductos ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Usuarios</small>
                                <h2 class="fw-bolder text-success mt-2"><?= $totalUsuarios ?></h2>
                            </div>
                        </div>
                    </div>

                   
                    <div class="col-md-3">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Productos en Oferta</small>
                                <h2 class="fw-bolder text-info mt-2"><?= $productosOferta ?></h2>
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
                                <h2 class="fw-bolder text-success mt-2"><?= $productosActivos ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Sin Stock</small>
                                <h2 class="fw-bolder text-danger mt-2"><?= $sinStock ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Stock Bajo</small>
                                <h2 class="fw-bolder text-warning mt-2"><?= $stockBajo ?></h2>
                            </div>
                        </div>
                    </div>

                </div>
<!-- =========================
     SINCRONIZACIÓN / IMPORT
========================= -->
<h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Sincronización</h4>

<div class="row g-4 mb-5">
  <div class="col-12">
    <div class="card shadow rounded-4 border-0 bg-white">
      <div class="card-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
          <div>
            <h5 class="fw-bold mb-1 text-primary">
              <i class="bi bi-cloud-arrow-down"></i> Importar productos desde BD externa
            </h5>
            <small class="text-muted">
              Ejecuta importación incremental (si no hay cambios, fetched/applied = 0).
            </small>
          </div>

          <div class="d-flex gap-2">
            <button id="btnSyncImport" type="button" class="btn btn-primary">
              <i class="bi bi-play-fill"></i> Ejecutar import
            </button>
            <button id="btnSyncClear" type="button" class="btn btn-outline-secondary">
              <i class="bi bi-trash3"></i> Limpiar
            </button>
          </div>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
          <span id="syncEstado" class="badge bg-secondary px-3 py-2">
            <i class="bi bi-info-circle"></i> Listo
          </span>

          <small class="text-muted text-break">
            Endpoint:
            <span id="syncEndpointText"></span>
          </small>
        </div>

        <pre id="syncResultado"
             class="p-3 rounded-3"
             style="background:#0b1020;color:#e9eefc;white-space:pre-wrap;word-wrap:break-word;min-height:120px;margin:0;">
(aquí aparecerá el JSON)
        </pre>

      </div>
    </div>
  </div>
</div>
<!-- ========================= -->
                <h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Usuarios</h4>
                <div class="row g-4 mb-5">


                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Administradores</small>
                                <h2 class="fw-bolder text-primary mt-2"><?= $admins ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border-0 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Clientes</small>
                                <h2 class="fw-bolder text-secondary mt-2"><?= $clientes ?></h2>
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
                                <h5 class="fw-bolder mt-2 text-info"><?= $ultimoUsuario ?: 'N/A' ?></h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow rounded-4 border border-info border-3 h-100 bg-white">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1 fw-semibold">Último Producto Añadido</small>
                                <h5 class="fw-bolder mt-2 text-info"><?= $ultimoProducto ?: 'N/A' ?></h5>
                            </div>
                        </div>
                    </div>

                </div>

            </main>

        </div>
    </div>

    <script>
      // UI de sincronización (panel admin)
      // ✅ MVC: la vista SOLO llama a un controlador interno (/Controller/AdminSyncController.php)
      // y NO expone tokens ni lógica de negocio.

      function setSyncBadge(type, text, icon) {
        const el = document.getElementById("syncEstado");
        if (!el) return;

        el.className = "badge px-3 py-2";
        if (type === "ok") el.classList.add("bg-success");
        else if (type === "err") el.classList.add("bg-danger");
        else if (type === "run") el.classList.add("bg-primary");
        else el.classList.add("bg-secondary");

        el.innerHTML = `<i class="bi ${icon}"></i> ${text}`;
      }

      function buildAdminSyncUrl() {
        // Endpoint interno: valida sesión admin y ejecuta SyncManager
        return `/Controller/AdminSyncController.php?accion=import&entidad=productos`;
      }

      async function ejecutarImport() {
        const btn = document.getElementById("btnSyncImport");
        const out = document.getElementById("syncResultado");
        const epTxt = document.getElementById("syncEndpointText");

        const url = buildAdminSyncUrl();
        if (epTxt) epTxt.textContent = url;

        if (btn) {
          btn.disabled = true;
          btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Ejecutando...`;
        }
        setSyncBadge("run", "Ejecutando import...", "bi-hourglass-split");
        if (out) out.textContent = "";

        try {
          const r = await fetch(url, { method: "GET", credentials: "same-origin" });
          const json = await r.json();

          if (out) out.textContent = JSON.stringify(json, null, 2);

          if (json.ok) {
            const fetched = json?.result?.import?.fetched ?? 0;
            const applied = json?.result?.import?.applied ?? 0;
            setSyncBadge("ok", `OK (fetched ${fetched}, applied ${applied})`, "bi-check-circle");
          } else {
            setSyncBadge("err", "Error en import", "bi-x-circle");
          }

        } catch (err) {
          setSyncBadge("err", "Error de conexión", "bi-wifi-off");
          if (out) out.textContent = String(err);
        } finally {
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-play-fill"></i> Ejecutar import`;
          }
        }
      }

      function limpiarSalida() {
        const out = document.getElementById("syncResultado");
        if (out) out.textContent = "(aquí aparecerá el JSON)";
        setSyncBadge("idle", "Listo", "bi-info-circle");
      }

      document.addEventListener("DOMContentLoaded", () => {
        const epTxt = document.getElementById("syncEndpointText");
        if (epTxt) epTxt.textContent = buildAdminSyncUrl();

        document.getElementById("btnSyncImport")?.addEventListener("click", ejecutarImport);
        document.getElementById("btnSyncClear")?.addEventListener("click", limpiarSalida);
      });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>