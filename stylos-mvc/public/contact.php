<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Contacto - Stylos Madys | Cuidado Capilar</title>
  <meta name="description" content="Contáctanos en Stylos Madys para consultas sobre productos capilares, tratamientos o reserva de citas. Estamos en Aguachica, Cesar.">
  <meta name="keywords" content="contacto, cuidado capilar, productos para el cabello, agendamiento de citas">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/drift-zoom/drift-basic.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="contact-page">

  <header id="header" class="header position-relative">
    <!-- Header Bar -->
    <div class="header-bar">
      <div class="container-fluid container-xl">
        <div class="d-flex align-items-center justify-content-between">

          <!-- Logo -->
          <a href="index.html" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="Stylos Madys">
          </a>

          <!-- Search -->
          <form class="header-search d-none d-lg-flex">
            <input type="text" class="search-input" placeholder="Buscar productos, categorías, marcas...">
            <button class="search-btn" type="submit">
              <i class="bi bi-search"></i>
              <span class="d-none d-xl-inline">Buscar</span>
            </button>
          </form>

          <!-- Header Actions -->
          <div class="header-actions d-flex align-items-center">

            <!-- Mobile Search Toggle -->
            <button class="action-icon mobile-search-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false" aria-controls="mobileSearch">
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
      <!-- USUARIO LOGUEADO -->
      <div class="account-panel-header">
        <h6>¡Bienvenida, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h6>
        <p><?= htmlspecialchars($_SESSION['email']) ?></p>
      </div>
      <div class="account-panel-links">
        <a href="orders.php">
          <i class="bi bi-box-seam"></i>
          <span>Mis Pedidos</span>
        </a>
        <a href="citas.php">
          <i class="bi bi-calendar-check"></i>
          <span>Mis Citas</span>
        </a>
        <a href="logout.php" style="color: #e74c3c;">
          <i class="bi bi-box-arrow-right"></i>
          <span>Cerrar Sesión</span>
        </a>
      </div>

    <?php else: ?>
      <!-- USUARIO NO LOGUEADO -->
      <div class="account-panel-header">
        <h6>¡Bienvenido!</h6>
        <p>Ingresa para tener la mejor experiencia</p>
      </div>
      <div class="account-panel-auth">
        <a href="login.php" class="btn btn-signin">Iniciar Sesión</a>
        <a href="login.php?tab=register" class="btn btn-register">Crear Cuenta</a>
      </div>
      <div class="account-panel-links">
        <a href="orders.php">
          <i class="bi bi-box-seam"></i>
          <span>Mis Pedidos</span>
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>
            <!-- Cart -->
            <div class="dropdown cart-dropdown">
                <button class="action-icon" data-bs-toggle="dropdown" aria-label="Cart">
                  <i class="bi bi-cart3"></i>
                  <span class="count-dot">3</span>
                </button>
              <div class="dropdown-menu cart-panel">
                <div class="cart-panel-header">
                  <h6>Carrito de Compras</h6>
                  <span class="item-count">3 items</span>
                </div>
                <div class="cart-panel-body">
                  <!-- Cart Item 1 -->
                  <div class="cart-item">
                    <div class="cart-item-img">
                      <img src="assets/img/product/product-1.webp" alt="Product" class="img-fluid">
                    </div>
                    <div class="cart-item-info">
                      <h6>Shampoo Pro Crecimiento</h6>
                      <div class="cart-item-meta">
                        <span class="cart-item-price">$35.000</span>
                        <span class="cart-item-qty">Cant: 1</span>
                      </div>
                    </div>
                    <button class="cart-item-remove" aria-label="Remove item">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div><!-- End Cart Item -->

                  <!-- Cart Item 2 -->
                  <div class="cart-item">
                    <div class="cart-item-img">
                      <img src="assets/img/product/product-2.webp" alt="Product" class="img-fluid">
                    </div>
                    <div class="cart-item-info">
                      <h6>Acondicionador Botánico Romero</h6>
                      <div class="cart-item-meta">
                        <span class="cart-item-price">$34.400</span>
                        <span class="cart-item-qty">Cant: 1</span>
                      </div>
                    </div>
                    <button class="cart-item-remove" aria-label="Remove item">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div><!-- End Cart Item -->

                  <!-- Cart Item 3 -->
                  <div class="cart-item">
                    <div class="cart-item-img">
                      <img src="assets/img/product/product-3.webp" alt="Product" class="img-fluid">
                    </div>
                    <div class="cart-item-info">
                      <h6>Tratamiento de Rizos</h6>
                      <div class="cart-item-meta">
                        <span class="cart-item-price">$35.000</span>
                        <span class="cart-item-qty">Cant: 2</span>
                      </div>
                    </div>
                    <button class="cart-item-remove" aria-label="Remove item">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div><!-- End Cart Item -->
                </div>
                <div class="cart-panel-footer">
                  <div class="cart-subtotal">
                    <span>Subtotal</span>
                    <span class="cart-subtotal-price">$139.400</span>
                  </div>
                  <a href="checkout.php" class="btn btn-checkout">Finalizar Compra</a>
                  <a href="cart.php" class="btn-viewcart">Ver carrito completo →</a>
                </div>
              </div>
            </div>

            <!-- Mobile Navigation Toggle -->
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
            <li><a href="category.php">Categoría</a></li>
            <li><a href="detalle_producto.php">Detalles de Producto</a></li>            
            <li><a href="checkout.php">Finalizar Compra</a></li>
            <li><a href="contact.php">Contacto</a></li>
          </ul>
        </nav>
      </div>
    </div>

          
    <!-- Mobile Search Form -->
    <div class="collapse" id="mobileSearch">
      <div class="container-fluid container-xl">
        <form class="mobile-search-form">
          <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Buscar productos...">
          </div>
        </form>
      </div>
    </div>

  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Contacto</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Inicio</a></li>
            <li class="current">Contacto</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Contact 2 Section -->
    <section id="contact-2" class="contact-2 section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-0 contact-wrapper">

          <!-- Left Panel: Contact Details -->
          <div class="col-lg-4" data-aos="fade-right" data-aos-delay="150">
            <div class="details-panel">
              <h3>¡Inicia una Conversación!</h3>
              <p class="panel-intro">Estamos aquí para ayudarte con todas tus dudas sobre nuestros productos capilares, tratamientos o para agendar tu cita.</p>

              <div class="detail-item">
                <div class="detail-icon">
                  <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="detail-text">
                  <h5>Visítanos</h5>
                  <p>Calle 12 #14-69 - Barrio Higueron, Aguachica, Cesar</p>
                </div>
              </div><!-- End Detail Item -->

              <div class="detail-item">
                <div class="detail-icon">
                  <i class="bi bi-envelope-fill"></i>
                </div>
                <div class="detail-text">
                  <h5>Escríbenos</h5>
                  <p>stylosmadys@gmail.com</p>
                </div>
              </div><!-- End Detail Item -->

              <div class="detail-item">
                <div class="detail-icon">
                  <i class="bi bi-clock-fill"></i>
                </div>
                <div class="detail-text">
                  <h5>Horario de Atención</h5>
                  <p>Lunes - Viernes: 9am - 6pm</p>
                  <p>Sábado: 10am - 4pm</p>
                  <p>Domingo: Cerrado</p>
                </div>
              </div><!-- End Detail Item -->

              <div class="social-links">
                <a href="https://www.facebook.com/nelly.garaylarios" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/stylosmadys/" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="https://www.tiktok.com/@dylan34_3?lang=es" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
              </div>
            </div>
          </div><!-- End Left Panel -->

          <!-- Right Panel: Contact Form -->
          <div class="col-lg-8" data-aos="fade-left" data-aos-delay="250">
            <div class="form-panel">
              <h3>Envíanos un Mensaje</h3>
              <p class="form-subtitle">Déjanos tu mensaje y te responderemos a la brevedad posible. ¡Tu cabello nos importa!</p>

              <form action="forms/contact.php" method="post" class="php-email-form">
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="form-label">Tu Nombre</label>
                    <input type="text" class="form-control" name="name" placeholder="Nombre completo" required="" autocomplete="name">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" name="email" placeholder="tucorreo@ejemplo.com" required="" autocomplete="email">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Número de Teléfono</label>
                    <input type="tel" class="form-control" name="phone" placeholder="+57 300 000 0000" autocomplete="tel">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Asunto</label>
                    <input type="text" class="form-control" name="subject" placeholder="¿Cómo podemos ayudarte?" required="">
                  </div>

                  <div class="col-12">
                    <label class="form-label">Tu Mensaje</label>
                    <textarea class="form-control" name="message" rows="6" placeholder="Describe tu consulta en detalle..." required=""></textarea>
                  </div>

                  <div class="col-12">
                    <div class="loading">Cargando</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Tu mensaje ha sido enviado. ¡Gracias por contactarnos!</div>
                  </div>

                  <div class="col-12">
                    <button type="submit" class="btn-send">
                      <span>Enviar Mensaje</span>
                      <i class="bi bi-arrow-right"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div><!-- End Right Panel -->

        </div><!-- End Contact Wrapper -->

      </div>

      <!-- Map Strip - CON EL MAPA QUE PROPORCIONASTE -->
      <div class="map-strip" data-aos="zoom-in" data-aos-delay="200">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2832.4887733048536!2d-73.63101733498573!3d8.302187389035629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e5d856816abc235%3A0xee98f7941165186a!2sCl.%2012%20%23%206-14%2C%20Aguachica%2C%20Cesar!5e1!3m2!1ses!2sco!4v1780157033454!5m2!1ses!2sco" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>

    </section><!-- /Contact 2 Section -->

  </main>

  <footer id="footer" class="footer dark-background">
    <div class="footer-main">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6">
            <div class="footer-widget footer-about">
              <a href="index.html" class="logo">
                <span class="sitename">Stylos Madys</span>
              </a>
              <p>En nuestro centro de salud y cuidado capilar nos enfocamos en revitalizar tu cuero cabelludo mientras disfrutas de una experiencia de relajación única. Cada tratamiento está diseñado para ayudarte a liberar el estrés, mejorar la salud de tu cabello y resaltar tu belleza natural.</p>

              <div class="social-links mt-4">
                <h5>Conéctate Con Nosotros</h5>
                <div class="social-icons">
                  <a href="https://www.facebook.com/nelly.garaylarios" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                  <a href="https://www.instagram.com/stylosmadys/" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                  <a href="https://www.tiktok.com/@dylan34_3?lang=es" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-widget">
              <h4>Tienda</h4>
              <ul class="footer-links">
                <li><a href="category.html">✨ Productos</a></li>
                <li><a href="category.html">🌿 Cuidado Capilar</a></li>
                <li><a href="category.html">💆‍♀️ Servicios</a></li>
                <li><a href="category.html">🧴 Nuestra Tienda</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-widget">
              <h4>Soporte</h4>
              <ul class="footer-links">
                <li><a href="support.html">💬 Ayuda</a></li>
                <li><a href="account.html">🚚 Seguimiento</a></li>
                <li><a href="shiping-info.html">📦 Envíos</a></li>
                <li><a href="return-policy.html">🔄 Cambios y Garantías</a></li>
                <li><a href="contact.html">📲 Reserva tu Cita</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="footer-widget">
              <h4>Información de Contacto</h4>
              <div class="footer-contact">
                <div class="contact-item">
                  <i class="bi bi-geo-alt"></i>
                  <span>Calle 12 #14-69 - Barrio Higueron, Aguachica, Cesar</span>
                </div>
                <div class="contact-item">
                  <i class="bi bi-telephone"></i>
                  <span>+57 310 491 6849</span>
                </div>
                <div class="contact-item">
                  <i class="bi bi-person"></i>
                  <span>@nellygaraylarios</span>
                </div>
                <div class="contact-item">
                  <i class="bi bi-clock"></i>
                  <span>Lunes-Viernes: 9am-6pm<br>Sábado: 10am-4pm<br>Domingo: Cerrado</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <div class="row gy-3 align-items-center">
          <div class="col-lg-6 col-md-12">
            <div class="copyright">
              <p>© <span>Copyright</span> <strong class="sitename">Stylos Madys</strong>. Todos los derechos reservados.</p>
            </div>
            <div class="credits mt-1">
              Diseñado por <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
          </div>

          <div class="col-lg-6 col-md-12">
            <div class="d-flex flex-wrap justify-content-lg-end justify-content-center align-items-center gap-4">
              <div class="payment-methods">
                <div class="payment-icons">
                  <i class="bi bi-credit-card" aria-label="Tarjeta de Crédito"></i>
                  <i class="bi bi-paypal" aria-label="PayPal"></i>
                  <i class="bi bi-apple" aria-label="Apple Pay"></i>
                  <i class="bi bi-google" aria-label="Google Pay"></i>
                  <i class="bi bi-shop" aria-label="Shop Pay"></i>
                  <i class="bi bi-cash" aria-label="Contra Entrega"></i>
                </div>
              </div>

              <div class="legal-links">
                <a href="tos.html">Términos</a>
                <a href="privacy.html">Privacidad</a>
                <a href="tos.html">Cookies</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/drift-zoom/Drift.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>