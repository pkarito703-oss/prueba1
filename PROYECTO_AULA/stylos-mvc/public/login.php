<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login - Stylos Madys</title>
  <link href="assets/img/favicon.png" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f8f5f2; }

    .page-title {
      padding: 20px 0;
      background: #f8f5f2;
      border-bottom: 1px solid #e8e0d8;
      margin-bottom: 40px;
    }
    .page-title h1 { font-size: 1.5rem; font-weight: 600; color: #333; }
    .breadcrumbs ol { display: flex; list-style: none; padding: 0; margin: 0; gap: 8px; }
    .breadcrumbs ol li a { color: #c8956c; text-decoration: none; }
    .breadcrumbs ol li.current { color: #888; }
    .breadcrumbs ol li:not(:last-child)::after { content: "/"; margin-left: 8px; color: #ccc; }

    .login.section { padding: 20px 0 60px; }

    .auth-container {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 30px rgba(0,0,0,0.08);
      overflow: hidden;
      padding: 40px;
    }

    .auth-form { display: none; }
    .auth-form.active { display: block; }

    .form-header { text-align: center; margin-bottom: 30px; }
    .form-header h3 { font-size: 1.6rem; font-weight: 700; color: #2c2c2c; margin-bottom: 6px; }
    .form-header p { color: #888; font-size: 0.95rem; }

    .input-group { position: relative; display: flex; align-items: center; border: 1.5px solid #e0d6cc; border-radius: 10px; overflow: hidden; background: #fff; margin-bottom: 16px; }
    .input-group:focus-within { border-color: #c8956c; box-shadow: 0 0 0 3px rgba(200,149,108,0.15); }
    .input-icon { padding: 0 14px; color: #c8956c; font-size: 1rem; }
    .input-group .form-control { border: none; box-shadow: none; padding: 12px 10px; font-size: 0.95rem; background: transparent; }
    .input-group .form-control:focus { box-shadow: none; border: none; outline: none; }
    .password-toggle { padding: 0 14px; cursor: pointer; color: #aaa; }
    .password-toggle:hover { color: #c8956c; }

    .name-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 0; }

    .form-options { display: flex; justify-content: space-between; align-items: center; }
    .remember-me { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #666; }
    .forgot-password { font-size: 0.9rem; color: #c8956c; text-decoration: none; }
    .forgot-password:hover { text-decoration: underline; }

    .auth-btn {
      width: 100%;
      padding: 13px;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .primary-btn { background: #c8956c; color: #fff; }
    .primary-btn:hover { background: #b07d55; transform: translateY(-1px); }

    .terms-check { display: flex; align-items: flex-start; gap: 10px; font-size: 0.88rem; color: #666; }
    .terms-check a { color: #c8956c; text-decoration: none; }
    .terms-check input { margin-top: 3px; accent-color: #c8956c; }

    .switch-form { text-align: center; margin-top: 20px; font-size: 0.92rem; color: #888; }
    .switch-btn { background: none; border: none; color: #c8956c; font-weight: 600; cursor: pointer; font-size: 0.92rem; padding: 0; margin-left: 6px; }
    .switch-btn:hover { text-decoration: underline; }

    /* Header simple */
    .header { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.06); padding: 15px 0; }
    .logo img { height: 45px; }

    @media (max-width: 576px) {
      .auth-container { padding: 25px 18px; }
      .name-row { grid-template-columns: 1fr; }
    }
  </style>
</head>

<body class="login-page">

<?php
$tab_activo = isset($_GET['tab']) && $_GET['tab'] == 'register' ? 'register' : 'login';
$error      = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$exito      = isset($_GET['exito']) ? htmlspecialchars($_GET['exito']) : '';
?>

  <header id="header" class="header position-relative">
    <div class="container-fluid container-xl">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="Stylos Madys">
      </a>
    </div>
  </header>

  <main class="main">

    <div class="page-title">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Mi Cuenta</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Inicio</a></li>
            <li class="current">Login</li>
          </ol>
        </nav>
      </div>
    </div>

    <section id="login" class="login section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 col-md-8">

            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if ($exito): ?>
              <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= $exito ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <div class="auth-container">

              <!-- LOGIN FORM -->
              <div class="auth-form login-form <?= $tab_activo == 'login' ? 'active' : '' ?>">
                <div class="form-header">
                  <h3>¡Bienvenida de nuevo! 💆‍♀️</h3>
                  <p>Inicia sesión en tu cuenta</p>
                </div>
                <form action="procesar_login.php" method="POST">
                  <div class="input-group">
                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                  </div>
                  <div class="input-group">
                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passLogin" class="form-control" placeholder="Contraseña" required>
                    <span class="password-toggle" onclick="togglePass('passLogin', this)"><i class="bi bi-eye"></i></span>
                  </div>
                  <div class="form-options mb-4">
                    <div class="remember-me">
                      <input type="checkbox" id="rememberLogin">
                      <label for="rememberLogin">Recordarme</label>
                    </div>
                    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                  </div>
                  <button type="submit" class="auth-btn primary-btn mb-3">
                    Iniciar Sesión <i class="bi bi-arrow-right"></i>
                  </button>
                  <div class="switch-form">
                    <span>¿No tienes cuenta?</span>
                    <button type="button" class="switch-btn" onclick="cambiarTab('register')">Crear cuenta</button>
                  </div>
                </form>
              </div>

              <!-- REGISTER FORM -->
              <div class="auth-form register-form <?= $tab_activo == 'register' ? 'active' : '' ?>">
                <div class="form-header">
                  <h3>Crear Cuenta ✨</h3>
                  <p>Únete hoy y empieza a disfrutar</p>
                </div>
                <form action="procesar_registro.php" method="POST">
                  <div class="name-row">
                    <div class="input-group">
                      <span class="input-icon"><i class="bi bi-person"></i></span>
                      <input type="text" name="nombres" class="form-control" placeholder="Nombres" required>
                    </div>
                    <div class="input-group">
                      <span class="input-icon"><i class="bi bi-person"></i></span>
                      <input type="text" name="apellidos" class="form-control" placeholder="Apellidos" required>
                    </div>
                  </div>
                  <div class="input-group">
                    <span class="input-icon"><i class="bi bi-telephone"></i></span>
                    <input type="tel" name="telefono" class="form-control" placeholder="Teléfono">
                  </div>
                  <div class="input-group">
                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                  </div>
                  <div class="input-group">
                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passReg1" class="form-control" placeholder="Crear contraseña" required>
                    <span class="password-toggle" onclick="togglePass('passReg1', this)"><i class="bi bi-eye"></i></span>
                  </div>
                  <div class="input-group">
                    <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirmar" id="passReg2" class="form-control" placeholder="Confirmar contraseña" required>
                    <span class="password-toggle" onclick="togglePass('passReg2', this)"><i class="bi bi-eye"></i></span>
                  </div>
                  <div class="terms-check mb-4 mt-2">
                    <input type="checkbox" id="termsRegister" required>
                    <label for="termsRegister">Acepto los <a href="#">Términos</a> y <a href="#">Política de Privacidad</a></label>
                  </div>
                  <button type="submit" class="auth-btn primary-btn mb-3">
                    Crear Cuenta <i class="bi bi-arrow-right"></i>
                  </button>
                  <div class="switch-form">
                    <span>¿Ya tienes cuenta?</span>
                    <button type="button" class="switch-btn" onclick="cambiarTab('login')">Iniciar sesión</button>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    function cambiarTab(tab) {
      document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
      document.querySelector('.' + tab + '-form').classList.add('active');
    }
    function togglePass(id, btn) {
      const input = document.getElementById(id);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }
  </script>

</body>
</html>