<?php
$conexion = new mysqli("localhost", "root", "", "go_michelas");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_post = $_POST["id"];
    $conexion->query("DELETE FROM productos WHERE id = $id_post");
    header("Location: productos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
    <style>
        body {
            background-color: #0c0c0c;
            color: white;
            font-family: 'Orbitron', sans-serif;
        }
        .box {
            max-width: 400px;
            margin: 100px auto;
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 0 20px #ff0044;
        }
        h2 {
            color: #ff0044;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }
        .confirmar { background-color: #ff0044; color: white; }
        .cancelar { background-color: #00ffaa; color: black; }
    </style>
</head>
<body>
    <div class="box">
        <h2>¿Seguro que deseas eliminar este producto?</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="confirmar">Sí, eliminar</button>
            <a href="productos.php"><button type="button" class="cancelar">Cancelar</button></a>
        </form>
    </div>
</body>
</html>
