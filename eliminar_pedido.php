<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Eliminar detalles del pedido primero
    $conexion->query("DELETE FROM detalle_pedido WHERE pedido_id = $id");
    // Eliminar el pedido
    $conexion->query("DELETE FROM pedidos WHERE id = $id");
}
header('Location: gestionar_pedidos.php');
exit;
