<?php $seccionActiva = "promociones"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Promoción | MegaSantiago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-fluid">
    <div class="row">

        <?php include __DIR__ . "/../partials/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">

            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                <h2 class="fw-bold text-primary mb-0">➕ Nueva Promoción</h2>
                <a href="<?= PROJECT_BASE ?>/panel/promociones" class="btn btn-outline-secondary">Volver</a>
            </div>

            <form action="<?= PROJECT_BASE ?>/panel/promociones/acciones"
                  method="POST"
                  class="card shadow-sm p-4 rounded-4">

                <input type="hidden" name="accion" value="crear">

                <!-- Fechas -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha fin (opcional)</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                </div>

                <!-- ===== BANNER PROMOCIÓN (NUEVO) ===== -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Banner de la promoción</label>

                    <!-- Archivo -->
<input type="file" id="fileBanner" accept="image/*" class="form-control" required>
<input type="hidden" name="imagen_banner" id="bannerUrl" required>

<small class="text-muted d-block mt-2" id="estadoUpload">
    Selecciona un banner para subirlo a Firebase.
</small>

                </div>

                <button type="submit"
                        id="btnGuardar"
                        class="btn btn-primary">
                    Guardar promoción
                </button>

            </form>

        </main>
    </div>
</div>

<!-- ================= FIREBASE JS ================= -->

<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.14.1/firebase-app.js";
import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.14.1/firebase-storage.js";

const firebaseConfig = {
    apiKey: "AIzaSyDkCYDW61i0q186DDEJ-bxAvjp0iAW4-Xc",
    authDomain: "megasantiago.firebaseapp.com",
    projectId: "megasantiago",
    storageBucket: "megasantiago.firebasestorage.app",
    messagingSenderId: "68954725464",
    appId: "1:68954725464:web:7e93567bcc98b4b3d83e2d"
};

const app = initializeApp(firebaseConfig);
const storage = getStorage(app);

// 🔥 IDs CORRECTOS
const fileInput = document.getElementById("fileBanner");
const bannerUrl = document.getElementById("bannerUrl");
const estado = document.getElementById("estadoUpload");
const form = document.querySelector("form");
const btnGuardar = document.getElementById("btnGuardar");

function nombreSeguro(nombre) {
    return nombre.toLowerCase()
        .replace(/\s+/g, "_")
        .replace(/[^a-z0-9._-]/g, "");
}

async function subirBanner(file) {
    const path = `banners/${Date.now()}_promo.jpg`;
    const storageRef = ref(storage, path);

    await uploadBytes(storageRef, file, { contentType: file.type });
    return await getDownloadURL(storageRef);
}

fileInput.addEventListener("change", async () => {
    const file = fileInput.files[0];
    if (!file) return;

    estado.textContent = "Subiendo banner...";
    bannerUrl.value = "";
    btnGuardar.disabled = true;

    try {
        const url = await subirBanner(file);
        bannerUrl.value = url;
        estado.textContent = "✅ Banner subido correctamente.";
        btnGuardar.disabled = false;
    } catch (e) {
        console.error("🔥 Firebase error:", e);
        estado.textContent = "❌ Error subiendo banner: " + e.message;
    }
});

form.addEventListener("submit", (e) => {
    if (!bannerUrl.value) {
        e.preventDefault();
        alert("Primero espera a que el banner termine de subirse.");
    }
});
</script>





</body>
</html>
