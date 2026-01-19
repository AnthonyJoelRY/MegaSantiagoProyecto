<?php $seccionActiva = "productos"; ?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto | MegaSantiago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">

            <?php include __DIR__ . "/../partials/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">

                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <h2 class="fw-bold text-primary mb-0">➕ Nuevo Producto</h2>
                    <a href="<?= PROJECT_BASE ?>/panel/productos" class="btn btn-outline-secondary">Volver</a>
                </div>

                <form action="<?= PROJECT_BASE ?>/panel/productos/acciones" method="POST" class="card shadow-sm p-4 rounded-4">
                    <input type="hidden" name="accion" value="crear">

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c["id_categoria"] ?>"><?= htmlspecialchars($c["nombre"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción corta</label>
                        <input type="text" name="descripcion_corta" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción larga</label>
                        <textarea name="descripcion_larga" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Descuento (%)</label>
                            <input type="number" step="0.01" name="precio_oferta" class="form-control">
                        </div>
                    </div>
                    
                    
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Stock inicial</label>
        <input
            type="number"
            name="stock"
            class="form-control"
            min="1"
            value="1"
            required
        >
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Stock mínimo (Alerta)</label>
        <input
            type="number"
            name="stock_minimo"
            class="form-control"
            min="1"
            value="1"
            required
        >
    </div>
</div>



                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Imagenes del producto</label>

                        <input type="file" id="fileImagenes" accept="image/*" class="form-control" multiple required>

                        <input type="hidden" name="imagen" id="imagenUrl" required>
                        <input type="hidden" name="imagenes_json" id="imagenesJson">

                        <small class="text-muted d-block mt-2" id="estadoUpload">Selecciona una o mas imagenes para subirlas a Firebase.</small>
                        <div id="previewImgs" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="aplica_iva" checked>
                        <label class="form-check-label">Aplica IVA</label>
                    </div>

                    <!-- ✅ COLORES (un producto puede tener varios) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Colores del producto (opcional)</label>
                        <div class="row g-2">
                            <?php if (!empty($colores)): ?>
                                <?php foreach ($colores as $c): ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <label class="d-flex align-items-center gap-2 border rounded-3 p-2" style="cursor:pointer;">
                                            <input type="checkbox" name="colores[]" value="<?= (int)$c['id_color'] ?>">
                                            <span style="width:18px;height:18px;border-radius:999px;border:1px solid #ddd;display:inline-block;background:<?= htmlspecialchars($c['codigo_hex']) ?>;"></span>
                                            <span><?= htmlspecialchars($c['nombre']) ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-secondary mb-0">
                                        Aún no hay colores registrados. Puedes crear uno abajo.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-3 p-3 border rounded-3 bg-white">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nuevo color (nombre)</label>
                                    <input type="text" name="nuevo_color_nombre" class="form-control" placeholder="Ej: Rojo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código HEX</label>
                                    <input type="text" name="nuevo_color_hex" class="form-control" placeholder="#FF0000">
                                </div>
                            </div>
                            <small class="text-muted">Si escribes un nuevo color, se creará (si no existe) y se asignará al producto.</small>
                        </div>
                    </div>

                    <button type="submit" id="btnGuardar" class="btn btn-primary">
    Guardar producto
</button>


                </form>

            </main>
        </div>
    </div>

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
        const preview = document.getElementById("previewImgs");
        const imagenUrl = document.getElementById("imagenUrl");
        const imagenesJson = document.getElementById("imagenesJson");
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("btnGuardar");

        let imagenes = []; // [{url, principal}]

        function nombreSeguro(nombre) {
            return nombre
                .toLowerCase()
                .replace(/\s+/g, "_")
                .replace(/[^a-z0-9._-]/g, "");
        }

        async function subirImagen(file) {
            const stamp = Date.now();
            const path = `productos/${stamp}_${nombreSeguro(file.name)}`;
            const storageRef = ref(storage, path);
            await uploadBytes(storageRef, file, { contentType: file.type });
            return await getDownloadURL(storageRef);
        }

        function syncHidden() {
            // Asegurar que exista 1 principal
            if (imagenes.length > 0 && !imagenes.some(i => i.principal)) {
                imagenes[0].principal = true;
            }
            const principal = imagenes.find(i => i.principal);
            imagenUrl.value = principal ? principal.url : "";
            imagenesJson.value = JSON.stringify(imagenes);
        }

        function renderPreview() {
            if (!preview) return;
            preview.innerHTML = "";
            imagenes.forEach((img, idx) => {
                const card = document.createElement("div");
                card.style.cssText = "width:110px;border:1px solid #e5e5e5;border-radius:12px;padding:8px;display:flex;flex-direction:column;gap:6px;align-items:center;background:#fff;";

                const im = document.createElement("img");
                im.src = img.url;
                im.alt = "img";
                im.style.cssText = "width:90px;height:70px;object-fit:cover;border-radius:10px;";

                const badge = document.createElement("div");
                badge.textContent = img.principal ? "Principal" : "";
                badge.style.cssText = "font-size:11px;color:#0d6efd;min-height:14px;";

                const btnPrincipal = document.createElement("button");
                btnPrincipal.type = "button";
                btnPrincipal.className = "btn btn-sm btn-outline-primary";
                btnPrincipal.textContent = "Hacer principal";
                btnPrincipal.disabled = img.principal;
                btnPrincipal.addEventListener("click", () => {
                    imagenes = imagenes.map((x, i) => ({...x, principal: i === idx}));
                    syncHidden();
                    renderPreview();
                });

                const btnEliminar = document.createElement("button");
                btnEliminar.type = "button";
                btnEliminar.className = "btn btn-sm btn-outline-danger";
                btnEliminar.textContent = "Quitar";
                btnEliminar.addEventListener("click", () => {
                    imagenes.splice(idx, 1);
                    syncHidden();
                    renderPreview();
                });

                card.appendChild(im);
                card.appendChild(badge);
                card.appendChild(btnPrincipal);
                card.appendChild(btnEliminar);
                preview.appendChild(card);
            });
        }

        fileInput.addEventListener("change", async () => {
            const files = Array.from(fileInput.files || []);
            if (files.length === 0) return;

            if (btnGuardar) btnGuardar.disabled = true;
            estado.textContent = "Subiendo imagenes a Firebase...";

            try {
                for (const f of files) {
                    const url = await subirImagen(f);
                    imagenes.push({ url, principal: imagenes.length === 0 });
                }
                estado.textContent = "Imagenes subidas correctamente.";
                syncHidden();
                renderPreview();
            } catch (e) {
                console.error(e);
                estado.textContent = "Error subiendo imagenes. Revisa Rules de Storage y consola.";
            } finally {
                // permitir re-seleccionar las mismas imagenes
                fileInput.value = "";
                if (btnGuardar) btnGuardar.disabled = false;
            }
        });

        form.addEventListener("submit", (e) => {
            syncHidden();
            if (!imagenUrl.value) {
                e.preventDefault();
                alert("Debes subir al menos 1 imagen y marcar una como principal.");
                return;
            }
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerText = "Guardando...";
            }
        });
</script>

</body>

</html>