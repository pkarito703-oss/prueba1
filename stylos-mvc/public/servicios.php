<?php
session_start();
include("../src/config/conexion.php");

$sql = "SELECT * FROM servicios WHERE activo = 1 ORDER BY precio ASC";
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Servicios - Stylos Madys</title>
  <meta name="description" content="Descubre todos los servicios de belleza y cuidado capilar de Stylos Madys.">

  <link href="assets/img/favicon.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* ── Variables ── */
    :root {
      --rosa:      #c9a0a0;
      --rosa-dark: #9e6b6b;
      --crema:     #fdf6f0;
      --carbon:    #1a1a1a;
      --muted:     #7a6e6e;
      --borde:     #e8dbd5;
    }

    body { background: var(--crema); font-family: 'DM Sans', sans-serif; }

    /* ── HERO SERVICIOS ── */
    .servicios-hero {
      background: var(--carbon);
      padding: 90px 0 60px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .servicios-hero::before {
      content: "SERVICIOS";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(80px, 14vw, 160px);
      font-weight: 700;
      color: rgba(255,255,255,0.04);
      white-space: nowrap;
      pointer-events: none;
      letter-spacing: 0.2em;
    }
    .servicios-hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2.5rem, 5vw, 4rem);
      font-weight: 600;
      color: #fff;
      margin-bottom: 12px;
      position: relative;
    }
    .servicios-hero h1 em {
      color: var(--rosa);
      font-style: italic;
    }
    .servicios-hero p {
      color: rgba(255,255,255,0.5);
      font-size: 1rem;
      font-weight: 300;
      max-width: 400px;
      margin: 0 auto;
      position: relative;
    }
    .hero-line {
      width: 1px;
      height: 50px;
      background: var(--rosa);
      margin: 20px auto 30px;
    }

    /* ── FILTROS ── */
    .filtros-wrap {
      background: #fff;
      border-bottom: 1px solid var(--borde);
      padding: 18px 0;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .filtros-wrap .container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }
    .filtro-count {
      font-size: 0.85rem;
      color: var(--muted);
      white-space: nowrap;
    }
    .filtro-count span { font-weight: 600; color: var(--carbon); }
    .filtros-tags { display: flex; gap: 8px; flex-wrap: wrap; }
    .filtro-tag {
      border: 1px solid var(--borde);
      background: transparent;
      color: var(--muted);
      padding: 6px 16px;
      border-radius: 40px;
      font-size: 0.82rem;
      cursor: pointer;
      transition: all .2s;
      font-family: 'DM Sans', sans-serif;
    }
    .filtro-tag:hover, .filtro-tag.activo {
      background: var(--carbon);
      border-color: var(--carbon);
      color: #fff;
    }
    .ordenar-select {
      border: 1px solid var(--borde);
      background: transparent;
      color: var(--muted);
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.82rem;
      cursor: pointer;
      outline: none;
      font-family: 'DM Sans', sans-serif;
    }

    /* ── GRID ── */
    .servicios-section { padding: 60px 0 100px; }

    .card-sv {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--borde);
      transition: transform .3s ease, box-shadow .3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .card-sv:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 50px rgba(26,26,26,.1);
    }

    /* imagen con fallback */
    .card-sv-img {
      position: relative;
      height: 240px;
      overflow: hidden;
      background: linear-gradient(135deg, #e8dbd5 0%, #c9a0a0 100%);
    }
    .card-sv-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .5s ease;
      display: block;
    }
    .card-sv:hover .card-sv-img img { transform: scale(1.06); }

    /* placeholder si imagen falla */
    .img-fallback {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity .3s;
    }
    .img-fallback i { font-size: 3rem; color: rgba(255,255,255,.7); }
    .img-fallback span { font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: rgba(255,255,255,.6); margin-top: 8px; }
    .card-sv-img img.error ~ .img-fallback { opacity: 1; }

    /* badge duración */
    .badge-dur {
      position: absolute;
      top: 14px;
      right: 14px;
      background: rgba(26,26,26,.75);
      backdrop-filter: blur(6px);
      color: #fff;
      font-size: 0.78rem;
      padding: 5px 11px;
      border-radius: 30px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    /* contenido tarjeta */
    .card-sv-body {
      padding: 24px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    .card-sv-nombre {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.45rem;
      font-weight: 600;
      color: var(--carbon);
      margin-bottom: 8px;
      line-height: 1.2;
    }
    .card-sv-desc {
      font-size: 0.88rem;
      color: var(--muted);
      line-height: 1.65;
      flex-grow: 1;
      margin-bottom: 20px;
    }
    .card-sv-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 18px;
      border-top: 1px solid var(--borde);
    }
    .card-sv-precio {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.7rem;
      font-weight: 700;
      color: var(--carbon);
      line-height: 1;
    }
    .card-sv-precio span { font-size: 1rem; font-weight: 400; color: var(--muted); }

    /* botón reservar */
    .btn-sv {
      background: var(--carbon);
      color: #fff;
      border: none;
      padding: 10px 22px;
      border-radius: 40px;
      font-size: 0.85rem;
      font-weight: 500;
      font-family: 'DM Sans', sans-serif;
      cursor: pointer;
      transition: background .25s, transform .15s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
    }
    .btn-sv:hover { background: var(--rosa-dark); transform: scale(1.02); color: #fff; }
    .btn-sv i { font-size: 0.9rem; }

    /* ── SIN RESULTADOS ── */
    .empty-state {
      text-align: center;
      padding: 80px 20px;
    }
    .empty-state i { font-size: 3rem; color: var(--borde); }
    .empty-state h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem;
      color: var(--muted);
      margin-top: 16px;
    }

    /* ── ANIMACIÓN ENTRADA ── */
    .card-entry {
      opacity: 0;
      transform: translateY(24px);
      animation: entrar .5s ease forwards;
    }
    @keyframes entrar {
      to { opacity: 1; transform: translateY(0); }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 576px) {
      .servicios-hero { padding: 70px 0 40px; }
      .card-sv-img { height: 200px; }
    }
  </style>
</head>

<body class="index-page">

  <!-- ── HEADER (igual al resto del sitio) ── -->
  <header id="header" class="header position-relative">
    <div class="header-bar">
      <div class="container-fluid container-xl">
        <div class="d-flex align-items-center justify-content-between">

          <a href="index.php" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="Stylos Madys">
          </a>

          <form class="header-search d-none d-lg-flex">
            <input type="text" class="search-input" placeholder="Buscar productos, categorías, marcas...">
            <button class="search-btn" type="submit">
              <i class="bi bi-search"></i>
              <span class="d-none d-xl-inline">Buscar</span>
            </button>
          </form>

          <div class="header-actions d-flex align-items-center">
            <button class="action-icon mobile-search-toggle d-lg-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mobileSearch">
              <i class="bi bi-search"></i>
            </button>

            <!-- Account -->
            <div class="dropdown account-dropdown">
              <button class="action-icon" data-bs-toggle="dropdown" aria-label="Account">
                <i class="bi bi-person"></i>
                <span class="action-label d-none d-md-inline">
                  <?php if(isset($_SESSION['logueado']) && $_SESSION['logueado']): ?>
                    <?= htmlspecialchars($_SESSION['nombre']) ?>
                  <?php else: ?>
                    Ingresar
                  <?php endif; ?>
                </span>
              </button>
              <div class="dropdown-menu account-panel">
                <?php if(isset($_SESSION['logueado']) && $_SESSION['logueado']): ?>
                  <div class="account-panel-header">
                    <h6>¡Bienvenida, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h6>
                    <p><?= htmlspecialchars($_SESSION['email']) ?></p>
                  </div>
                  <div class="account-panel-links">
                    <a href="orders.php"><i class="bi bi-box-seam"></i><span>Mis Pedidos</span></a>
                    <a href="citas.php"><i class="bi bi-calendar-check"></i><span>Mis Citas</span></a>
                    <a href="src/controllers/AuthController.php?action=logout" style="color:#e74c3c;">
                      <i class="bi bi-box-arrow-right"></i><span>Cerrar Sesión</span>
                    </a>
                  </div>
                <?php else: ?>
                  <div class="account-panel-header">
                    <h6>¡Bienvenida!</h6>
                    <p>Ingresa para tener la mejor experiencia</p>
                  </div>
                  <div class="account-panel-auth">
                    <a href="login.php" class="btn btn-signin">Iniciar Sesión</a>
                    <a href="login.php?tab=register" class="btn btn-register">Crear Cuenta</a>
                  </div>
                  <div class="account-panel-links">
                    <a href="orders.php"><i class="bi bi-box-seam"></i><span>Mis Pedidos</span></a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div><!-- /header-actions -->

        </div>
      </div>
    </div><!-- /header-bar -->

    <!-- Nav -->
    <div class="header-nav">
      <div class="container-fluid container-xl position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="about.php">Acerca de</a></li>
            <li><a href="category.php">Categoría</a></li>
            <li><a href="servicios.php" class="active">Servicios</a></li>
            <li><a href="checkout.php">Finalizar Compra</a></li>
            <li><a href="contact.php">Contacto</a></li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Mobile Search -->
    <div class="collapse" id="mobileSearch">
      <div class="container-fluid container-xl">
        <form class="mobile-search-form">
          <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Buscar servicios...">
          </div>
        </form>
      </div>
    </div>
  </header>
  <!-- /header -->

  <!-- ── HERO ── -->
  <section class="servicios-hero">
    <div class="hero-line"></div>
    <h1>Nuestros <em>Servicios</em></h1>
    <p>Belleza con propósito. Cada servicio diseñado para ti.</p>
  </section>

  <!-- ── BARRA DE FILTROS (JS-driven) ── -->
  <div class="filtros-wrap">
    <div class="container">
      <div class="filtro-count">
        Mostrando <span id="count-visible">0</span> servicios
      </div>
      <div class="filtros-tags">
        <button class="filtro-tag activo" data-filter="todos">Todos</button>
        <button class="filtro-tag" data-filter="economico">Hasta $50.000</button>
        <button class="filtro-tag" data-filter="medio">$50.000 – $100.000</button>
        <button class="filtro-tag" data-filter="premium">Más de $100.000</button>
      </div>
      <select class="ordenar-select" id="ordenar">
        <option value="defecto">Ordenar por</option>
        <option value="precio-asc">Precio: menor a mayor</option>
        <option value="precio-desc">Precio: mayor a menor</option>
        <option value="duracion">Duración</option>
      </select>
    </div>
  </div>

  <!-- ── GRID DE SERVICIOS ── -->
  <section class="servicios-section">
    <div class="container">
      <div class="row g-4" id="grid-servicios">

        <?php
        $i = 0;
        if (mysqli_num_rows($resultado) > 0):
          while ($fila = mysqli_fetch_assoc($resultado)):
            $i++;
            $delay = ($i - 1) * 80; // ms

            // Ruta de imagen: si ya trae 'uploads/' o 'assets/' respetamos;
            // si no, asumimos que está en assets/img/servicios/
            $img_raw = trim($fila['imagen']);
            if (empty($img_raw)) {
              $img_src = '';
            } elseif (str_starts_with($img_raw, 'http') || str_starts_with($img_raw, '/') || str_starts_with($img_raw, 'assets/') || str_starts_with($img_raw, 'uploads/')) {
              $img_src = htmlspecialchars($img_raw);
            } else {
              $img_src = 'assets/img/servicios/' . htmlspecialchars($img_raw);
            }

            $precio_num = (int) $fila['precio'];
            if ($precio_num < 50000)       $rango = 'economico';
            elseif ($precio_num <= 100000) $rango = 'medio';
            else                            $rango = 'premium';
        ?>

        <div class="col-sm-6 col-lg-4 card-entry servicio-item"
             style="animation-delay: <?= $delay ?>ms"
             data-precio="<?= $precio_num ?>"
             data-duracion="<?= (int)$fila['duracion_min'] ?>"
             data-rango="<?= $rango ?>">

          <div class="card-sv">

            <div class="card-sv-img">
              <?php if (!empty($img_src)): ?>
                <img src="<?= $img_src ?>"
                     alt="<?= htmlspecialchars($fila['nombre']) ?>"
                     loading="lazy"
                     onerror="this.classList.add('error'); this.style.opacity='0';">
              <?php endif; ?>
              <!-- Fallback visible cuando la imagen falla o no existe -->
              <div class="img-fallback">
                <i class="bi bi-scissors"></i>
                <span><?= htmlspecialchars($fila['nombre']) ?></span>
              </div>
              <!-- Siempre visible -->
              <div class="badge-dur">
                <i class="bi bi-clock"></i>
                <?= (int)$fila['duracion_min'] ?> min
              </div>
            </div>

            <div class="card-sv-body">
              <h3 class="card-sv-nombre"><?= htmlspecialchars($fila['nombre']) ?></h3>
              <p class="card-sv-desc"><?= htmlspecialchars($fila['descripcion']) ?></p>
              <div class="card-sv-footer">
                <div class="card-sv-precio">
                  $<?= number_format($precio_num, 0, ',', '.') ?>
                  <span>COP</span>
                </div>
                <?php if(isset($_SESSION['logueado']) && $_SESSION['logueado']): ?>
                  <a href="citas.php?servicio=<?= (int)$fila['id'] ?>" class="btn-sv">
                    <i class="bi bi-calendar-check"></i> Reservar
                  </a>
                <?php else: ?>
                  <a href="login.php?redirect=servicios.php" class="btn-sv">
                    <i class="bi bi-calendar-check"></i> Reservar
                  </a>
                <?php endif; ?>
              </div>
            </div>

          </div>
        </div>

        <?php endwhile; else: ?>
        <!-- Sin servicios en BD -->
        <div class="col-12">
          <div class="empty-state">
            <i class="bi bi-scissors"></i>
            <h3>No hay servicios disponibles por ahora</h3>
            <p style="color:#7a6e6e; margin-top:8px;">Vuelve pronto, ¡estamos preparando algo especial para ti!</p>
          </div>
        </div>
        <?php endif; ?>

      </div><!-- /row -->

      <!-- Mensaje "sin resultados" para el filtro JS -->
      <div id="no-resultados" style="display:none;" class="empty-state">
        <i class="bi bi-funnel"></i>
        <h3>Ningún servicio coincide con este filtro</h3>
        <p style="color:#7a6e6e; margin-top:8px;">Prueba con otro rango de precio.</p>
      </div>

    </div>
  </section>

  <!-- Vendor JS -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {

    const items    = document.querySelectorAll('.servicio-item');
    const tags     = document.querySelectorAll('.filtro-tag');
    const ordenar  = document.getElementById('ordenar');
    const grid     = document.getElementById('grid-servicios');
    const noRes    = document.getElementById('no-resultados');
    const counter  = document.getElementById('count-visible');

    let filtroActual = 'todos';

    // ── Contar visibles ──
    function actualizarContador() {
      const vis = document.querySelectorAll('.servicio-item:not([style*="display: none"])').length;
      counter.textContent = vis;
    }

    // ── Filtrar ──
    function aplicarFiltro(filtro) {
      filtroActual = filtro;
      let alguno = false;
      items.forEach(item => {
        const muestra = filtro === 'todos' || item.dataset.rango === filtro;
        item.style.display = muestra ? '' : 'none';
        if (muestra) alguno = true;
      });
      noRes.style.display = alguno ? 'none' : 'block';
      actualizarContador();
    }

    // ── Ordenar ──
    function aplicarOrden(criterio) {
      const arr = Array.from(items);
      arr.sort((a, b) => {
        if (criterio === 'precio-asc')  return +a.dataset.precio   - +b.dataset.precio;
        if (criterio === 'precio-desc') return +b.dataset.precio   - +a.dataset.precio;
        if (criterio === 'duracion')    return +a.dataset.duracion  - +b.dataset.duracion;
        return 0;
      });
      arr.forEach(el => grid.appendChild(el));
    }

    // ── Eventos filtro ──
    tags.forEach(tag => {
      tag.addEventListener('click', () => {
        tags.forEach(t => t.classList.remove('activo'));
        tag.classList.add('activo');
        aplicarFiltro(tag.dataset.filter);
      });
    });

    // ── Evento ordenar ──
    ordenar.addEventListener('change', () => aplicarOrden(ordenar.value));

    // ── Fallback imágenes: mostrar placeholder cuando falla ──
    document.querySelectorAll('.card-sv-img img').forEach(img => {
      if (!img.complete || img.naturalWidth === 0) {
        img.classList.add('error');
        img.style.opacity = '0';
      }
      img.addEventListener('error', function () {
        this.classList.add('error');
        this.style.opacity = '0';
      });
    });

    // ── Inicializar contador ──
    actualizarContador();
  });
  </script>

</body>
</html>