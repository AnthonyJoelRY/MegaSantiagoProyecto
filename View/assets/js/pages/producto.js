// View/assets/js/pages/producto.js
(function () {
  const CART_KEY = "carritoMega";

  const API = () => "/Controller/productosController.php";

  function getParam(name) {
    const u = new URL(window.location.href);
    return u.searchParams.get(name);
  }

  function money(n) {
    const x = Number(n || 0);
    return `$${x.toFixed(2)}`;
  }

  function setMsg(text, ok = true) {
    const el = document.getElementById("msg");
    if (!el) return;
    el.textContent = text || "";
    el.className = `msg ${ok ? "ok" : "err"}`;
  }

  // ✅ Manejo de imágenes: URL externa (Firebase) o ruta relativa guardada en BD (imagenes/...)
  function resolverImagen(img) {
    if (!img || String(img).trim() === "") return "/Model/imagenes/sin-imagen.png";
    const limpia = String(img).trim();
    if (/^https?:\/\//i.test(limpia)) return limpia;

    // si viene "imagenes/xxx.jpg" o "imagenes/..."
    if (limpia.startsWith("imagenes/")) return "/Model/" + limpia;

    // si viene solo "xxx.jpg" asumimos /Model/imagenes/xxx.jpg
    return "/Model/imagenes/" + limpia;
  }

  function obtenerCarrito() {
    try { return JSON.parse(localStorage.getItem(CART_KEY) || "[]"); }
    catch { return []; }
  }

  function guardarCarrito(carrito) {
    localStorage.setItem(CART_KEY, JSON.stringify(carrito));
    try { window.dispatchEvent(new Event("carrito_actualizado")); } catch (e) {}
    if (typeof window.actualizarContadorCarrito === "function") window.actualizarContadorCarrito();
  }

  function agregarAlCarrito(item) {
    const carrito = obtenerCarrito();

    // si manejas color como variante, lo tomamos en cuenta
    const idx = carrito.findIndex(
      (x) => String(x.id) === String(item.id) && String(x.color || "") === String(item.color || "")
    );

    if (idx >= 0) {
      carrito[idx].cantidad = (carrito[idx].cantidad || 1) + item.cantidad;
    } else {
      carrito.push(item);
    }

    guardarCarrito(carrito);
  }

  function cardRelacionado(prod) {
    const precioBase = Number(prod.precio || 0);
    const precioOferta = prod.precio_oferta != null ? Number(prod.precio_oferta) : null;
    const precioMostrado =
      precioOferta && precioOferta > 0 && precioOferta < precioBase ? precioOferta : precioBase;

    const rutaImagen = resolverImagen(prod.imagen);

    return `
      <article class="card-producto" data-id="${prod.id}" style="cursor:pointer;">
        <div class="img-producto"><img src="${rutaImagen}" alt="${prod.nombre}"></div>
        <p class="nombre-producto">${prod.nombre}</p>
        <p class="precio-actual">${money(precioMostrado)} ${prod.aplica_iva ? "<small>+ IVA</small>" : ""}</p>
      </article>
    `;
  }

  function wireClickCards(container) {
    container.querySelectorAll(".card-producto").forEach((card) => {
      card.addEventListener("click", (e) => {
        if (e.target.closest("button") || e.target.closest("a")) return;
        const id = card.dataset.id;
        window.location.href = `/View/pages/producto.html?id=${encodeURIComponent(id)}`;
      });
    });
  }

  function prepararQty() {
    const inp = document.getElementById("inpQty");
    const btnMenos = document.getElementById("btnMenos");
    const btnMas = document.getElementById("btnMas");

    if (!inp || !btnMenos || !btnMas) return;

    btnMenos.onclick = () => {
      const v = Math.max(1, Number(inp.value || 1) - 1);
      inp.value = v;
    };
    btnMas.onclick = () => {
      const v = Math.max(1, Number(inp.value || 1) + 1);
      inp.value = v;
    };
    inp.addEventListener("change", () => {
      const v = Math.max(1, Number(inp.value || 1));
      inp.value = v;
    });
  }

  // ✅ Relacionados con fallback "Más vendidos"
  // - Si hay relacionados por categoría → título "Productos relacionados"
  // - Si no hay → muestra "Más vendidos" para que la sección no quede vacía
  async function cargarRelacionados(id) {
    const relWrap = document.getElementById("relWrap");
    const grid = document.getElementById("gridRel");
    const titulo = document.getElementById("relTitulo") || relWrap?.querySelector("h2");

    if (!relWrap || !grid) return;

    async function pintar(lista, textoTitulo) {
      const arr = (Array.isArray(lista) ? lista : []).filter(
        (p) => String(p?.id ?? "") !== String(id)
      );

      if (!arr.length) {
        relWrap.style.display = "none";
        return;
      }

      if (titulo) titulo.textContent = textoTitulo;
      relWrap.style.display = "block";
      grid.innerHTML = arr.map(cardRelacionado).join("");
      wireClickCards(grid);
    }

    // 1) Intentar relacionados
    const respRel = await fetch(`${API()}?accion=relacionados&id=${encodeURIComponent(id)}&limit=4`);
    const dataRel = await respRel.json().catch(() => null);

    if (respRel.ok && Array.isArray(dataRel) && dataRel.length) {
      await pintar(dataRel, "Productos relacionados");
      return;
    }

    // 2) Fallback: más vendidos
    const respMV = await fetch(`${API()}?accion=masVendidos&limit=4`);
    const dataMV = await respMV.json().catch(() => null);

    if (respMV.ok && Array.isArray(dataMV) && dataMV.length) {
      await pintar(dataMV, "Más vendidos");
      return;
    }

    relWrap.style.display = "none";
  }

  async function cargarDetalle() {
    const id = Number(getParam("id") || 0);
    const estado = document.getElementById("estado");

    if (!id) {
      if (estado) estado.innerHTML = "<p>Falta el id del producto.</p>";
      return;
    }

    if (estado) estado.innerHTML = "<p>Cargando producto...</p>";

    const resp = await fetch(`${API()}?accion=detalle&id=${encodeURIComponent(id)}`);
    const data = await resp.json().catch(() => null);

    if (!resp.ok || !data || data.error) {
      if (estado) estado.innerHTML = `<p>${(data && data.error) || "No se pudo cargar el producto."}</p>`;
      return;
    }

    const wrap = document.getElementById("productoWrap");
    if (wrap) wrap.style.display = "grid";
    if (estado) estado.innerHTML = "";

    // Info
    document.getElementById("pNombre").textContent = data.nombre || "Producto";
    document.getElementById("pSku").textContent =
      data.sku ? `SKU: ${data.sku} | Categoría: ${data.categoria_nombre}` : `Categoría: ${data.categoria_nombre}`;

    const precioBase = Number(data.precio || 0);
    const precioOferta = data.precio_oferta != null ? Number(data.precio_oferta) : null;
    const precioMostrado =
      precioOferta && precioOferta > 0 && precioOferta < precioBase ? precioOferta : precioBase;

    document.getElementById("pPrecio").innerHTML =
      `${money(precioMostrado)} ${data.aplica_iva ? "<small>+ IVA</small>" : ""}`;

    // ✅ Precio Empresa (rol=2): solo visual (el descuento real se aplica al generar el pedido)
    try {
      const u = JSON.parse(localStorage.getItem("usuarioMega") || "null");
      const esEmpresa = u && Number(u.rol) === 2;
      const elPE = document.getElementById("pPrecioEmpresa");
      if (elPE && esEmpresa) {
        const rate = 0.10; // 10% empresa
        const precioEmpresa = Math.max(0, precioMostrado * (1 - rate));
        elPE.style.display = "block";
        elPE.innerHTML = `Empresa: ${money(precioEmpresa)} <span style="font-weight:600; opacity:.8;">(-${Math.round(rate * 100)}%)</span>`;
      } else if (elPE) {
        elPE.style.display = "none";
        elPE.innerHTML = "";
      }
    } catch (e) {}

    document.getElementById("pDescCorta").textContent = data.descripcion_corta || "";
    document.getElementById("pDescLarga").innerHTML = (data.descripcion_larga || "").replace(/\n/g, "<br>");

    // ============================
    // Galería (principal + miniaturas)
    // ============================
    const imgPrincipal = document.getElementById("imgPrincipal");
    const thumbs = document.getElementById("thumbs");

    const normalizeUrl = (u) => {
      const s = String(u || "").trim();
      if (!s) return "";
      if (s.startsWith("http://") || s.startsWith("https://")) return s;
      return s.startsWith("/") ? s : `/${s}`;
    };

    // En tu API: data.imagenes es array de objetos {url_imagen, es_principal, orden}
    let imgs = [];
    if (Array.isArray(data.imagenes)) {
      imgs = data.imagenes
        .map((x) => normalizeUrl(x.url_imagen ?? x.imagen ?? x.url ?? x))
        .filter(Boolean);
    }

    // Fallback por si algún endpoint devuelve "imagen"
    if (!imgs.length && data.imagen) imgs = [normalizeUrl(data.imagen)];

    if (imgPrincipal) {
      imgPrincipal.src = imgs[0] || "https://via.placeholder.com/600x600?text=Sin+Imagen";
      imgPrincipal.onerror = () => {
        imgPrincipal.src = "https://via.placeholder.com/600x600?text=Sin+Imagen";
      };
    }

    if (thumbs) {
      thumbs.innerHTML = "";
      imgs.forEach((url, idx) => {
        const b = document.createElement("button");
        b.type = "button";
        b.innerHTML = `<img src="${url}" alt="Imagen ${idx + 1}" />`;
        b.addEventListener("click", () => {
          if (imgPrincipal) imgPrincipal.src = url;
        });
        thumbs.appendChild(b);
      });
    }

      function ensureCarouselControls(total) {
        const wrap = document.querySelector(".product-image-main");
        if (!wrap) return;

        // limpiar controles prev/next existentes
        wrap.querySelectorAll(".carousel-prev,.carousel-next").forEach(el => el.remove());

        if (total <= 1) return;

        const prev = document.createElement("button");
        prev.type = "button";
        prev.className = "carousel-prev";
        prev.innerHTML = "&#10094;";
        prev.addEventListener("click", () => setIndex(currentIndex - 1));

        const next = document.createElement("button");
        next.type = "button";
        next.className = "carousel-next";
        next.innerHTML = "&#10095;";
        next.addEventListener("click", () => setIndex(currentIndex + 1));

        wrap.appendChild(prev);
        wrap.appendChild(next);

        // swipe en mobile
        let x0 = null;
        imgPrincipal.addEventListener("touchstart", (e) => {
          x0 = (e.touches && e.touches[0]) ? e.touches[0].clientX : null;
        }, { passive: true });
        imgPrincipal.addEventListener("touchend", (e) => {
          if (x0 === null) return;
          const x1 = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0].clientX : null;
          if (x1 === null) return;
          const dx = x1 - x0;
          x0 = null;
          if (Math.abs(dx) < 35) return;
          if (dx > 0) setIndex(currentIndex - 1);
          else setIndex(currentIndex + 1);
        }, { passive: true });
      }

      function renderDots(total) {
        let dotsWrap = document.getElementById("carouselDots");
        if (!dotsWrap) {
          dotsWrap = document.createElement("div");
          dotsWrap.id = "carouselDots";
          dotsWrap.className = "carousel-dots";
          const gallery = document.querySelector(".product-gallery");
          if (gallery) gallery.appendChild(dotsWrap);
        }
        dotsWrap.innerHTML = "";
        if (total <= 1) return;
        for (let i = 0; i < total; i++) {
          const d = document.createElement("button");
          d.type = "button";
          d.className = "carousel-dot" + (i === currentIndex ? " active" : "");
          d.addEventListener("click", () => setIndex(i));
          dotsWrap.appendChild(d);
        }
      }


    // Colores (1 producto puede tener varios; se elige 1 antes de agregar al carrito)
    const colores = Array.isArray(data.colores) ? data.colores : [];
    const rowColor = document.getElementById("rowColor");
    const sel = document.getElementById("selColor");

    if (colores.length && rowColor && sel) {
      rowColor.style.display = "flex";
      // ✅ Placeholder para obligar a escoger
      sel.innerHTML = [
        '<option value="">Seleccione un color...</option>',
        ...colores.map((c) => `<option value="${String(c).replace(/"/g, '&quot;')}">${c}</option>`)
      ].join("");
    } else if (rowColor) {
      rowColor.style.display = "none";
      if (sel) sel.innerHTML = "";
    }

    // Qty
    prepararQty();

    // Add
    const btnAdd = document.getElementById("btnAdd");
    btnAdd.onclick = () => {
      const inp = document.getElementById("inpQty");
      const qty = Math.max(1, Number(inp.value || 1));

      const colorSel =
        (rowColor && rowColor.style.display !== "none" && sel)
          ? String(sel.value || "").trim()
          : "";

      // ✅ Si hay colores disponibles, exigir selección
      if (colores.length && !colorSel) {
        setMsg("Selecciona un color antes de agregar al carrito.", false);
        return;
      }

      agregarAlCarrito({
        id: data.id,
        nombre: data.nombre,
        precio: precioMostrado,
        cantidad: qty,
        color: colorSel,
      });

      setMsg("Agregado al carrito.", true);
    };

    await cargarRelacionados(id);
  }

  // ✅ init para ejecutar después de loadLayout()
  window.initProductoPage = function () {
    cargarDetalle().catch((e) => {
      console.error(e);
      const estado = document.getElementById("estado");
      if (estado) estado.innerHTML = "<p>Error al cargar el producto.</p>";
    });
  };
})();
