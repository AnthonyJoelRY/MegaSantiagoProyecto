// Model/js/sesion-usuario.js
// Este script puede ser insertado dinámicamente (por fetch/layout-loader),
// así que NO debemos depender únicamente de DOMContentLoaded.

function initSesionUsuario() {
  const contenedor = document.getElementById("header-usuario");
  if (!contenedor) return;

  const usuario = JSON.parse(localStorage.getItem("usuarioMega"));

  // ❌ NO hay sesión
  if (!usuario) {
    contenedor.innerHTML = `
      <a href="/MegaSantiagoFront/View/pages/login.html" class="link-header">
        Acceder / Registrarse
      </a>
      <a href="/MegaSantiagoFront/View/pages/carrito.html" class="link-header">
        🛒 Carrito
      </a>
    `;
    return;
  }

  // ✅ HAY sesión
  contenedor.innerHTML = `
    <span class="user-name">Hola, ${usuario.email}</span>
    <a href="/MegaSantiagoFront/View/pages/carrito.html" class="link-header">🛒</a>
    <a href="#" id="logout" class="link-header">Salir</a>
  `;

  document.getElementById("logout").addEventListener("click", () => {
    localStorage.removeItem("usuarioMega");
    window.location.href = "/MegaSantiagoFront/index.html";
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initSesionUsuario);
} else {
  initSesionUsuario();
}
