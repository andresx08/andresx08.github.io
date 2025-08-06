<?php
session_start();

$id_producto = $_POST['id_producto'];
$tamano = isset($_POST['tamano']) ? $_POST['tamano'] : null;

// Consultar la categoría del producto
include 'conexion.php';
$categoria = '';
$stmt = $conexion->prepare("SELECT categoria FROM productos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$stmt->bind_result($categoria);
$stmt->fetch();
$stmt->close();

// Si es Dorilocos y tiene tamaño, usar clave compuesta
if (strtolower($categoria) === 'dorilocos' && $tamano) {
    $key = $id_producto . '_' . $tamano;
} else {
    $key = $id_producto;
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Incrementar cantidad si ya existe
if (isset($_SESSION['carrito'][$key])) {
    $_SESSION['carrito'][$key]['cantidad']++;
} else {
    $_SESSION['carrito'][$key] = [
        'id_producto' => $id_producto,
        'cantidad' => 1,
        'tamano' => $tamano
    ];
}

header("Location: carrito.php");
exit();
