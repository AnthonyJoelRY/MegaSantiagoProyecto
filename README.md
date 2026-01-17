<h1>📘 MegaSantiago — Renovación del Portal Web (MVC + Ecommerce)</h1>
<p><strong>Papelería MegaSantiago — Proyecto de rediseño, reconstrucción y modernización del sistema</strong></p>

<hr>

<h2>📌 Descripción del Proyecto</h2>
<p>
  Este repositorio contiene el desarrollo del sistema <strong>MegaSantiago</strong>, una nueva versión del portal web de la
  <strong>Papelería MegaSantiago</strong>. La empresa contaba con un portal anterior que presentaba fallos funcionales,
  problemas de usabilidad y limitaciones técnicas.  
</p>
<p>
  El objetivo del proyecto fue <strong>reconstruir el sistema</strong> aplicando una arquitectura clara (MVC + Front Controller),
  mejorando la estabilidad, seguridad, experiencia de usuario y habilitando funcionalidades clave como compras en línea,
  gestión de inventario y pagos con PayPal.
</p>

<hr>

<h2>🎯 Objetivo General</h2>
<p>
  Desarrollar un sistema web que permita a la Papelería MegaSantiago gestionar sus productos, clientes y pedidos de forma eficiente,
  brindando a los usuarios una experiencia de compra clara, segura y accesible.
</p>

<h2>✅ Objetivos Específicos</h2>
<ul>
  <li>Implementar autenticación y gestión de usuarios por roles.</li>
  <li>Desarrollar módulo de gestión de productos e inventarios.</li>
  <li>Permitir la realización de compras en línea.</li>
  <li>Integrar una pasarela de pago segura (PayPal).</li>
  <li>Facilitar la visualización y seguimiento de pedidos por parte del cliente.</li>
  <li>Mejorar la experiencia de usuario mediante una interfaz responsiva.</li>
</ul>

<hr>

<h2>🧩 Funcionalidades Principales</h2>
<ul>
  <li><strong>Catálogo de productos</strong> (visualización, detalle, búsqueda y productos relacionados).</li>
  <li><strong>Carrito de compras</strong> con validación de stock.</li>
  <li><strong>Pedidos</strong> (registro, detalle, estado y visualización por cliente).</li>
  <li><strong>Direcciones / Retiro en sucursal</strong> según el flujo de compra.</li>
  <li><strong>Panel administrativo</strong> para gestión de productos e inventario.</li>
  <li><strong>Pago con PayPal</strong> (Sandbox/Live).</li>
  <li><strong>Recuperación de contraseña</strong> (flujo de reseteo).</li>
</ul>

<hr>

<h2>🧱 Arquitectura</h2>
<p>
  El sistema utiliza <strong>MVC</strong> reforzado con <strong>Front Controller</strong> (punto único de entrada),
  además de capas de <strong>Service</strong>, <strong>DAO</strong> y <strong>Entity</strong>.
</p>

<ul>
  <li><strong>Front Controller:</strong> <code>index.php</code> + <code>.htaccess</code></li>
  <li><strong>Controllers:</strong> <code>Controller/</code></li>
  <li><strong>Model:</strong> <code>Model/</code> (DAO, Entity, Service, Config, DB)</li>
  <li><strong>Views:</strong> <code>View/</code> (público y panel)</li>
</ul>

<hr>

<h2>🧰 Tecnologías Utilizadas</h2>
<ul>
  <li><strong>Backend:</strong> PHP 8, PDO</li>
  <li><strong>Base de datos:</strong> MySQL</li>
  <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript</li>
  <li><strong>UI Panel:</strong> Bootstrap 5</li>
  <li><strong>Servicios externos:</strong> PayPal API</li>
  <li><strong>Hosting:</strong> InfinityFree (Apache + MySQL)</li>
  <li><strong>Herramientas:</strong> XAMPP (local), GitHub (Wiki/Control de versiones)</li>
</ul>

<hr>

<h2>🔐 Roles y Permisos (RBAC)</h2>
<ul>
  <li><strong>Administrador:</strong> control total del sistema y panel.</li>
  <li><strong>Empleado:</strong> gestión operativa de productos/pedidos en panel.</li>
  <li><strong>Cliente:</strong> compras, direcciones y visualización de sus pedidos.</li>
  <li><strong>Visitante:</strong> navegación del catálogo sin acceso a compras.</li>
</ul>

<hr>

<h2>🚀 Instalación y Configuración (InfinityFree)</h2>
<p>
  Para desplegar el sistema en un dominio (InfinityFree), se realizan 2 acciones principales:
</p>
<ol>
  <li><strong>Subir el proyecto</strong> al directorio público del dominio (ej: <code>htdocs/</code>).</li>
  <li><strong>Crear base de datos</strong> en el hosting e importar el <code>.sql</code>.</li>
</ol>

<p><strong>Configurar credenciales obligatorias:</strong></p>
<ul>
  <li><strong>Base de datos:</strong> editar <code>Model/Config/credenciales.php</code></li>
  <li><strong>PayPal:</strong> editar credenciales (Client ID / Secret) en el archivo de configuración correspondiente del proyecto</li>
</ul>

<hr>

<h2>💳 PayPal Sandbox (Checkout)</h2>
<ul>
  <li>Configura tus credenciales en el archivo de PayPal del proyecto (Client ID / Secret).</li>
  <li>El carrito (ej: <code>View/pages/carrito.html</code>) carga el SDK de PayPal y usa endpoints del backend:</li>
</ul>

<ul>
  <li><code>Controller/paypalController.php?accion=config</code></li>
  <li><code>Controller/paypalController.php?accion=create-order</code></li>
  <li><code>Controller/paypalController.php?accion=capture-order</code></li>
</ul>

<hr>

<h2>📚 Documentación (GitHub Wiki)</h2>
<p>
  La documentación técnica y funcional del proyecto está disponible en la Wiki:
</p>
<p>
  <a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki">https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki</a>
</p>

<p><strong>Índice de la Wiki:</strong></p>
<ol>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/01%E2%80%90Descripci%C3%B3n-del-Proyecto">Descripción del Proyecto</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/02%E2%80%90Requisitos-del-Sistema">Requisitos del Sistema</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/03%E2%80%90Diagramas-del-Sistema">Diagramas del Sistema</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/04%E2%80%90Arquitectura-y-Dise%C3%B1o">Arquitectura y Diseño</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/05%E2%80%90C%C3%B3digo-Fuente">Código Fuente</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/06%E2%80%90Tecnolog%C3%ADas-Utilizadas">Tecnologías Utilizadas</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/07%E2%80%90Instalaci%C3%B3n-y-Configuraci%C3%B3n">Instalación y Configuración</a></li>
  <li><a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki/08%E2%80%90Roles-y-Permisos">Roles y Permisos</a></li>
</ol>

<hr>

<h2>🧾 Wireframe</h2>
<p>
  Enlace al wireframe (Excalidraw):
  <a href="https://excalidraw.com/#json=_fcWCgIk4n3clVOwuzyCa,tix3LXE9W7AnRhXhoEdX8Q">
    https://excalidraw.com/#json=_fcWCgIk4n3clVOwuzyCa,tix3LXE9W7AnRhXhoEdX8Q
  </a>
</p>

<hr>

<h2>📄 Licencia</h2>
<p>
  Este proyecto se distribuye bajo la <strong>Apache License, Version 2.0</strong>.
</p>

<hr>

