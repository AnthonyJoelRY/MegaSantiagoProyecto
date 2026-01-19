<?php $seccionActiva = "productos"; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto | MegaSantiago</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            <!-- SIDEBAR -->
            <?php include __DIR__ . "/../partials/sidebar.php"; ?>

            <!-- CONTENIDO -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">

                <!-- CABECERA -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <h2 class="fw-bold text-primary mb-0">✏️ Editar Producto</h2>
                    <a href="<?= PROJECT_BASE ?>/panel/productos" class="btn btn-outline-secondary">Volver</a>
                </div>

                <!-- FORMULARIO -->
                <div class="card shadow-sm rounded-4 border-0 bg-white p-4">

                    <form action="<?= PROJECT_BASE ?>/panel/productos/acciones" method="POST">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_producto" value="<?= $producto["id_producto"] ?>">

                        <div class="row">

                            <!-- NOMBRE -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text"
                                    name="nombre"
                                    class="form-control"
                                    value="<?= htmlspecialchars($producto["nombre"]) ?>"
                                    required>
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text"
                                    name="sku"
                                    class="form-control"
                                    value="<?= htmlspecialchars($producto["sku"]) ?>"
                                    required>
                            </div>

                            <!-- Imagenes -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Imagenes del producto</label>

                                <input type="file" id="fileImagenes" accept="image/*" class="form-control" multiple>

                                <!-- compat: imagen principal -->
                                <input type="hidden" name="imagen" id="imagenUrl" value="<?= htmlspecialchars($producto["url_imagen"] ?? "") ?>">
                                <input type="hidden" name="imagenes_json" id="imagenesJson" value="">

                                <div id="previewImgs" class="d-flex flex-wrap gap-2 mt-3"></div>

                                <small class="text-muted d-block mt-1" id="estadoUpload">
                                    Puedes agregar varias imagenes. La principal sera la primera o la que marques como principal.
                                </small>
                            </div>



                            <!-- CATEGORÍA -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Categoría</label>
                                <select name="id_categoria" class="form-select" required>
                                    <option value="">Seleccione una categoría</option>

                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= $c["id_categoria"] ?>"
                                            <?= $c["id_categoria"] == $producto["id_categoria"] ? "selected" : "" ?>>
                                            <?= htmlspecialchars($c["nombre"]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- PRECIO -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Precio</label>
                                <input type="number"
                                    step="0.01"
                                    name="precio"
                                    class="form-control"
                                    value="<?= $producto["precio"] ?>"
                                    required>
                            </div>

                            <!-- PRECIO OFERTA -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Descuento (%)</label>
                                <input type="number"
                                    step="0.01"
                                    name="precio_oferta"
                                    class="form-control"
                                    value="<?= $producto["precio_oferta"] ?>">
                            </div>

                            <!-- IVA -->
                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="aplica_iva"
                                        id="aplicaIva"
                                        <?= $producto["aplica_iva"] ? "checked" : "" ?>>
                                    <label class="form-check-label fw-semibold" for="aplicaIva">
                                        Aplica IVA
                                    </label>
                                </div>
                            </div>
                            
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Stock actual</label>
        <input
            type="number"
            name="stock"
            class="form-control"
            min="0"
            value="<?= (int)($producto["stock_actual"] ?? 0) ?>"
            required
        >
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Stock mínimo</label>
        <input
            type="number"
            name="stock_minimo"
            class="form-control"
            min="0"
            value="<?= (int)($producto["stock_minimo"] ?? 0) ?>"
            required
        >
    </div>
</div>


                            <!-- ✅ COLORES (un producto puede tener varios) -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Colores del producto (opcional)</label>
                                <div class="row g-2">
                                    <?php if (!empty($colores)): ?>
                                        <?php foreach ($colores as $c): ?>
                                            <?php $cid = (int)$c['id_color']; ?>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <label class="d-flex align-items-center gap-2 border rounded-3 p-2" style="cursor:pointer;">
                                                    <input type="checkbox" name="colores[]" value="<?= $cid ?>" <?= (!empty($coloresProducto) && in_array($cid, $coloresProducto)) ? 'checked' : '' ?>>
                                                    <span style="width:18px;height:18px;border-radius:999px;border:1px solid #ddd;display:inline-block;background:<?= htmlspecialchars($c['codigo_hex']) ?>;"></span>
                                                    <span><?= htmlspecialchars($c['nombre']) ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="alert alert-secondary mb-0">Aún no hay colores registrados. Puedes crear uno abajo.</div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-3 p-3 border rounded-3 bg-white">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Nuevo color (nombre)</label>
                                            <input type="text" name="nuevo_color_nombre" class="form-control" placeholder="Ej: Azul">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Código HEX</label>
                                            <input type="text" name="nuevo_color_hex" class="form-control" placeholder="#0000FF">
                                        </div>
                                    </div>
                                    <small class="text-muted">Si escribes un nuevo color, se creará (si no existe) y se asignará al producto.</small>
                                </div>
                            </div>



                            <!-- DESCRIPCIÓN CORTA -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Descripción corta</label>
                                <input type="text"
                                    name="descripcion_corta"
                                    class="form-control"
                                    value="<?= htmlspecialchars($producto["descripcion_corta"]) ?>"
                                    required>
                            </div>

                            <!-- DESCRIPCIÓN LARGA -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold">Descripción larga</label>
                                <textarea name="descripcion_larga"
                                    class="form-control"
                                    rows="4"><?= htmlspecialchars($producto["descripcion_larga"]) ?></textarea>
                            </div>
                        </div>

                        <!-- BOTÓN -->
                        <button type="submit" id="btnGuardar" class="btn btn-primary fw-semibold">
                            Guardar cambios
                        </button>
                    </form>

                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
window.__PRODUCT_IMAGES__ = <?php echo json_encode($producto['imagenes'] ?? []); ?>;
</script>
<script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.14.1/firebase-app.js";
        import {
            getStorage,
            ref,
            uploadBytes,
            getDownloadURL
        } from "https://www.gstatic.com/firebasejs/10.14.1/firebase-storage.js";

        const firebaseConfig = {
            apiKey: "AIzaSyDkCYDW61i0q186DDEJ-bxAvjp0iAW4-Xc",
            authDomain: "megasantiago.firebaseapp.com",
            projectId: "megasantiago",
            storageBucket: "megasantiago.firebasestorage.app",
            messagingSenderId: "68954725464",
            appId: "1:68954725464:web:7e93567bcc98b4b3d83e2d",
            measurementId: "G-8VB7YCPJW1"
        };

        const app = initializeApp(firebaseConfig);
        const storage = getStorage(app);

        const fileInput = document.getElementById("fileImagenes");
        const estado = document.getElementById("estadoUpload");
        const imagenUrl = document.getElementById("imagenUrl");
        const imagenesJson = document.getElementById("imagenesJson");
        const preview = document.getElementById("previewImgs");
        const btnGuardar = document.getElementById("btnGuardar");

        // Normaliza URLs para que funcionen dentro de /panel/... (evita que el navegador busque /panel/.../uploads/...)
        const normalizeUrl = (u) => {
            const s = String(u || "").trim();
            if (!s) return "";
            if (s.startsWith("http://") || s.startsWith("https://")) return s;
            return s.startsWith("/") ? s : `/${s}`;
        };

        // Estructura unificada para el frontend: [{ url: string, principal: bool }]
        let images = Array.isArray(window.__PRODUCT_IMAGES__) ? window.__PRODUCT_IMAGES__.slice() : [];
        images = images
            .map((i) => {
                // soporta formatos: {url, principal} | {url_imagen, es_principal}
                const rawUrl = (i && typeof i === "object")
                    ? (i.url ?? i.url_imagen ?? i.imagen ?? i.path ?? "")
                    : i;
                const principal = (i && typeof i === "object")
                    ? !!(i.principal ?? i.es_principal ?? i.is_principal)
                    : false;
                return { url: normalizeUrl(rawUrl), principal };
            })
            .filter((i) => !!i.url);

        function normalize() {
            // ensure exactly one principal
            if (!images.some(i => i.principal)) {
                if (images[0]) images[0].principal = true;
            } else {
                let seen = false;
                images.forEach(i => {
                    if (i.principal) {
                        if (!seen) seen = true;
                        else i.principal = false;
                    }
                });
            }
        }

        function syncHidden() {
            normalize();
            const principal = images.find(i => i.principal) || images[0] || null;
            imagenUrl.value = principal ? principal.url : "";
            imagenesJson.value = JSON.stringify(images.map(i => ({ url: i.url, principal: !!i.principal })));
        }

        function renderPreview() {
            preview.innerHTML = "";
            normalize();
            if (!images.length) {
                const empty = document.createElement("div");
                empty.className = "text-muted";
                empty.textContent = "Aun no hay imagenes registradas.";
                preview.appendChild(empty);
                syncHidden();
                return;
            }

            images.forEach((img, idx) => {
                const item = document.createElement("div");
                item.style.display = "flex";
                item.style.alignItems = "center";
                item.style.gap = "10px";
                item.style.padding = "6px 0";

                const thumb = document.createElement("img");
                thumb.src = img.url;
                thumb.alt = "img";
                thumb.style.width = "56px";
                thumb.style.height = "56px";
                thumb.style.objectFit = "cover";
                thumb.style.borderRadius = "10px";
                thumb.style.border = img.principal ? "2px solid #0d6efd" : "1px solid #ddd";

                const meta = document.createElement("div");
                meta.style.flex = "1";

                const title = document.createElement("div");
                title.style.fontSize = "13px";
                title.textContent = img.principal ? "Principal" : "Imagen";

                const actions = document.createElement("div");
                actions.style.display = "flex";
                actions.style.gap = "8px";

                const btnPrincipal = document.createElement("button");
                btnPrincipal.type = "button";
                btnPrincipal.className = "btn btn-sm btn-outline-primary";
                btnPrincipal.textContent = "Hacer principal";
                btnPrincipal.disabled = !!img.principal;
                btnPrincipal.addEventListener("click", () => {
                    images.forEach(i => i.principal = false);
                    images[idx].principal = true;
                    renderPreview();
                });

                const btnRemove = document.createElement("button");
                btnRemove.type = "button";
                btnRemove.className = "btn btn-sm btn-outline-danger";
                btnRemove.textContent = "Quitar";
                btnRemove.addEventListener("click", () => {
                    const wasPrincipal = !!images[idx].principal;
                    images.splice(idx, 1);
                    if (wasPrincipal && images[0]) images[0].principal = true;
                    renderPreview();
                });

                actions.appendChild(btnPrincipal);
                actions.appendChild(btnRemove);

                meta.appendChild(title);
                meta.appendChild(actions);

                item.appendChild(thumb);
                item.appendChild(meta);

                preview.appendChild(item);
            });

            syncHidden();
        }

        async function uploadFile(file) {
            const safeName = `${Date.now()}_${Math.random().toString(16).slice(2)}_${file.name}`;
            const storageRef = ref(storage, `productos/${safeName}`);
            await uploadBytes(storageRef, file);
            return await getDownloadURL(storageRef);
        }

        if (fileInput) {
            fileInput.addEventListener("change", async () => {
                const files = Array.from(fileInput.files || []);
                if (!files.length) return;

                if (btnGuardar) btnGuardar.disabled = true;
                estado.textContent = "Subiendo imagenes...";

                try {
                    for (const f of files) {
                        const url = await uploadFile(f);
                        images.push({"url": url, "principal": images.length === 0});
                    }
                } catch (e) {
                    console.error(e);
                    alert("No se pudo subir una o mas imagenes. Revisa tu configuracion de Firebase.");
                } finally {
                    fileInput.value = "";
                    if (btnGuardar) btnGuardar.disabled = false;
                    estado.textContent = "Listo.";
                    renderPreview();
                }
            });
        }

        // Evitar guardar mientras se esta subiendo / si no hay principal
        const form = document.querySelector("form");
        if (form) {
            form.addEventListener("submit", (e) => {
                syncHidden();
                if (!imagenUrl.value) {
                    e.preventDefault();
                    alert("Debes tener al menos 1 imagen (principal) antes de guardar.");
                    return;
                }
                if (btnGuardar) {
                    btnGuardar.disabled = true;
                    btnGuardar.innerText = "Guardando...";
                }
            });
        }

        renderPreview();
</script>

</body>

</html>