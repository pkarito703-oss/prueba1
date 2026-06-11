<?php
session_start();
require_once __DIR__ . '/../src/config/conexion.php';
require_once __DIR__ . '/../src/controllers/CarritoController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    echo json_encode(['ok' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$id_detalle = intval($_POST['id_detalle'] ?? 0);

if ($id_detalle <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Item inválido']);
    exit();
}

$carrito = new CarritoController($conexion);
$result  = $carrito->eliminar($_SESSION['id_usuario'], $id_detalle);
mostrarToast('Producto eliminado', 'success');
setTimeout(() => location.reload(), 800); // espera 800ms para que se vea el toast
echo json_encode($result);