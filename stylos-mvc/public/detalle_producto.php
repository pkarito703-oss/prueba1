<?php
session_start();
require_once __DIR__ . '/../src/config/conexion.php';
require_once __DIR__ . '/../src/controllers/CarritoController.php';

// Cargar items del carrito si el usuario está logueado
$carrito_items  = [];
$carrito_count  = 0;
$carrito_subtotal = 0;
$id_carrito_actual = null;

if (isset($_SESSION['logueado']) && $_SESSION['logueado']) {
    $carritoCtrl      = new CarritoController($conexion);
    $id_carrito_actual = $carritoCtrl->obtenerOCrearCarrito($_SESSION['id_usuario']);
    $items_result     = $carritoCtrl->obtenerItems($_SESSION['id_usuario']);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $carrito_items[] = $item;
        $carrito_count  += $item['cantidad'];
        $carrito_subtotal += $item['subtotal_linea'];
    }
}

// Cargar productos desde la BD
$productos_db = [];
$sql_prod = "SELECT * FROM productos ORDER BY id_productos ASC";
$res_prod = mysqli_query($conexion, $sql_prod);
while ($p = mysqli_fetch_assoc($res_prod)) {
    $productos_db[] = $p;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Categorías - Stylos Madys | Productos Capilares</title>
  <link href="assets/img/favicon.png" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    /* Toast de notificación */
    .toast-carrito {
      position: fixed;
      bottom: 25px;
      right: 25px;
      z-index: 9999;
      min-width: 280px;
      padding: 14px 20px;
      border-radius: 12px;
      color: #fff;
      font-size: 0.92rem;
      font-weight: 500;
      box-shadow: 0 4px 20px rgba(0,0,0,0.18);
      display: flex;
      align-items: center;
      gap: 10px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.35s ease;
      pointer-events: none;
    }
    .toast-carrito.show { opacity: 1; transform: translateY(0); }
    .toast-carrito.success { background: #28a745; }
    .toast-carrito.error   { background: #dc3545; }
    .toast-carrito.warning { background: #c8956c; }

    /* Botón agregando... */
    .quick-add-btn.loading, .cart-icon-btn.loading {
      opacity: 0.6;
      pointer-events: none;
    }

    /* Cart panel vacío */
    .cart-empty {
      text-align: center;
      padding: 30px 20px;
      color: #999;
    }
    .cart-empty i { font-size: 2.5rem; margin-bottom: 10px; display: block; }
  </style>
</head>

<body class="category-page">

<header id="header" class="header position-relative">
  <div class="header-bar">
    <div class="container-fluid container-xl">
      <div class="d-flex align-items-center justify-content-between">

        <!-- Logo -->
        <a href="index.php" class="logo d-flex align-items-center">
          <img src="assets/img/logo.png" alt="Stylos Madys">
        </a>

        <!-- Search -->
        <form class="header-search d-none d-lg-flex">
          <input type="text" class="search-input" id="headerSearch" placeholder="Buscar productos, categorías, marcas...">
          <button class="search-btn" type="button" onclick="buscarDesdeHeader()">
            <i class="bi bi-search"></i>
            <span class="d-none d-xl-inline">Buscar</span>
          </button>
        </form>

        <!-- Header Actions -->
        <div class="header-actions d-flex align-items-center">

          <!-- Account -->
          <div class="dropdown account-dropdown">
            <button class="action-icon" data-bs-toggle="dropdown" aria-label="Account">
              <i class="bi bi-person"></i>
              <span class="action-label d-none d-md-inline">
                <?php echo isset($_SESSION['logueado']) && $_SESSION['logueado'] ? htmlspecialchars($_SESSION['nombre']) : 'Ingresar'; ?>
              </span>
            </button>
            <div class="dropdown-menu account-panel">
              <?php if (isset($_SESSION['logueado']) && $_SESSION['logueado']): ?>
                <div class="account-panel-header">
                  <h6>¡Bienvenida, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h6>
                  <p><?= htmlspecialchars($_SESSION['email']) ?></p>
                </div>
                <div class="account-panel-links">
                  <a href="citas.php"><i class="bi bi-calendar-check"></i><span>Mis Citas</span></a>
                  <a href="logout.php" style="color:#e74c3c;"><i class="bi bi-box-arrow-right"></i><span>Cerrar Sesión</span></a>
                </div>
              <?php else: ?>
                <div class="account-panel-header">
                  <h6>¡Bienvenido!</h6>
                  <p>Ingresa para tener la mejor experiencia</p>
                </div>
                <div class="account-panel-auth">
                  <a href="login.php" class="btn btn-signin">Iniciar Sesión</a>
                  <a href="login.php?tab=register" class="btn btn-register">Crear Cuenta</a>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Cart -->
          <div class="dropdown cart-dropdown">
            <button class="action-icon" data-bs-toggle="dropdown" aria-label="Cart">
              <i class="bi bi-cart3"></i>
              <span class="count-dot" id="cart-count" <?= $carrito_count == 0 ? 'style="display:none"' : '' ?>>
                <?= $carrito_count ?>
              </span>
            </button>
            <div class="dropdown-menu cart-panel">
              <div class="cart-panel-header">
                <h6>Carrito de Compras</h6>
                <span class="item-count" id="cart-item-label"><?= $carrito_count ?> Producto<?= $carrito_count != 1 ? 's' : '' ?></span>
              </div>

              <div class="cart-panel-body" id="cart-panel-body">
                <?php if (empty($carrito_items)): ?>
                  <div class="cart-empty" id="cart-empty-msg">
                    <i class="bi bi-cart-x"></i>
                    <?php if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']): ?>
                      <p>Inicia sesión para ver tu carrito</p>
                      <a href="login.php" class="btn btn-sm" style="background:#c8956c; color:#fff; border-radius:8px;">Iniciar Sesión</a>
                    <?php else: ?>
                      <p>Tu carrito está vacío</p>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <?php foreach ($carrito_items as $item): ?>
                    <div class="cart-item" id="cart-item-<?= $item['id_detalle'] ?>">
                      <div class="cart-item-img">
                        <img src="<?= htmlspecialchars($item['imagen'] ?? 'assets/img/product/product-1.webp') ?>" alt="<?= htmlspecialchars($item['nombre_producto']) ?>" class="img-fluid">
                      </div>
                      <div class="cart-item-info">
                        <h6><?= htmlspecialchars($item['nombre_producto']) ?></h6>
                        <div class="cart-item-meta">
                          <span class="cart-item-price">$<?= number_format($item['precio_unitario'], 0, ',', '.') ?></span>
                          <span class="cart-item-qty">Cant: <?= $item['cantidad'] ?></span>
                        </div>
                      </div>
                      <button class="cart-item-remove" onclick="eliminarDelCarrito(<?= $item['id_detalle'] ?>, this)" aria-label="Eliminar">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>

              <div class="cart-panel-footer">
                <div class="cart-subtotal">
                  <span>Subtotal</span>
                  <span class="cart-subtotal-price" id="cart-subtotal">
                    $<?= number_format($carrito_subtotal, 0, ',', '.') ?>
                  </span>
                </div>
                <a href="checkout.php" class="btn btn-checkout">Finalizar Compra</a>
                <a href="cart.php" class="btn-viewcart">Ver carrito completo →</a>
              </div>
            </div>
          </div>

          <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
        </div>
      </div>
    </div>
  </div>

   <!-- Navigation -->
    <div class="header-nav">
      <div class="container-fluid container-xl position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="index.php" class="active">Inicio</a></li>
            <li><a href="about.php">Acerca de</a></li>
            <li><a href="servicios.php">Servicios</a></li>
            <li><a href="detalle_producto.php">Detalles de Producto</a></li>            
            <li><a href="checkout.php">Finalizar Compra</a></li>
            <li><a href="contact.html">Contacto</a></li>
          </ul>
        </nav>
      </div>
    </div>

<main class="main">


  <div class="container">
    <div class="row">

      <!-- Sidebar -->
      <div class="col-lg-4 sidebar">
        <div class="widgets-container">

          <!-- Categorías -->
          <div class="product-categories-widget widget-item">
            <h3 class="widget-title">Categorías de Productos</h3>
            <ul class="category-tree list-unstyled mb-0">
              <li class="category-item">
                <div class="d-flex justify-content-between align-items-center category-header collapsed" data-bs-toggle="collapse" data-bs-target="#cat-shampoos">
                  <a href="javascript:void(0)" class="category-link" onclick="filtrarCategoria('shampoo')">Shampoos</a>
                  <span class="category-toggle"><i class="bi bi-chevron-down"></i><i class="bi bi-chevron-up"></i></span>
                </div>
                <ul id="cat-shampoos" class="subcategory-list list-unstyled collapse ps-3 mt-2">
                  <li><a href="#" class="subcategory-link" onclick="filtrarCategoria('shampoo')">Todos los Shampoos</a></li>
                </ul>
              </li>
              <li class="category-item">
                <div class="d-flex justify-content-between align-items-center category-header collapsed" data-bs-toggle="collapse" data-bs-target="#cat-mascarillas">
                  <a href="javascript:void(0)" class="category-link" onclick="filtrarCategoria('mascarilla')">Mascarillas</a>
                  <span class="category-toggle"><i class="bi bi-chevron-down"></i><i class="bi bi-chevron-up"></i></span>
                </div>
                <ul id="cat-mascarillas" class="subcategory-list list-unstyled collapse ps-3 mt-2">
                  <li><a href="#" class="subcategory-link" onclick="filtrarCategoria('mascarilla')">Todas las Mascarillas</a></li>
                </ul>
              </li>
              <li class="category-item">
                <a href="javascript:void(0)" class="category-link" onclick="filtrarCategoria('')">Ver todos</a>
              </li>
            </ul>
          </div>

          <!-- Rango de precio -->
          <div class="pricing-range-widget widget-item">
            <h3 class="widget-title">Rango de Precio</h3>
            <div class="price-range-container">
              <div class="current-range mb-3">
                <span class="min-price" id="label-min">$0</span>
                <span class="max-price float-end" id="label-max">$100.000</span>
              </div>
              <div class="price-inputs mt-3">
                <div class="row g-2">
                  <div class="col-6">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="precioMin" placeholder="Min" value="0" min="0">
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="precioMax" placeholder="Max" value="1000000" min="0">
                    </div>
                  </div>
                </div>
              </div>
              <div class="filter-actions mt-3">
                <button type="button" class="btn btn-sm btn-primary w-100" onclick="aplicarFiltros()">Aplicar Filtro</button>
              </div>
            </div>
          </div>

          <!-- Filtro por marca -->
          <div class="filter-widget widget-item">
            <h3 class="widget-title">Filtrar por Marca</h3>
            <div class="brand-list" id="lista-marcas">
              <!-- Se llena dinámicamente desde los productos -->
            </div>
            <div class="brand-actions mt-2">
              <button class="btn btn-sm btn-outline-primary" onclick="aplicarFiltros()">Aplicar Filtro</button>
              <button class="btn btn-sm btn-link" onclick="limpiarFiltros()">Limpiar Todo</button>
            </div>
          </div>

        </div>
      </div>

      <!-- Productos -->
      <div class="col-lg-8">

        <section id="category-header" class="category-header section">
          <div class="container section-title" data-aos="fade-up">
            <h2>Productos Capilares</h2>
            <p>Descubre nuestra selección de productos profesionales para el cuidado de tu cabello</p>
          </div>

          <div class="container" data-aos="fade-up" data-aos-delay="100">
            <!-- Buscador -->
            <div class="search-bar" data-aos="fade-down">
              <i class="bi bi-search search-icon"></i>
              <input type="text" class="search-input" id="productSearch" placeholder="¿Qué producto buscas?" oninput="aplicarFiltros()">
              <button class="search-action" type="button" onclick="aplicarFiltros()">Buscar</button>
            </div>

            <!-- Ordenar -->
            <div class="toolbar mt-3" data-aos="fade-up" data-aos-delay="200">
              <div class="row align-items-center g-3">
                <div class="col-lg-8">
                  <div class="filter-group">
                    <div class="filter-dropdown">
                      <label for="sortBy">Ordenar por</label>
                      <select class="form-select" id="sortBy" onchange="aplicarFiltros()">
                        <option value="default">Destacados</option>
                        <option value="precio_asc">Precio: Menor a Mayor</option>
                        <option value="precio_desc">Precio: Mayor a Menor</option>
                        <option value="nombre_asc">Nombre A-Z</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="toolbar-actions">
                    <div class="display-toggle">
                      <button type="button" class="toggle-btn active" data-view="grid" onclick="cambiarVista('grid')"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                      <button type="button" class="toggle-btn" data-view="list" onclick="cambiarVista('list')"><i class="bi bi-list-ul"></i></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Resultados contador -->
            <p class="mt-2 text-muted" style="font-size:0.88rem;">
              Mostrando <strong id="contador-productos">0</strong> productos
            </p>
          </div>
        </section>

        <!-- Lista de productos -->
        <section id="category-product-list" class="category-product-list section">
          <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-3 g-lg-4" id="productos-grid">

              <?php if (empty($productos_db)): ?>
                <!-- Mensaje si no hay productos en la BD todavía -->
                <div class="col-12 text-center py-5" id="sin-productos-db">
                  <i class="bi bi-box-seam" style="font-size:3rem; color:#ccc;"></i>
                  <p class="text-muted mt-3">No hay productos registrados aún.</p>
                  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                    <a href="admin/productos_admin.php" class="btn btn-sm" style="background:#7c3aed; color:#fff; border-radius:8px;">
                      <i class="bi bi-plus-lg me-1"></i> Agregar Productos
                    </a>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <?php foreach ($productos_db as $p): ?>
                  <div class="col-6 col-lg-4 producto-card-wrapper"
                       data-nombre="<?= strtolower(htmlspecialchars($p['nombre_producto'])) ?>"
                       data-marca="<?= strtolower(htmlspecialchars($p['marca_producto'] ?? '')) ?>"
                       data-precio="<?= $p['precio_venta'] ?>">
                    <article class="item-card">
                      <div class="item-media">
                        <img src="<?= htmlspecialchars($p['imagen'] ?? 'assets/img/product/product-1.webp') ?>"
                             class="primary-img img-fluid"
                             alt="<?= htmlspecialchars($p['nombre_producto']) ?>"
                             onerror="this.src='assets/img/product/product-1.webp'">
                        <?php if ($p['stock'] <= 0): ?>
                          <span class="item-label" style="background:#dc3545;">Sin stock</span>
                        <?php endif; ?>
                        <div class="quick-add">
                          <button type="button"
                                  class="quick-add-btn"
                                  onclick="agregarAlCarrito(<?= $p['id_productos'] ?>, this)"
                                  <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-bag-plus me-1"></i>
                            <?= $p['stock'] <= 0 ? 'Sin stock' : 'Agregar' ?>
                          </button>
                        </div>
                      </div>
                      <div class="item-info">
                        <span class="item-tag"><?= htmlspecialchars($p['marca_producto'] ?? 'Stylos Madys') ?></span>
                        <h4 class="item-name">
                          <a href="detalle_producto.php?id=<?= $p['id_productos'] ?>">
                            <?= htmlspecialchars($p['nombre_producto']) ?>
                          </a>
                        </h4>
                        <div class="item-bottom">
                          <span class="item-cost">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></span>
                          <button type="button"
                                  class="cart-icon-btn"
                                  onclick="agregarAlCarrito(<?= $p['id_productos'] ?>, this)"
                                  <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-cart3"></i>
                          </button>
                        </div>
                        <?php if ($p['descripcion']): ?>
                          <p class="text-muted" style="font-size:0.8rem; margin-top:4px; display:none;" class="desc-producto">
                            <?= htmlspecialchars(substr($p['descripcion'], 0, 60)) ?>...
                          </p>
                        <?php endif; ?>
                      </div>
                    </article>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

            </div>

            <!-- Mensaje sin resultados -->
            <div id="sin-resultados" class="text-center py-5" style="display:none;">
              <i class="bi bi-search" style="font-size:2.5rem; color:#ccc;"></i>
              <p class="text-muted mt-3">No se encontraron productos con esos filtros.</p>
              <button class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltros()">Limpiar filtros</button>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>
</main>

<footer id="footer" class="footer dark-background">
  <div class="footer-bottom">
    <div class="container">
      <div class="row gy-3 align-items-center">
        <div class="col-lg-6">
          <p class="mb-0">© <strong>Stylos Madys</strong>. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Toast de notificación -->
<div class="toast-carrito" id="toastCarrito">
  <i class="bi bi-check-circle-fill" id="toast-icon"></i>
  <span id="toast-mensaje">Producto agregado</span>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>
<script>
AOS.init();

// ─── CARRITO ────────────────────────────────────────────────

function agregarAlCarrito(id_producto, btn) {
  btn.classList.add('loading');

  fetch('agregar_carrito.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id_producto=' + id_producto + '&cantidad=1'
  })
  .then(r => r.json())
  .then(data => {
    btn.classList.remove('loading');

    if (data.login) {
      mostrarToast('Debes iniciar sesión primero', 'warning');
      setTimeout(() => window.location.href = 'login.php', 1500);
      return;
    }

    if (data.ok) {
      mostrarToast(data.mensaje, 'success');
      actualizarContadorCarrito(data.total_items);
    if (data.ok) {
  mostrarToast(data.mensaje, 'success');
  setTimeout(() => location.reload(), 800); // ← agregar
      // ── Agregar item al panel visualmente ──
      const emptyMsg = document.getElementById('cart-empty-msg');
      if (emptyMsg) emptyMsg.remove();

      const body = document.getElementById('cart-panel-body');
      
      // Si ya existe el item en el panel, actualizar cantidad
      const itemExistente = document.getElementById('cart-item-prod-' + id_producto);
      if (itemExistente) {
        const qtySpan = itemExistente.querySelector('.cart-item-qty');
        const actual  = parseInt(qtySpan.textContent.replace('Cant: ', ''));
        qtySpan.textContent = 'Cant: ' + (actual + 1);
      } else {
        // Obtener info del producto desde la card
        const card     = btn.closest('.producto-card-wrapper');
        const nombre   = card.querySelector('.item-name a').textContent.trim();
        const precio   = card.querySelector('.item-cost').textContent.trim();
        const img      = card.querySelector('.primary-img').src;

        body.insertAdjacentHTML('afterbegin', `
          <div class="cart-item" id="cart-item-prod-${id_producto}">
            <div class="cart-item-img">
              <img src="${img}" alt="${nombre}" class="img-fluid">
            </div>
            <div class="cart-item-info">
              <h6>${nombre}</h6>
              <div class="cart-item-meta">
                <span class="cart-item-price">${precio}</span>
                <span class="cart-item-qty">Cant: 1</span>
              </div>
            </div>
            <button class="cart-item-remove" onclick="eliminarDelCarrito(${data.id_detalle}, this)" aria-label="Eliminar">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        `);
      }
    } else {
      mostrarToast(data.mensaje, 'error');
    }
  })
  .catch(() => {
    btn.classList.remove('loading');
    mostrarToast('Error de conexión', 'error');
  });
}

// Al eliminar, recarga la página para reflejar cambios reales de BD
function eliminarDelCarrito(id_detalle, btn) {
  btn.disabled = true; // evita doble click

  fetch('eliminar_carrito.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id_detalle=' + id_detalle
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      // Eliminar visualmente el item del panel
      const item = document.getElementById('cart-item-' + id_detalle);
      if (item) item.remove();

      // Actualizar contador
      actualizarContadorCarrito(data.total_items);

      // Si el carrito quedó vacío, mostrar mensaje
      const body = document.getElementById('cart-panel-body');
      if (data.total_items === 0) {
        body.innerHTML = `
          <div class="cart-empty" id="cart-empty-msg">
            <i class="bi bi-cart-x"></i>
            <p>Tu carrito está vacío</p>
          </div>`;
      }

      mostrarToast('Producto eliminado', 'success');
setTimeout(() => location.reload(), 800); // ← agregar
    } else {
      mostrarToast(data.mensaje, 'error');
      btn.disabled = false;
    }
  })
  .catch(() => {
    mostrarToast('Error de conexión', 'error');
    btn.disabled = false;
  });
}

// ─── FILTROS ────────────────────────────────────────────────

// Construir lista de marcas dinámicamente
window.addEventListener('DOMContentLoaded', () => {
  const cards  = document.querySelectorAll('.producto-card-wrapper');
  const marcas = new Set();

  cards.forEach(c => {
    const m = c.dataset.marca;
    if (m) marcas.add(m);
  });

  const lista = document.getElementById('lista-marcas');
  if (lista) {
    marcas.forEach((m, i) => {
      lista.innerHTML += `
        <div class="brand-item">
          <div class="form-check">
            <input class="form-check-input marca-check" type="checkbox" id="marca-${i}" value="${m}">
            <label class="form-check-label" for="marca-${i}">${m.charAt(0).toUpperCase() + m.slice(1)}</label>
          </div>
        </div>`;
    });
  }

  aplicarFiltros(); // Inicializar contador
});

function aplicarFiltros() {
  const busqueda  = document.getElementById('productSearch').value.toLowerCase();
  const precioMin = parseFloat(document.getElementById('precioMin').value) || 0;
  const precioMax = parseFloat(document.getElementById('precioMax').value) || 9999999;
  const sortBy    = document.getElementById('sortBy').value;

  const marcasChecked = [...document.querySelectorAll('.marca-check:checked')].map(c => c.value);
  const cards = [...document.querySelectorAll('.producto-card-wrapper')];

  let visibles = cards.filter(card => {
    const nombre = card.dataset.nombre || '';
    const marca  = card.dataset.marca  || '';
    const precio = parseFloat(card.dataset.precio) || 0;

    const matchBusqueda = nombre.includes(busqueda) || marca.includes(busqueda);
    const matchPrecio   = precio >= precioMin && precio <= precioMax;
    const matchMarca    = marcasChecked.length === 0 || marcasChecked.includes(marca);

    const visible = matchBusqueda && matchPrecio && matchMarca;
    card.style.display = visible ? '' : 'none';
    return visible;
  });

  // Ordenar
  const grid = document.getElementById('productos-grid');
  visibles.sort((a, b) => {
    const pa = parseFloat(a.dataset.precio);
    const pb = parseFloat(b.dataset.precio);
    const na = a.dataset.nombre;
    const nb = b.dataset.nombre;
    if (sortBy === 'precio_asc')  return pa - pb;
    if (sortBy === 'precio_desc') return pb - pa;
    if (sortBy === 'nombre_asc')  return na.localeCompare(nb);
    return 0;
  });
  visibles.forEach(c => grid.appendChild(c));

  // Contador
  document.getElementById('contador-productos').textContent = visibles.length;

  // Sin resultados
  const sinRes = document.getElementById('sin-resultados');
  if (sinRes) sinRes.style.display = visibles.length === 0 ? 'block' : 'none';
}

function limpiarFiltros() {
  document.getElementById('productSearch').value = '';
  document.getElementById('precioMin').value = 0;
  document.getElementById('precioMax').value = 1000000;
  document.querySelectorAll('.marca-check').forEach(c => c.checked = false);
  document.getElementById('sortBy').value = 'default';
  aplicarFiltros();
}

function filtrarCategoria(termino) {
  document.getElementById('productSearch').value = termino;
  aplicarFiltros();
}

function cambiarVista(tipo) {
  const grid = document.getElementById('productos-grid');
  document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
  document.querySelector(`[data-view="${tipo}"]`).classList.add('active');

  if (tipo === 'list') {
    grid.classList.add('row-cols-1');
    document.querySelectorAll('.producto-card-wrapper').forEach(c => {
      c.className = 'col-12 producto-card-wrapper';
      Object.assign(c.dataset, c.dataset); // mantener data
    });
  } else {
    document.querySelectorAll('.producto-card-wrapper').forEach(c => {
      c.className = 'col-6 col-lg-4 producto-card-wrapper';
    });
  }
}
function actualizarContadorCarrito(total) {
  const countEl = document.getElementById('cart-count');
  if (countEl) {
    countEl.textContent = total;
    countEl.style.display = total === 0 ? 'none' : '';
  }
  const labelEl = document.getElementById('cart-item-label');
  if (labelEl) {
    labelEl.textContent = total + ' Producto' + (total !== 1 ? 's' : '');
  }
}
function buscarDesdeHeader() {
  const q = document.getElementById('headerSearch').value;
  document.getElementById('productSearch').value = q;
  aplicarFiltros();
  document.getElementById('category-product-list').scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>