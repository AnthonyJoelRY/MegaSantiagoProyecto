<?php require_once __DIR__ . "/../../Model/Config/base.php"; ?>
<div class="top-bar py-2">
  <div class="container">
    <p class="mb-0 text-center fw-bold text-uppercase"
       style="font-size: 0.95rem; letter-spacing: 0.8px;">
      MEGASANTIAGO · TU TIENDA DE PAPELERÍA Y OFICINA
    </p>
  </div>
</div>

<header class="main-header">

  <div class="header-left">
    <a href="<?= PROJECT_BASE ?>/index.html">
      <img src="https://firebasestorage.googleapis.com/v0/b/megasantiago.firebasestorage.app/o/productos%2FLogo_MS_new.png?alt=media&token=1af8129d-6d18-4da8-bc9e-4445faab1a65"
           alt="MegaSantiago"
           class="logo-mega">
    </a>
  </div>

  <div class="header-center">
    <div class="search-box search-rounded">
      <input id="buscador" type="text" placeholder="Busca tu producto">
      <button class="btn-search" type="button" onclick="realizarBusquedaGlobal()" aria-label="Buscar">
        <i class="bi bi-search"></i>
      </button>
    </div>
  </div>

<!-- CONTENEDOR DERECHO DEL HEADER -->
<div class="header-right">

  <!-- BOTÓN HAMBURGUESA (MENÚ MÓVIL) -->
  <button class="btn-menu-mobile" type="button" aria-label="Abrir menú">
    <i class="bi bi-list"></i>
  </button>

  <!-- ESTE DIV LO SIGUE USANDO sesion-usuario.js -->
  <div id="header-usuario"></div>

</div>


</header>


<!-- ===============================
     MENÚ MÓVIL OVERLAY (FUERA DEL HEADER)
     =============================== -->
<div class="menu-mobile-overlay" id="menuMobile">
  <nav class="menu-mobile" id="menuMobileContent">
    <!-- AQUÍ se inyecta nav.php -->
  </nav>
</div>

