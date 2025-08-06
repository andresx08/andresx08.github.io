<?php
$host = "localhost";
$usuario = "root"; // Cambia según tu configuración
$clave = "";
$bd = "go_michelas";

$conexion = new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>