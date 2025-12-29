// Model/js/busqueda-global.js
function realizarBusquedaGlobal() {
  const input = document.getElementById("buscador");
  if (!input) return;

  const termino = input.value.trim();
  if (!termino) return;

  // index.html está en raíz → busqueda está en View/pages
  window.location.href =
    "/MegaSantiagoFront/View/pages/busqueda.html?q=" +
    encodeURIComponent(termino);
}
