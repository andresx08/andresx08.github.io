<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "gomichelas");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta estadísticas
$total = $conexion->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()['total'];
$promedio = $conexion->query("SELECT AVG(precio) AS promedio FROM productos")->fetch_assoc()['promedio'];
$categorias = $conexion->query("SELECT categoria, COUNT(*) AS cantidad FROM productos GROUP BY categoria");
$ultimos = $conexion->query("SELECT * FROM productos ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Go Michelas</title>
    <style>
        body {
            background-color: #0c0c0c;
            color: white;
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #1a1a1a;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #00ff00;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #00ff00;
            text-shadow: 0 0 8px #00ff00;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .nav-links a:hover {
            color: #00ff00;
        }
        .container {
            width: 90%;
            margin: 40px auto;
        }
        h2 {
            color: #ff00ff;
            text-shadow: 0 0 10px #ff00ff;
            text-align: center;
        }
        .cards {
            display: flex;
            gap: 20px;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .card {
            background-color: #1a1a1a;
            border-radius: 15px;
            padding: 20px;
            width: 250px;
            text-align: center;
            box-shadow: 0 0 10px #ff00ff;
        }
        .card h3 {
            color: cyan;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid cyan;
        }
        th {
            color: cyan;
        }
        img {
            width: 60px;
            height: 60px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">Go Michelas</div>
    <ul class="nav-links">
        <li><a href="index.php">Inicio</a></li>
        <li><a href="productos.php">Administrar Productos</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
    </ul>
</nav>

<div class="container">
    <h2>📊 Dashboard de Administración</h2>
    <div class="cards">
        <div class="card">
            <h3>🧮 Total Productos</h3>
            <p><?= $total ?></p>
        </div>
        <div class="card">
            <h3>💲 Precio Promedio</h3>
            <p>$<?= number_format($promedio, 0, ',', '.') ?></p>
        </div>
        <div class="card">
            <h3>📂 Categorías</h3>
            <?php while($cat = $categorias->fetch_assoc()): ?>
                <p><?= htmlspecialchars($cat['categoria']) ?>: <?= $cat['cantidad'] ?></p>
            <?php endwhile; ?>
        </div>
    </div>

    <h2>🆕 Últimos Productos Agregados</h2>
    <table>
        <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Descripción</th>
        </tr>
        <?php while($row = $ultimos->fetch_assoc()): ?>
            <tr>
                <td><img src="<?= $row['imagen_url'] ?>" alt="Producto"></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['categoria']) ?></td>
                <td>$<?= number_format($row['precio'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($row['descripcion']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
