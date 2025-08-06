<?php
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$carrito = $_SESSION['carrito'];

$id = isset($_GET['id']) ? $_GET['id'] : null;
$cantidad = isset($_GET['cantidad']) ? (int)$_GET['cantidad'] : 1;

if ($id !== null && $cantidad > 0) {
    if (isset($carrito[$id])) {
        if (is_array($carrito[$id])) {
            $carrito[$id]['cantidad'] = $cantidad;
        } else {
            $carrito[$id] = $cantidad;
        }
        $_SESSION['carrito'] = $carrito;
    }
}
// No se imprime nada, solo se actualiza la sesión
