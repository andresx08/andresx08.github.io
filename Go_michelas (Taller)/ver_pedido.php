<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "ID de pedido inválido.";
    exit;
}

// Obtener pedido
$stmt = $conexion->prepare("SELECT p.*, u.nombre AS usuario FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
if (!$pedido) {
    echo "Pedido no encontrado.";
    exit;
}
// Obtener detalles
$stmt = $conexion->prepare("SELECT d.*, pr.nombre FROM detalle_pedido d LEFT JOIN productos pr ON d.producto_id = pr.id WHERE d.pedido_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$detalles = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #111; color: #fff; }
        .table { background: #222; color: #fff; border-radius: 10px; }
        .table th, .table td { vertical-align: middle; }
        .table th { background: #00ff00; color: #111; }
        .table-striped > tbody > tr:nth-of-type(odd) { background: #191919; }
    </style>
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4" style="color:#00ff00;">Detalle del Pedido #<?= $pedido['id'] ?></h1>
    <a href="gestionar_pedidos.php" class="btn btn-secondary mb-3">Volver a pedidos</a>
    <div class="mb-3"><strong>Usuario:</strong> <?= htmlspecialchars($pedido['usuario'] ?? 'Invitado') ?></div>
    <div class="mb-3"><strong>Fecha:</strong> <?= $pedido['fecha'] ?></div>
    <div class="mb-3"><strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?></div>
    <div class="mb-3"><strong>Estado:</strong> <?= htmlspecialchars($pedido['estado']) ?></div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $detalles->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= $row['cantidad'] ?></td>
                <td>$<?= number_format($row['precio_unitario'], 2) ?></td>
                <td>$<?= number_format($row['cantidad'] * $row['precio_unitario'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
