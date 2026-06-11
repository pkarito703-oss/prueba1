<?php
session_start();
require_once __DIR__ . '/../../src/config/conexion.php';
require_once __DIR__ . '/../../src/controllers/UsuarioController.php';

if (!isset($_SESSION['logueado']) || $_SESSION['rol'] != 'admin') {
    header("Location: /public/login.php?error=Acceso denegado");
    exit();
}

$usuarioCtrl = new UsuarioController($conexion);
$exito = $error = '';

if (isset($_GET['activar']))      { $usuarioCtrl->activar($_GET['activar']); $exito = 'Usuario activado correctamente'; }
if (isset($_GET['desactivar']))   { $usuarioCtrl->desactivar($_GET['desactivar'], $_SESSION['id_usuario']) ? $exito='Usuario desactivado' : $error='No puedes desactivar tu propio usuario'; }
if (isset($_GET['hacer_admin']))  { $usuarioCtrl->hacerAdmin($_GET['hacer_admin']); $exito = 'Usuario ahora es administrador'; }
if (isset($_GET['hacer_cliente'])){ $usuarioCtrl->hacerCliente($_GET['hacer_cliente'], $_SESSION['id_usuario']) ? $exito='Usuario cambiado a cliente' : $error='No puedes cambiar tu propio rol'; }

if (!empty($exito)) { header("Location: usuarios_admin.php?exito=".urlencode($exito)); exit(); }
if (!empty($error)) { header("Location: usuarios_admin.php?error=".urlencode($error)); exit(); }

$buscar   = trim($_GET['buscar'] ?? '');
$usuarios = $usuarioCtrl->obtenerTodos($buscar);
$total    = $usuarioCtrl->contarTotal();
$activos  = $usuarioCtrl->contarPorEstado('activo');
$inactivos= $usuarioCtrl->contarPorEstado('inactivo');
$exito    = isset($_GET['exito']) ? htmlspecialchars($_GET['exito']) : '';
$error    = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$pagina_activa = 'usuarios';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Usuarios - Stylos Madys</title>
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
    .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:15px; margin-bottom:25px; }
    .stat-mini { background:#fff; border-radius:10px; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,0.05); text-align:center; }
    .stat-mini h4 { font-size:1.6rem; font-weight:700; margin:0; }
    .stat-mini p { margin:0; font-size:0.8rem; color:#888; }
    .content-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; }
    .content-card-header { padding:18px 20px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
    .content-card-header h5 { margin:0; font-weight:600; color:#333; font-size:1rem; }
    .table th { font-weight:600; color:#555; font-size:0.85rem; }
    .table td { font-size:0.88rem; color:#444; vertical-align:middle; }
    .btn-accion { padding:4px 10px; border-radius:6px; font-size:0.78rem; border:none; cursor:pointer; text-decoration:none; margin:1px; display:inline-block; }
    .user-avatar { width:35px; height:35px; border-radius:50%; background:#7c3aed; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:600; font-size:0.85rem; }
    .search-box { display:flex; gap:8px; }
    .search-box input { border:1.5px solid #e0d6f0; border-radius:8px; padding:6px 12px; font-size:0.88rem; }
    .search-box input:focus { border-color:#7c3aed; outline:none; }
    .search-box button { background:#7c3aed; color:#fff; border:none; border-radius:8px; padding:6px 14px; cursor:pointer; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/../../src/views/layouts/header_admin.php'; ?>

  <div class="main-content">
    <div class="top-navbar">
      <h5><i class="bi bi-people me-2" style="color:#7c3aed"></i>Usuarios</h5>
      <div class="admin-info">
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
        <div>
          <div style="font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($_SESSION['nombre']) ?></div>
          <div style="color:#7c3aed; font-size:0.78rem;">Administrador</div>
        </div>
      </div>
    </div>

    <div class="content-area">
      <?php if ($exito): ?><div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle me-2"></i><?= $exito ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show mb-3"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

      <div class="stats-row">
        <div class="stat-mini"><h4 style="color:#7c3aed"><?= $total ?></h4><p>Total Usuarios</p></div>
        <div class="stat-mini"><h4 style="color:#28a745"><?= $activos ?></h4><p>Activos</p></div>
        <div class="stat-mini"><h4 style="color:#dc3545"><?= $inactivos ?></h4><p>Inactivos</p></div>
      </div>

      <div class="content-card">
        <div class="content-card-header">
          <h5><i class="bi bi-people me-2" style="color:#7c3aed"></i>Lista de Usuarios</h5>
          <form method="GET" class="search-box">
            <input type="text" name="buscar" placeholder="Buscar por nombre o email..." value="<?= htmlspecialchars($buscar) ?>">
            <button type="submit"><i class="bi bi-search"></i></button>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Usuario</th><th>Email</th><th>Teléfono</th><th>Rol</th><th>Estado</th><th>Registro</th><th>Acciones</th></tr></thead>
            <tbody>
              <?php while($u = mysqli_fetch_assoc($usuarios)): ?>
              <tr>
                <td><?= $u['id_usuarios'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar"><?= strtoupper(substr($u['nombres_usuarios'], 0, 1)) ?></div>
                    <div>
                      <strong><?= htmlspecialchars($u['nombres_usuarios'] . ' ' . $u['apellidos_usuarios']) ?></strong>
                      <?php if($u['id_usuarios'] == $_SESSION['id_usuario']): ?><span class="badge" style="background:#7c3aed; font-size:0.7rem;">Tú</span><?php endif; ?>
                    </div>
                  </div>
                </td>
                <td><?= htmlspecialchars($u['email_usuarios']) ?></td>
                <td><?= htmlspecialchars($u['telefono_usuarios'] ?? '—') ?></td>
                <td><span style="background:<?= $u['nombre_roles']=='admin'?'#e2d9f3; color:#4a1d96':'#e8f5e9; color:#155724' ?>; padding:3px 10px; border-radius:20px; font-size:0.78rem;"><?= $u['nombre_roles'] ?? 'cliente' ?></span></td>
                <td><span style="background:<?= $u['estado_usuarios']=='activo'?'#d4edda; color:#155724':'#f8d7da; color:#721c24' ?>; padding:3px 10px; border-radius:20px; font-size:0.78rem;"><?= ucfirst($u['estado_usuarios']) ?></span></td>
                <td><small><?= $u['fecha_creacion_usuarios'] ? date('d/m/Y', strtotime($u['fecha_creacion_usuarios'])) : '—' ?></small></td>
                <td>
                  <?php if($u['id_usuarios'] != $_SESSION['id_usuario']): ?>
                    <?php if($u['estado_usuarios']=='activo'): ?>
                      <a href="usuarios_admin.php?desactivar=<?= $u['id_usuarios'] ?>" class="btn-accion" style="background:#f8d7da; color:#721c24;" onclick="return confirm('¿Desactivar?')"><i class="bi bi-person-x"></i> Desactivar</a>
                    <?php else: ?>
                      <a href="usuarios_admin.php?activar=<?= $u['id_usuarios'] ?>" class="btn-accion" style="background:#d4edda; color:#155724;"><i class="bi bi-person-check"></i> Activar</a>
                    <?php endif; ?>
                    <?php if($u['nombre_roles']!='admin'): ?>
                      <a href="usuarios_admin.php?hacer_admin=<?= $u['id_usuarios'] ?>" class="btn-accion" style="background:#e2d9f3; color:#4a1d96;" onclick="return confirm('¿Hacer admin?')"><i class="bi bi-shield-check"></i> Admin</a>
                    <?php else: ?>
                      <a href="usuarios_admin.php?hacer_cliente=<?= $u['id_usuarios'] ?>" class="btn-accion" style="background:#e8f5e9; color:#155724;" onclick="return confirm('¿Quitar admin?')"><i class="bi bi-person"></i> Cliente</a>
                    <?php endif; ?>
                  <?php else: ?>
                    <span style="color:#888; font-size:0.78rem;"><i class="bi bi-lock"></i> Tu cuenta</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php if(mysqli_num_rows($usuarios)==0): ?><tr><td colspan="8" class="text-center text-muted py-4">No se encontraron usuarios</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
