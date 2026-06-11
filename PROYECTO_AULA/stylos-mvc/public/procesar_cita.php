<?php
session_start();
require_once __DIR__ . '/../src/config/conexion.php';
require_once __DIR__ . '/../src/controllers/CitaController.php';

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header("Location: /public/login.php?error=Debes iniciar sesión para reservar una cita");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cita = new CitaController($conexion);
    $cita->crear($_POST, $_SESSION['id_usuario']);
}
