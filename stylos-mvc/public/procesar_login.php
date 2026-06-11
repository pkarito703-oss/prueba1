<?php
session_start();
require_once __DIR__ . '/../src/config/conexion.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $auth = new AuthController($conexion);
    $auth->login($email, $password);
}
