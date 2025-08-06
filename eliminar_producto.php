<?php
session_start();

if (!isset($_GET['id'])) {
    header('Location: carrito.php');
    exit;
}

$id = $_GET['id'];

if (isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header('Location: carrito.php');
exit;
