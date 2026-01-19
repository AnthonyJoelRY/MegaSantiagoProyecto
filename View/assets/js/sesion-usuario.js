// View/assets/js/sesion-usuario.js

function initSesionUsuario() {
  const contenedor = document.getElementById("header-usuario");
  if (!contenedor) return;

  const base = window.PROJECT_BASE || "";
  const usuario = JSON.parse(localStorage.getItem("usuarioMega") || "null");

  const carritoLink = (extraLinks = "") => `
    <a href="${base}/View/pages/carrito.html"
       class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2 position-relative carrito-link">
      <i class="bi bi-cart3"></i>
      <span>Carrito</span>
      <span id="carrito-count"
            style="display:none; position:absolute; top:-6px; right:-10px;
                   min-width:18px; height:18px; padding:0 5px;
                   border-radius:999px; font-size:12px; line-height:18px;
                   text-align:center; background:#e53935; color:#fff;">
        0
      </span>
    </a>
    ${extraLinks}
  `;

  // ❌ NO hay sesión
  if (!usuario) {
    contenedor.innerHTML = `
      <a href="${base}/View/pages/login.html"
         class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2">
        <i class="bi bi-person"></i>
        Acceder
      </a>
      ${carritoLink()}
    `;

    // ✅ badge existe recién después del innerHTML
    actualizarContadorCarrito();
    return;
  }

  // ✅ HAY sesión
  const esAdmin = (Number(usuario.rol) === 1);
  const esEmpleado = (Number(usuario.rol) === 4);
  // 2 = Empresa (antes se trataba como vendedor)
  const esEmpresa = (Number(usuario.rol) === 2);
  const esCliente = (Number(usuario.rol) === 3);

  let htmlSesion = `
    <span class="user-name fw-semibold text-muted small">
      Hola, ${usuario.email}
    </span>
  `;

  // 👉 ADMINISTRADOR o EMPLEADO: ver dashboard
  if (esAdmin || esEmpleado) {
    htmlSesion += `
      <a href="${base}/dashboard"
         class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2">
        <i class="bi bi-speedometer2"></i>
        Panel de control
      </a>
    `;
  }

  // 👉 EMPRESA
  // (no mostramos dashboard; solo habilitamos compra como cliente)
  if (esEmpresa) {
    ;
  }

  // ✅ Mis pedidos para Cliente y Empresa
  // (mis-pedidos muestra solo los pedidos del usuario logueado)
  if ((esCliente || esEmpresa) && usuario.email) {
    htmlSesion += `
      <a href="${base}/mis-pedidos"
         class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2">
        <i class="bi bi-box-seam"></i>
        Mis pedidos
      </a>
    `;
  }

  // ✅ Carrito para Cliente y Empresa
  if (esCliente || esEmpresa) {
    htmlSesion += `
      ${carritoLink(`
        <a href="#" id="logout"
           class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-2">
          <i class="bi bi-box-arrow-right"></i>
          Salir
        </a>
      `)}
    `;
  } else {
    // Otros roles: solo logout
    htmlSesion += `
      <a href="#" id="logout"
         class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-right"></i>
        Salir
      </a>
    `;
  }

  contenedor.innerHTML = htmlSesion;

  // ✅ actualizar badge solo si existe (si no es cliente, no existe y no pasa nada)
  actualizarContadorCarrito();

  const btnLogout = document.getElementById("logout");
  if (btnLogout) {
    btnLogout.addEventListener("click", (e) => {
      e.preventDefault();
      localStorage.removeItem("usuarioMega");
      window.location.href = `${base}/index.html`;
    });
  }
}

function actualizarContadorCarrito() {
  const badge = document.getElementById("carrito-count");
  if (!badge) return;

  const carrito = JSON.parse(localStorage.getItem("carritoMega") || "[]");
  const total = carrito.reduce((sum, item) => sum + (item.cantidad || 0), 0);

  if (total > 0) {
    badge.textContent = total;
    badge.style.display = "inline-block";
  } else {
    badge.style.display = "none";
  }
}

window.addEventListener("carrito_actualizado", () => {
  actualizarContadorCarrito();
});

window.addEventListener("storage", (e) => {
  if (e.key === "carritoMega") {
    actualizarContadorCarrito();
  }
});

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initSesionUsuario);
} else {
  initSesionUsuario();
}
