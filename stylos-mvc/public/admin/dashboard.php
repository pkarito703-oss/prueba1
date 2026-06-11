<?php
session_start();
require_once __DIR__ . '/../../src/config/conexion.php';
require_once __DIR__ . '/../../src/controllers/CitaController.php';
require_once __DIR__ . '/../../src/controllers/UsuarioController.php';

// Solo admin puede entrar
if (!isset($_SESSION['logueado']) || $_SESSION['rol'] != 'admin') {
    header("Location: /public/login.php?error=Acceso denegado");
    exit();
}

$citaCtrl    = new CitaController($conexion);
$usuarioCtrl = new UsuarioController($conexion);

// Estadísticas
$total_usuarios   = $usuarioCtrl->contarTotal();
$total_citas      = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM citas"))['total'];
$citas_hoy        = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM citas WHERE fecha = CURDATE()"))['total'];
$citas_pendientes = $citaCtrl->contarPorEstado('pendiente');
$citas            = $citaCtrl->obtenerTodas();
$usuarios         = $usuarioCtrl->obtenerRecientes(5);

$pagina_activa = 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Mi Perfil Admin - Stylos Madys</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: #f4f6f9; }
    .sidebar { width:250px; height:100vh; background:#fff; position:fixed; top:0; left:0; z-index:100; overflow-y:auto; box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
    .sidebar-brand { padding:20px; border-bottom:1px solid #e8e0f0; text-align:center; }
    .sidebar-brand h4 { color:#7c3aed; font-weight:700; margin:0; font-size:1.1rem; }
    .sidebar-brand p { color:#555; font-size:0.8rem; margin:0; }
    .sidebar-menu { padding:15px 0; }
    .sidebar-menu a { display:flex; align-items:center; gap:12px; padding:12px 20px; color:#444; text-decoration:none; transition:all 0.3s; font-size:0.9rem; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background:#f5f3ff; color:#7c3aed; border-left:3px solid #7c3aed; }
    .sidebar-menu a i { font-size:1.1rem; width:20px; }
    .sidebar-menu .menu-title { padding:10px 20px 5px; color:#555; font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; }
    .main-content { margin-left:250px; min-height:100vh; }
    .top-navbar { background:#fff; padding:15px 25px; box-shadow:2px 0 10px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:99; }
    .top-navbar h5 { margin:0; font-weight:600; color:#333; }
    .admin-info { display:flex; align-items:center; gap:10px; }
    .admin-avatar { width:38px; height:38px; background:#7c3aed; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; }
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; padding:25px; }
    .stat-card { background:#fff; border-radius:12px; padding:20px; box-shadow:2px 0 10px rgba(0,0,0,0.05); display:flex; align-items:center; gap:15px; }
    .stat-icon { width:55px; height:55px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; }
    .stat-card h3 { font-size:1.8rem; font-weight:700; margin:0; color:#333; }
    .stat-card p { margin:0; color:#888; font-size:0.85rem; }
    .content-area { padding:0 25px 25px; }
    .content-card { background:#fff; border-radius:12px; box-shadow:2px 0 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:20px; }
    .content-card-header { padding:18px 20px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center; }
    .content-card-header h5 { margin:0; font-weight:600; color:#333; font-size:1rem; }
    .table th { font-weight:600; color:#555; font-size:0.85rem; }
    .table td { font-size:0.88rem; color:#444; vertical-align:middle; }
    .badge-estado { padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:500; }
    .btn-accion { padding:4px 10px; border-radius:6px; font-size:0.8rem; border:none; cursor:pointer; text-decoration:none; }
    @media(max-width:768px){ .sidebar{width:0;} .main-content{margin-left:0;} .stats-grid{grid-template-columns:repeat(2,1fr);} }
  </style>
</head>
<body>

  <?php include __DIR__ . '/../../src/views/layouts/header_admin.php'; ?>

  <div class="main-content">
    <div class="top-navbar">
      <h5><i class="bi bi-speedometer2 me-2" style="color:#7c3aed"></i>Mi perfil admin</h5>
      <div class="admin-info">
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
        <div>
          <div style="font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($_SESSION['nombre']) ?></div>
          <div style="color:#7c3aed; font-size:0.78rem;">Administrador</div>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;"><i class="bi bi-people-fill" style="color:#7c3aed;"></i></div>
        <div><h3><?= $total_usuarios ?></h3><p>Total Usuarios</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;"><i class="bi bi-calendar-check-fill" style="color:#28a745;"></i></div>
        <div><h3><?= $total_citas ?></h3><p>Total Citas</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#e3f2fd;"><i class="bi bi-calendar-day-fill" style="color:#2196f3;"></i></div>
        <div><h3><?= $citas_hoy ?></h3><p>Citas Hoy</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fff8e1;"><i class="bi bi-clock-fill" style="color:#ffc107;"></i></div>
        <div><h3><?= $citas_pendientes ?></h3><p>Citas Pendientes</p></div>
      </div>
    </div>

    <div class="content-area">
      <div class="row">
        <div class="col-lg-8">
          <div class="content-card">
            <div class="content-card-header">
              <h5><i class="bi bi-calendar-check me-2" style="color:#7c3aed"></i>Últimas Citas</h5>
              <a href="/public/admin/citas_admin.php" class="btn btn-sm" style="background:#7c3aed; color:#fff; border-radius:8px;">Ver todas</a>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead><tr><th>Cliente</th><th>Servicio</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Acción</th></tr></thead>
                <tbody>
                  <?php while($cita = mysqli_fetch_assoc($citas)): ?>
                  <tr>
                    <td><?= htmlspecialchars($cita['nombre_cliente']) ?></td>
                    <td><?= htmlspecialchars($cita['servicio']) ?></td>
                    <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                    <td><?= substr($cita['hora'], 0, 5) ?></td>
                    <td>
                      <span class="badge-estado" style="background:<?= $cita['estado']=='confirmada'?'#d4edda; color:#155724':($cita['estado']=='pendiente'?'#fff3cd; color:#856404':($cita['estado']=='cancelada'?'#f8d7da; color:#721c24':'#d1ecf1; color:#0c5460')) ?>">
                        <?= ucfirst($cita['estado']) ?>
                      </span>
                    </td>
                    <td>
                      <a href="/public/admin/citas_admin.php?confirmar=<?= $cita['id'] ?>" class="btn-accion" style="background:#d4edda; color:#155724;"><i class="bi bi-check"></i></a>
                      <a href="/public/admin/citas_admin.php?cancelar=<?= $cita['id'] ?>" class="btn-accion" style="background:#f8d7da; color:#721c24;"><i class="bi bi-x"></i></a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="content-card">
            <div class="content-card-header">
              <h5><i class="bi bi-people me-2" style="color:#7c3aed"></i>Nuevos Usuarios</h5>
            </div>
            <div style="padding:10px;">
              <?php while($u = mysqli_fetch_assoc($usuarios)): ?>
              <div class="d-flex align-items-center gap-3 p-2 mb-2" style="background:#f8f9fa; border-radius:8px;">
                <div style="width:38px; height:38px; background:#7c3aed; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; font-size:0.9rem; flex-shrink:0;">
                  <?= strtoupper(substr($u['nombres_usuarios'], 0, 1)) ?>
                </div>
                <div style="overflow:hidden;">
                  <div style="font-weight:600; font-size:0.88rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($u['nombres_usuarios'] . ' ' . $u['apellidos_usuarios']) ?></div>
                  <div style="color:#888; font-size:0.78rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($u['email_usuarios']) ?></div>
                </div>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
