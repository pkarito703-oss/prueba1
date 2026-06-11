<?php
session_start();
require_once __DIR__ . '/../src/config/conexion.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auth = new AuthController($conexion);
    $auth->registro($_POST);
}
