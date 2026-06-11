<?php
// src/views/layouts/header_admin.php
// Sidebar y navbar reutilizable para el panel admin
// Uso: include con $pagina_activa definida antes
$pagina_activa = $pagina_activa ?? '';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <h4>💇‍♀️ Stylos Madys</h4>
        <p>Panel Administrador</p>
    </div>
    <div class="sidebar-menu">
        <div class="menu-title">Principal</div>
        <a href="/public/admin/dashboard.php" class="<?= $pagina_activa=='dashboard'?'active':'' ?>">
            <i class="bi bi-speedometer2"></i> Mi perfil admin
        </a>
        <a href="/public/admin/citas_admin.php" class="<?= $pagina_activa=='citas'?'active':'' ?>">
            <i class="bi bi-calendar-check"></i> Gestionar Citas
        </a>
        <a href="/public/admin/usuarios_admin.php" class="<?= $pagina_activa=='usuarios'?'active':'' ?>">
            <i class="bi bi-people"></i> Usuarios
        </a>
        <a href="/public/admin/productos_admin.php" class="<?= $pagina_activa=='productos'?'active':'' ?>">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a href="/public/admin/servicios_admin.php" class="<?= $pagina_activa=='servicios'?'active':'' ?>">
            <i class="bi bi-scissors"></i> Servicios
        </a>
        <div class="menu-title" style="margin-top:15px;">Cuenta</div>
        <a href="/public/index.php"><i class="bi bi-house"></i> Ver Tienda</a>
        <a href="/public/logout.php" style="color:#e74c3c;">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>
</div>
