<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conexion->query("UPDATE pedidos SET estado = 'aprobado' WHERE id = $id");
}
header('Location: gestionar_pedidos.php');
exit;
