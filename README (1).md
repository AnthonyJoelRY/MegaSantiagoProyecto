<h1>MegaSantiago — Renovación del Portal Web (MVC + Ecommerce)</h1>
<p><strong>Papelería MegaSantiago — Proyecto de rediseño, reconstrucción y modernización del sistema</strong></p>

<hr>

<h2>Descripción del Proyecto</h2>
<p>
  Este repositorio contiene el desarrollo del sistema <strong>MegaSantiago</strong>, una nueva versión del portal web de la
  <strong>Papelería MegaSantiago</strong>. La empresa contaba con un portal anterior que presentaba fallos funcionales,
  problemas de usabilidad y limitaciones técnicas.
</p>
<p>
  El objetivo del proyecto fue <strong>reconstruir el sistema</strong> aplicando una arquitectura clara
  (MVC + Front Controller), mejorando la estabilidad, seguridad, experiencia de usuario y habilitando
  funcionalidades clave como compras en línea, gestión de inventario y pagos con PayPal.
</p>

<hr>

<h2>Objetivo General</h2>
<p>
  Desarrollar un sistema web que permita a la Papelería MegaSantiago gestionar sus productos, clientes y pedidos
  de forma eficiente, brindando a los usuarios una experiencia de compra clara, segura y accesible.
</p>

<h2>Objetivos Específicos</h2>
<ul>
  <li>Implementar autenticación y gestión de usuarios por roles.</li>
  <li>Desarrollar módulo de gestión de productos e inventarios.</li>
  <li>Permitir la realización de compras en línea.</li>
  <li>Integrar una pasarela de pago segura (PayPal).</li>
  <li>Facilitar la visualización y seguimiento de pedidos por parte del cliente.</li>
  <li>Mejorar la experiencia de usuario mediante una interfaz responsiva.</li>
</ul>

<hr>

<h2>Funcionalidades Principales</h2>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th align="left">Módulo</th>
      <th align="left">Descripción</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Catálogo de productos</td>
      <td>Visualización, detalle, búsqueda y productos relacionados.</td>
    </tr>
    <tr>
      <td>Carrito de compras</td>
      <td>Gestión de productos con validación de stock.</td>
    </tr>
    <tr>
      <td>Pedidos</td>
      <td>Registro, detalle, estado y visualización por cliente.</td>
    </tr>
    <tr>
      <td>Direcciones / Sucursal</td>
      <td>Entrega a domicilio o retiro en sucursal según el flujo de compra.</td>
    </tr>
    <tr>
      <td>Panel administrativo</td>
      <td>Gestión de productos, inventario y pedidos.</td>
    </tr>
    <tr>
      <td>Pagos</td>
      <td>Integración con PayPal (Sandbox / Live).</td>
    </tr>
    <tr>
      <td>Seguridad</td>
      <td>Recuperación de contraseña y control de acceso por roles.</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>Arquitectura</h2>
<p>
  El sistema utiliza el patrón <strong>MVC</strong> reforzado con <strong>Front Controller</strong>,
  además de capas de <strong>Service</strong>, <strong>DAO</strong> y <strong>Entity</strong>.
</p>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th align="left">Capa</th>
      <th align="left">Ubicación</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Front Controller</td>
      <td><code>index.php</code>, <code>.htaccess</code></td>
    </tr>
    <tr>
      <td>Controladores</td>
      <td><code>Controller/</code></td>
    </tr>
    <tr>
      <td>Modelo</td>
      <td><code>Model/</code> (DAO, Entity, Service, Config, DB)</td>
    </tr>
    <tr>
      <td>Vistas</td>
      <td><code>View/</code> (público y panel)</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>Tecnologías Utilizadas</h2>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th align="left">Categoría</th>
      <th align="left">Tecnologías</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Backend</td>
      <td>PHP 8, PDO</td>
    </tr>
    <tr>
      <td>Base de datos</td>
      <td>MySQL</td>
    </tr>
    <tr>
      <td>Frontend</td>
      <td>HTML5, CSS3, JavaScript</td>
    </tr>
    <tr>
      <td>Interfaz de usuario</td>
      <td>Bootstrap 5</td>
    </tr>
    <tr>
      <td>Servicios externos</td>
      <td>PayPal API</td>
    </tr>
    <tr>
      <td>Hosting</td>
      <td>InfinityFree (Apache + MySQL)</td>
    </tr>
    <tr>
      <td>Herramientas</td>
      <td>XAMPP, GitHub (Wiki y control de versiones)</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>Roles y Permisos</h2>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th align="left">Rol</th>
      <th align="left">Permisos principales</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Administrador</td>
      <td>Control total del sistema y panel administrativo.</td>
    </tr>
    <tr>
      <td>Empleado</td>
      <td>Gestión operativa de productos y pedidos.</td>
    </tr>
    <tr>
      <td>Cliente</td>
      <td>Compras, direcciones y visualización de pedidos propios.</td>
    </tr>
    <tr>
      <td>Visitante</td>
      <td>Navegación del catálogo sin acceso a compras.</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>Instalación y Configuración (InfinityFree)</h2>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th align="left">Paso</th>
      <th align="left">Descripción</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Subir el proyecto al directorio público del dominio (htdocs).</td>
    </tr>
    <tr>
      <td>2</td>
      <td>Crear la base de datos en el hosting e importar el archivo SQL.</td>
    </tr>
    <tr>
      <td>3</td>
      <td>Configurar credenciales de base de datos en <code>Model/Config/credenciales.php</code>.</td>
    </tr>
    <tr>
      <td>4</td>
      <td>Configurar credenciales de PayPal (Client ID y Secret).</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>PayPal Sandbox (Checkout)</h2>
<ul>
  <li>Configurar credenciales de PayPal en el archivo correspondiente del proyecto.</li>
  <li>El carrito consume los siguientes endpoints:</li>
</ul>
<ul>
  <li><code>Controller/paypalController.php?accion=config</code></li>
  <li><code>Controller/paypalController.php?accion=create-order</code></li>
  <li><code>Controller/paypalController.php?accion=capture-order</code></li>
</ul>

<hr>

<h2>Documentación</h2>
<p>
  La documentación técnica y funcional completa está disponible en la Wiki del proyecto:
</p>
<p>
  <a href="https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki">
    https://github.com/AnthonyJoelRY/MegaSantiagoFront/wiki
  </a>
</p>

<hr>
<h2>Licencia</h2>
<p>
  Este proyecto se distribuye bajo la <strong>Apache License, Version 2.0</strong>.
</p>

<hr>
