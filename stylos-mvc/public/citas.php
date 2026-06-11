<?php
session_start();
require_once __DIR__ . '/../src/config/conexion.php';

// Si no está logueado, redirigir al login
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header("Location: login.php?error=Debes iniciar sesión para reservar una cita");
    exit();
}

// Obtener servicios activos
$sql_servicios = "SELECT id, nombre, precio, duracion_min FROM servicios WHERE activo = 1 ORDER BY nombre";
$resultado_servicios = mysqli_query($conexion, $sql_servicios);

$error  = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$exito  = isset($_GET['exito']) ? htmlspecialchars($_GET['exito']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Reservar Cita - Stylos Madys</title>
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

    .cita-container {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 30px rgba(0,0,0,0.08);
      padding: 40px;
      margin-bottom: 40px;
    }

    .cita-header { text-align: center; margin-bottom: 35px; }
    .cita-header h3 { font-size: 1.6rem; font-weight: 700; color: #2c2c2c; margin-bottom: 6px; }
    .cita-header p { color: #888; font-size: 0.95rem; }

    .form-label { font-weight: 500; color: #444; margin-bottom: 6px; }

    .form-control, .form-select {
      border: 1.5px solid #e0d6cc;
      border-radius: 10px;
      padding: 12px 15px;
      font-size: 0.95rem;
      transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
      border-color: #c8956c;
      box-shadow: 0 0 0 3px rgba(200,149,108,0.15);
    }

    .servicio-card {
      border: 2px solid #e0d6cc;
      border-radius: 12px;
      padding: 15px;
      cursor: pointer;
      transition: all 0.3s;
      margin-bottom: 10px;
    }
    .servicio-card:hover { border-color: #c8956c; background: #fdf8f5; }
    .servicio-card.selected { border-color: #c8956c; background: #fdf8f5; }
    .servicio-card h6 { font-weight: 600; color: #333; margin-bottom: 4px; }
    .servicio-card .precio { color: #c8956c; font-weight: 600; }
    .servicio-card .duracion { color: #888; font-size: 0.85rem; }

    .btn-reservar {
      background: #c8956c;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 14px;
      font-size: 1rem;
      font-weight: 600;
      width: 100%;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .btn-reservar:hover { background: #b07d55; transform: translateY(-1px); }

    .info-box {
      background: #fdf8f5;
      border: 1px solid #e8d5c4;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }
    .info-box h5 { color: #c8956c; font-weight: 600; margin-bottom: 10px; }
    .info-box ul { list-style: none; padding: 0; margin: 0; }
    .info-box ul li { padding: 5px 0; color: #666; font-size: 0.9rem; }
    .info-box ul li i { color: #c8956c; margin-right: 8px; }

    /* Header simple */
    .header { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.06); padding: 15px 0; }
    .logo img { height: 45px; }

    @media (max-width: 576px) {
      .cita-container { padding: 25px 18px; }
    }
  </style>
</head>
<body>

  <header id="header" class="header position-relative">
    <div class="container-fluid container-xl">
      <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
          <img src="assets/img/logo.png" alt="Stylos Madys">
        </a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i> Volver al inicio
        </a>
      </div>
    </div>
  </header>

  <main class="main">

    <div class="page-title">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Reservar Cita 💆‍♀️</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Inicio</a></li>
            <li class="current">Reservar Cita</li>
          </ol>
        </nav>
      </div>
    </div>

    <section class="section" style="padding: 0 0 60px;">
      <div class="container">

        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if ($exito): ?>
          <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="bi bi-check-circle me-2"></i><?= $exito ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <div class="row">

          <!-- Formulario -->
          <div class="col-lg-7">
            <div class="cita-container">
              <div class="cita-header">
                <h3>Agenda tu cita ✨</h3>
                <p>Selecciona el servicio, fecha y hora que prefieras</p>
              </div>

              <form action="procesar_cita.php" method="POST">

                <!-- Datos personales -->
                <div class="row mb-3">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre_cliente" class="form-control" 
                      value="<?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="telefono" class="form-control" placeholder="310 000 0000" required>
                  </div>
                </div>

                <!-- Selección de servicio -->
                <div class="mb-3">
                  <label class="form-label">Selecciona el servicio</label>
                  <select name="servicio_id" id="servicioSelect" class="form-select" required onchange="mostrarInfo(this)">
                    <option value="">-- Elige un servicio --</option>
                    <?php while($s = mysqli_fetch_assoc($resultado_servicios)): ?>
                      <option value="<?= $s['id'] ?>" 
                              data-precio="<?= number_format($s['precio'], 0, ',', '.') ?>"
                              data-duracion="<?= $s['duracion_min'] ?>">
                        <?= htmlspecialchars($s['nombre']) ?> — $<?= number_format($s['precio'], 0, ',', '.') ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <!-- Info del servicio seleccionado -->
                <div id="servicioInfo" class="info-box" style="display:none;">
                  <h5><i class="bi bi-scissors"></i> <span id="infoNombre"></span></h5>
                  <ul>
                    <li><i class="bi bi-currency-dollar"></i> Precio: <strong>$<span id="infoPrecio"></span></strong></li>
                    <li><i class="bi bi-clock"></i> Duración aproximada: <strong><span id="infoDuracion"></span> minutos</strong></li>
                  </ul>
                </div>

                <!-- Fecha y hora -->
                <div class="row mb-3">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" id="fechaInput" class="form-control" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Hora</label>
                    <select name="hora" class="form-select" required>
                      <option value="">-- Selecciona hora --</option>
                      <option value="09:00">9:00 AM</option>
                      <option value="09:30">9:30 AM</option>
                      <option value="10:00">10:00 AM</option>
                      <option value="10:30">10:30 AM</option>
                      <option value="11:00">11:00 AM</option>
                      <option value="11:30">11:30 AM</option>
                      <option value="12:00">12:00 PM</option>
                      <option value="14:00">2:00 PM</option>
                      <option value="14:30">2:30 PM</option>
                      <option value="15:00">3:00 PM</option>
                      <option value="15:30">3:30 PM</option>
                      <option value="16:00">4:00 PM</option>
                      <option value="16:30">4:30 PM</option>
                      <option value="17:00">5:00 PM</option>
                    </select>
                  </div>
                </div>

                <!-- Notas -->
                <div class="mb-4">
                  <label class="form-label">Notas adicionales (opcional)</label>
                  <textarea name="notas" class="form-control" rows="3" 
                    placeholder="Ej: cabello largo, alergias, preferencias especiales..."></textarea>
                </div>

                <button type="submit" class="btn-reservar">
                  <i class="bi bi-calendar-check"></i> Confirmar Reserva
                </button>

              </form>
            </div>
          </div>

          <!-- Info lateral -->
          <div class="col-lg-5">
            <div class="cita-container">
              <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-warning me-2"></i>Información importante</h5>
              <ul class="list-unstyled">
                <li class="mb-3"><i class="bi bi-clock" style="color:#c8956c"></i> <strong>Horario de atención:</strong><br>
                  <span class="text-muted ms-3">Lunes - Viernes: 9am - 6pm<br>
                  <span class="ms-3">Sábado: 10am - 4pm</span></span>
                </li>
                <li class="mb-3"><i class="bi bi-geo-alt" style="color:#c8956c"></i> <strong>Dirección:</strong><br>
                  <span class="text-muted ms-3">Calle 12 #14-69, Barrio Higuerón<br>
                  <span class="ms-3">Aguachica, Cesar</span></span>
                </li>
                <li class="mb-3"><i class="bi bi-telephone" style="color:#c8956c"></i> <strong>Teléfono:</strong><br>
                  <span class="text-muted ms-3">+57 310 491 6849</span>
                </li>
                <li class="mb-3"><i class="bi bi-exclamation-circle" style="color:#c8956c"></i> <strong>Política de cancelación:</strong><br>
                  <span class="text-muted ms-3">Cancela con mínimo 2 horas de anticipación</span>
                </li>
              </ul>
            </div>

            <!-- Mis citas recientes -->
            <?php
            $sql_miscitas = "SELECT c.fecha, c.hora, c.estado, s.nombre as servicio 
                            FROM citas c 
                            JOIN servicios s ON c.servicio_id = s.id
                            WHERE c.usuario_id = ? 
                            ORDER BY c.fecha DESC, c.hora DESC 
                            LIMIT 3";
            $stmt = mysqli_prepare($conexion, $sql_miscitas);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_usuario']);
            mysqli_stmt_execute($stmt);
            $miscitas = mysqli_stmt_get_result($stmt);
            $total_citas = mysqli_num_rows($miscitas);
            ?>

            <?php if($total_citas > 0): ?>
            <div class="cita-container mt-3">
              <h5 class="fw-bold mb-3"><i class="bi bi-calendar2-check" style="color:#c8956c"></i> Mis últimas citas</h5>
              <?php while($cita = mysqli_fetch_assoc($miscitas)): ?>
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" 
                     style="background:#f8f5f2; border-radius:8px;">
                  <div>
                    <strong style="font-size:0.9rem;"><?= htmlspecialchars($cita['servicio']) ?></strong><br>
                    <small class="text-muted"><?= date('d/m/Y', strtotime($cita['fecha'])) ?> — <?= substr($cita['hora'], 0, 5) ?></small>
                  </div>
                  <span class="badge" style="background:
                    <?= $cita['estado'] == 'confirmada' ? '#28a745' : 
                       ($cita['estado'] == 'pendiente' ? '#ffc107' : 
                       ($cita['estado'] == 'cancelada' ? '#dc3545' : '#6c757d')) ?>">
                    <?= ucfirst($cita['estado']) ?>
                  </span>
                </div>
              <?php endwhile; ?>
            </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </section>

  </main>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    // Fecha mínima = mañana
    const hoy = new Date();
    hoy.setDate(hoy.getDate() + 1);
    const fechaMin = hoy.toISOString().split('T')[0];
    document.getElementById('fechaInput').min = fechaMin;

    // No permitir domingos
    document.getElementById('fechaInput').addEventListener('change', function() {
      const dia = new Date(this.value + 'T00:00:00').getDay();
      if (dia === 0) {
        alert('Lo sentimos, los domingos estamos cerrados. Por favor elige otro día.');
        this.value = '';
      }
    });

    // Mostrar info del servicio seleccionado
    function mostrarInfo(select) {
      const option = select.options[select.selectedIndex];
      const info = document.getElementById('servicioInfo');
      if (select.value) {
        document.getElementById('infoNombre').textContent = option.text.split(' —')[0];
        document.getElementById('infoPrecio').textContent = option.dataset.precio;
        document.getElementById('infoDuracion').textContent = option.dataset.duracion;
        info.style.display = 'block';
      } else {
        info.style.display = 'none';
      }
    }
  </script>

</body>
</html>