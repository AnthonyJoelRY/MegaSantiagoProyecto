// View/js/layout-loader.js
// Carga layouts (header/nav/footer/scripts) usando rutas absolutas para NO romperse por carpetas.

const PROJECT_BASE = "/MegaSantiagoFront";
const LAYOUTS_BASE = `${PROJECT_BASE}/View/layouts`;

async function loadInto(id, url) {
  const el = document.getElementById(id);
  if (!el) return;
  const resp = await fetch(url, { cache: "no-cache" });
  if (!resp.ok) throw new Error(`No se pudo cargar ${url}`);
  el.innerHTML = await resp.text();
}

async function loadLayout() {
  // ⚠️ Importante:
  // No cargamos scripts.php con innerHTML porque los <script> insertados
  // así NO se ejecutan en la mayoría de navegadores.
  await Promise.all([
    loadInto("header", `${LAYOUTS_BASE}/header.php`),
    loadInto("nav", `${LAYOUTS_BASE}/nav.php`),
    loadInto("footer", `${LAYOUTS_BASE}/footer.php`)
  ]);

  // Cargar scripts globales de forma segura (sí se ejecutan).
  ensureScript(`${PROJECT_BASE}/Model/js/busqueda-global.js`);
  ensureScript(`${PROJECT_BASE}/Model/js/sesion-usuario.js`);

  // Marca activo el menú según URL actual (opcional)
  try {
    const path = window.location.pathname.toLowerCase();
    document.querySelectorAll(".menu a[data-route]").forEach(a => {
      const route = a.getAttribute("data-route");
      if (route && path.includes(route.toLowerCase())) {
        const li = a.closest("li");
        if (li) li.classList.add("active");
      }
    });
  } catch (e) {}
}

function ensureScript(src) {
  if (document.querySelector(`script[src="${src}"]`)) return;
  const s = document.createElement('script');
  s.src = src;
  s.defer = true;
  document.body.appendChild(s);
}
