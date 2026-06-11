<?php
$host     = "localhost";
$usuario  = "root";
$password = "";
$base     = "salon_stylos";

$conexion = mysqli_connect($host, $usuario, $password, $base);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");

// ← AGREGA ESTO:
define('BASE_URL', '/PROYECTO_AULA/stylos-mvc/public');