<?php
session_start();
include 'conexion.php';

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;

function obtenerProducto($id, $conexion) {
    $sql = "SELECT * FROM productos WHERE id = $id";
    return mysqli_fetch_assoc(mysqli_query($conexion, $sql));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito - Go Michelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #111; color: #fff; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .carrito-container { max-width: 700px; margin: 40px auto; background: #232323; border-radius: 18px; box-shadow: 0 0 20px #0f03; padding: 30px 24px; }
        h2 { color: #0f0; text-align: center; margin-bottom: 30px; }
        .producto-item { background: #222; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; font-size: 1.1em; box-shadow: 0 0 8px #0f03; display: flex; justify-content: space-between; align-items: center; }
        .producto-info { flex: 1; }
        .producto-actions { display: flex; flex-direction: column; gap: 8px; margin-left: 18px; }
        .btn-editar, .btn-eliminar { background: #ff00ff; color: #fff; border: none; border-radius: 8px; padding: 6px 14px; font-size: 1em; cursor: pointer; margin-bottom: 2px; transition: background 0.2s; }
        .btn-editar:hover { background: #00ff00; color: #181818; }
        .btn-eliminar:hover { background: #ff3333; }
        .total-carrito { font-size: 1.3em; color: #0f0; text-align: right; margin: 18px 0 10px 0; }
        .btn-volver { background: linear-gradient(90deg, #00ff00 60%, #0f0 100%); color: #181818; border: none; border-radius: 25px; padding: 12px 32px; font-size: 1.2em; font-weight: bold; cursor: pointer; box-shadow: 0 0 16px #00ff00; margin-top: 10px; transition: background 0.2s, color 0.2s, box-shadow 0.2s; }
        .btn-volver:hover { background: linear-gradient(90deg, #0f0 60%, #00ff00 100%); color: #232323; box-shadow: 0 0 32px #00ff00; }
        .formulario-pago { background: #222; border: 2px solid #00ff00; border-radius: 18px; padding: 24px; margin-top: 20px; max-width: 400px; margin-left: auto; margin-right: auto; }
        .formulario-pago label { color: #0f0; font-weight: bold; }
        .formulario-pago input, .formulario-pago select { width: 100%; padding: 8px; margin: 8px 0 16px 0; border-radius: 8px; border: none; background: #181818; color: #fff; }
        .btn-confirmar { background: #ff00ff; color: #fff; border: none; border-radius: 25px; padding: 12px 32px; font-size: 1.1em; font-weight: bold; cursor: pointer; box-shadow: 0 0 16px #ff00ff; transition: background 0.2s, box-shadow 0.2s; }
        .btn-confirmar:hover { background: #c800c8; box-shadow: 0 0 32px #ff00ff; }
    </style>
    <script>
        function volverInicio() { window.location = 'index.php'; }
        function editarCantidad(id) {
            var cantidad = document.getElementById('cantidad_' + id).value;
            fetch('editar_item.php?id=' + id + '&cantidad=' + cantidad)
                .then(() => location.reload());
        }
        function eliminarItem(id) {
            if(confirm('¿Eliminar este producto del carrito?')) {
                window.location = 'eliminar_item.php?id=' + id;
            }
        }
    </script>
</head>
<body>
    <div class="carrito-container">
        <h2>Productos en tu carrito</h2>
        <?php if (count($carrito) > 0): ?>
            <?php foreach ($carrito as $id => $cantidad): ?>
                <?php $producto = obtenerProducto($id, $conexion); if (!$producto) continue; ?>
                <?php $cantidad_num = is_array($cantidad) ? (int)reset($cantidad) : (int)$cantidad; ?>
                <?php $subtotal = $producto['precio'] * $cantidad_num; $total += $subtotal; ?>
                <div class="producto-item">
                    <div class="producto-info">
                        <strong><?= htmlspecialchars($producto['nombre']) ?></strong><br>
                        Precio: $<?= number_format($producto['precio'], 2) ?><br>
                        Cantidad: <input type="number" min="1" id="cantidad_<?= $id ?>" value="<?= $cantidad_num ?>" style="width:60px;">
                    </div>
                    <div class="producto-actions">
                        <button class="btn-editar" onclick="editarCantidad(<?= $id ?>)">Editar</button>
                        <button class="btn-eliminar" onclick="eliminarItem(<?= $id ?>)">Eliminar</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="total-carrito"><strong>Total: $<?= number_format($total, 2) ?></strong></div>
        <?php else: ?>
            <p style="text-align:center;">No hay productos en el carrito.</p>
        <?php endif; ?>
        <button class="btn-volver" onclick="volverInicio()">← Volver al inicio</button>
        <?php if (count($carrito) > 0): ?>
        <form class="formulario-pago" action="procesar_pedido.php" method="POST">
            <h3 style="color:#0f0;text-align:center;">Confirmar pago</h3>
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
            <label>Número:</label>
            <input type="text" name="telefono" required>
            <label>Dirección:</label>
            <input type="text" name="direccion" required>
            <label>Método de pago:</label>
            <select name="metodo_pago" required>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
            </select>
            <button class="btn-confirmar" type="submit">Confirmar pedido</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
