<?php
session_start();
require_once __DIR__ . '/../../src/config/conexion.php';
require_once __DIR__ . '/../../src/controllers/CitaController.php';

if (!isset($_SESSION['logueado']) || $_SESSION['rol'] != 'admin') {
    header("Location: /public/login.php?error=Acceso denegado");
    exit();
}

$citaCtrl = new CitaController($conexion);

if (isset($_GET['confirmar'])) { $citaCtrl->confirmar($_GET['confirmar']); header("Location: citas_admin.php?exito=Cita confirmada"); exit(); }
if (isset($_GET['cancelar']))  { $citaCtrl->cancelar($_GET['cancelar']);  header("Location: citas_admin.php?exito=Cita cancelada");  exit(); }
if (isset($_GET['completar'])) { $citaCtrl->completar($_GET['completar']); header("Location: citas_admin.php?exito=Cita completada"); exit(); }

$filtro   = $_GET['filtro'] ?? 'todas';
$citas    = $citaCtrl->obtenerTodas($filtro);
$pendientes  = $citaCtrl->contarPorEstado('pendiente');
$confirmadas = $citaCtrl->contarPorEstado('confirmada');
$completadas = $citaCtrl->contarPorEstado('completada');
$canceladas  = $citaCtrl->contarPorEstado('cancelada');
$exito    = isset($_GET['exito']) ? htmlspecialchars($_GET['exito']) : '';
$pagina_activa = 'citas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Gestionar Citas - Stylos Madys</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <style>
    * { font-family:'Poppins',sans-serif; } body { background:#f4f6f9; }
    .sidebar { width:250px; height:100vh; background:#fff; position:fixed; top:0; left:0; z-index:100; overflow-y:auto; box-shadow:2px 0 10px rgba(0,0,0,0.05); }
    .sidebar-brand { padding:20px; border-bottom:1px solid #e8e0f0; text-align:center; }
    .sidebar-brand h4 { color:#7c3aed; font-weight:700; margin:0; font-size:1.1rem; }
    .sidebar-brand p { color:#666; font-size:0.8rem; margin:0; }
    .sidebar-menu { padding:15px 0; }
    .sidebar-menu a { display:flex; align-items:center; gap:12px; padding:12px 20px; color:#444; text-decoration:none; transition:all 0.3s; font-size:0.9rem; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background:#f5f3ff; color:#7c3aed; border-left:3px solid #7c3aed; }
    .sidebar-menu a i { font-size:1.1rem; width:20px; }
    .sidebar-menu .menu-title { padding:10px 20px 5px; color:#555; font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; }
    .main-content { margin-left:250px; min-height:100vh; }
    .top-navbar { background:#fff; padding:15px 25px; box-shadow:0 2px 10px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:99; }
    .top-navbar h5 { margin:0; font-weight:600; color:#333; }
    .admin-info { display:flex; align-items:center; gap:10px; }
    .admin-avatar { width:38px; height:38px; background:#7c3aed; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; }
    .content-area { padding:25px; }
    .filter-tabs { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
    .filter-tab { padding:8px 18px; border-radius:20px; text-decoration:none; font-size:0.85rem; font-weight:500; transition:all 0.3s; border:2px solid #e0e0e0; color:#666; background:#fff; }
    .filter-tab:hover { border-color:#7c3aed; color:#7c3aed; }
    .filter-tab.active { background:#7c3aed; color:#fff; border-color:#7c3aed; }
    .stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:15px; margin-bottom:25px; }
    .stat-mini { background:#fff; border-radius:10px; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,0.05); text-align:center; }
    .stat-mini h4 { font-size:1.6rem; font-weight:700; margin:0; }
    .stat-mini p { margin:0; font-size:0.8rem; color:#888; }
    .content-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; }
    .content-card-header { padding:18px 20px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center; }
    .content-card-header h5 { margin:0; font-weight:600; color:#333; font-size:1rem; }
    .table th { font-weight:600; color:#555; font-size:0.85rem; }
    .table td { font-size:0.88rem; color:#444; vertical-align:middle; }
    .badge-estado { padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:500; }
    .btn-accion { padding:5px 10px; border-radius:6px; font-size:0.8rem; border:none; cursor:pointer; text-decoration:none; margin:1px; display:inline-block; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/../../src/views/layouts/header_admin.php'; ?>

  <div class="main-content">
    <div class="top-navbar">
      <h5><i class="bi bi-calendar-check me-2" style="color:#7c3aed"></i>Gestionar Citas</h5>
      <div class="admin-info">
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
        <div>
          <div style="font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($_SESSION['nombre']) ?></div>
          <div style="color:#7c3aed; font-size:0.78rem;">Administrador</div>
        </div>
      </div>
    </div>

    <div class="content-area">
      <?php if ($exito): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3">
          <i class="bi bi-check-circle me-2"></i><?= $exito ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="stats-row">
        <div class="stat-mini"><h4 style="color:#ffc107"><?= $pendientes ?></h4><p>Pendientes</p></div>
        <div class="stat-mini"><h4 style="color:#28a745"><?= $confirmadas ?></h4><p>Confirmadas</p></div>
        <div class="stat-mini"><h4 style="color:#7c3aed"><?= $completadas ?></h4><p>Completadas</p></div>
        <div class="stat-mini"><h4 style="color:#dc3545"><?= $canceladas ?></h4><p>Canceladas</p></div>
      </div>

      <div class="filter-tabs">
        <a href="citas_admin.php?filtro=todas"     class="filter-tab <?= $filtro=='todas'    ?'active':'' ?>">Todas</a>
        <a href="citas_admin.php?filtro=pendiente"  class="filter-tab <?= $filtro=='pendiente' ?'active':'' ?>">Pendientes</a>
        <a href="citas_admin.php?filtro=confirmada" class="filter-tab <?= $filtro=='confirmada'?'active':'' ?>">Confirmadas</a>
        <a href="citas_admin.php?filtro=completada" class="filter-tab <?= $filtro=='completada'?'active':'' ?>">Completadas</a>
        <a href="citas_admin.php?filtro=cancelada"  class="filter-tab <?= $filtro=='cancelada' ?'active':'' ?>">Canceladas</a>
      </div>

      <div class="content-card">
        <div class="content-card-header">
          <h5><i class="bi bi-calendar-check me-2" style="color:#7c3aed"></i>Lista de Citas</h5>
          <span class="badge" style="background:#7c3aed"><?= mysqli_num_rows($citas) ?> citas</span>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr><th>#</th><th>Cliente</th><th>Teléfono</th><th>Servicio</th><th>Precio</th><th>Fecha</th><th>Hora</th><th>Notas</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
              <?php while($c = mysqli_fetch_assoc($citas)): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><strong><?= htmlspecialchars($c['nombre_cliente']) ?></strong></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['servicio']) ?></td>
                <td>$<?= number_format($c['precio'], 0, ',', '.') ?></td>
                <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                <td><?= substr($c['hora'], 0, 5) ?></td>
                <td><small class="text-muted"><?= $c['notas'] ? htmlspecialchars(substr($c['notas'],0,30)).'...' : '—' ?></small></td>
                <td>
                  <span class="badge-estado" style="background:<?= $c['estado']=='confirmada'?'#d4edda; color:#155724':($c['estado']=='pendiente'?'#fff3cd; color:#856404':($c['estado']=='cancelada'?'#f8d7da; color:#721c24':'#e2d9f3; color:#4a1d96')) ?>">
                    <?= ucfirst($c['estado']) ?>
                  </span>
                </td>
                <td>
                  <?php if($c['estado']=='pendiente'): ?>
                    <a href="citas_admin.php?confirmar=<?= $c['id'] ?>&filtro=<?= $filtro ?>" class="btn-accion" style="background:#d4edda; color:#155724;"><i class="bi bi-check-lg"></i> Confirmar</a>
                  <?php endif; ?>
                  <?php if($c['estado']=='confirmada'): ?>
                    <a href="citas_admin.php?completar=<?= $c['id'] ?>&filtro=<?= $filtro ?>" class="btn-accion" style="background:#e2d9f3; color:#4a1d96;"><i class="bi bi-check-all"></i> Completar</a>
                  <?php endif; ?>
                  <?php if($c['estado']!='cancelada' && $c['estado']!='completada'): ?>
                    <a href="citas_admin.php?cancelar=<?= $c['id'] ?>&filtro=<?= $filtro ?>" class="btn-accion" style="background:#f8d7da; color:#721c24;" onclick="return confirm('¿Cancelar esta cita?')"><i class="bi bi-x-lg"></i> Cancelar</a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php if(mysqli_num_rows($citas)==0): ?>
              <tr><td colspan="10" class="text-center text-muted py-4">No hay citas registradas</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
