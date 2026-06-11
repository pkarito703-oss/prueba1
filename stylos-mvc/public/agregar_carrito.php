<?php
ob_start();
session_start();
require_once __DIR__ . '/../src/config/conexion.php';
require_once __DIR__ . '/../src/controllers/CarritoController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    ob_end_clean();
    echo json_encode(['ok' => false, 'mensaje' => 'Debes iniciar sesión', 'login' => true]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

$id_producto = intval($_POST['id_producto'] ?? 0);
$cantidad    = intval($_POST['cantidad'] ?? 1);

if ($id_producto <= 0) {
    ob_end_clean();
    echo json_encode(['ok' => false, 'mensaje' => 'Producto inválido']);
    exit();
}

$carrito = new CarritoController($conexion);
$result  = $carrito->agregar($_SESSION['id_usuario'], $id_producto, $cantidad);

ob_end_clean();
echo json_encode($result);