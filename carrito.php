<?php
session_start();
include 'conexion.php';

// Mostrar modal de éxito si existe
$modal_exito = $_SESSION['modal_exito'] ?? null;
if ($modal_exito) {
    unset($_SESSION['modal_exito']);
    echo '<div id="modal-exito-overlay">' . $modal_exito . '</div>';
    echo '<style>#modal-exito-overlay { position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.85); z-index:99999; display:flex; align-items:center; justify-content:center; }</style>';
}

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
        body {
            background: linear-gradient(135deg, #181818 0%, #232323 100%);
            color: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .carrito-container {
            max-width: 900px;
            margin: 50px auto 30px auto;
            background: rgba(34,34,34,0.98);
            border-radius: 24px;
            box-shadow: 0 8px 32px #00ff0055, 0 1.5px 0 #00ff00;
            padding: 40px 36px 30px 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            color: #0f0;
            text-align: center;
            margin-bottom: 32px;
            font-size: 2.2em;
            letter-spacing: 1px;
        }
        .productos-lista {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .producto-item {
            background: #232323;
            border-radius: 14px;
            padding: 18px 22px;
            font-size: 1.13em;
            box-shadow: 0 0 12px #0f03;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.2s;
        }
        .producto-item:hover {
            box-shadow: 0 0 24px #00ff00cc;
        }
        .producto-info {
            flex: 1;
            min-width: 220px;
        }
        .producto-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-left: 24px;
        }
        .btn-editar, .btn-eliminar {
            background: #ff00ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 1em;
            cursor: pointer;
            margin-bottom: 2px;
            transition: background 0.2s;
        }
        .btn-editar:hover {
            background: #00ff00;
            color: #181818;
        }
        .btn-eliminar:hover {
            background: #ff3333;
        }
        .subtotal-producto {
            display: block;
            color: #ff00ff;
            font-weight: bold;
            margin-top: 4px;
        }
        .total-carrito {
            font-size: 1.5em;
            color: #0f0;
            text-align: right;
            margin: 28px 0 16px 0;
            width: 100%;
        }
        .btn-volver {
            background: linear-gradient(90deg, #00ff00 60%, #0f0 100%);
            color: #181818;
            border: none;
            border-radius: 25px;
            padding: 14px 38px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 16px #00ff00;
            margin-top: 10px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .btn-volver:hover {
            background: linear-gradient(90deg, #0f0 60%, #00ff00 100%);
            color: #232323;
            box-shadow: 0 0 32px #00ff00;
        }
        .formulario-pago {
            background: #232323;
            border: 2px solid #00ff00;
            border-radius: 18px;
            padding: 28px 28px 18px 28px;
            margin-top: 30px;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 0 16px #00ff00aa;
        }
        .formulario-pago label {
            color: #0f0;
            font-weight: bold;
            margin-top: 8px;
        }
        .formulario-pago input, .formulario-pago select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 18px 0;
            border-radius: 8px;
            border: none;
            background: #181818;
            color: #fff;
            font-size: 1em;
        }
        .btn-confirmar {
            background: #ff00ff;
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 14px 38px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 16px #ff00ff;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-confirmar:hover {
            background: #c800c8;
            box-shadow: 0 0 32px #ff00ff;
        }
        @media (max-width: 700px) {
            .carrito-container {
                padding: 18px 4px;
            }
            .producto-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .producto-actions {
                flex-direction: row;
                gap: 8px;
                margin-left: 0;
            }
            .formulario-pago {
                padding: 16px 4px;
            }
        }
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
                window.location = 'eliminar_producto.php?id=' + id;
            }
        }
        function numberFormat(num) {
            return num.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        function actualizarSubtotalYTotal() {
            var total = 0;
            // Recalcula el total sumando todos los productos y actualiza subtotales
            <?php foreach ($carrito as $id_js => $item_js): ?>
                var cant = parseInt(document.getElementById('cantidad_<?= $id_js ?>').value) || 1;
                var precio = <?= is_array($item_js) && isset($item_js['id_producto']) ? obtenerProducto($item_js['id_producto'], $conexion)['precio'] : obtenerProducto($id_js, $conexion)['precio'] ?>;
                var subtotal = cant * precio;
                total += subtotal;
                document.getElementById('subtotal_<?= $id_js ?>').textContent = 'Subtotal: $' + numberFormat(subtotal);
            <?php endforeach; ?>
            document.querySelector('.total-carrito strong').textContent = 'Total: $' + numberFormat(total);
        }
    </script>
</head>
<body>
    <div class="carrito-container">
        <h2>Productos en tu carrito</h2>

        <?php if (count($carrito) > 0): ?>
        <div class="productos-lista">
            <?php foreach ($carrito as $id => $item): ?>
                <?php $producto = obtenerProducto(is_array($item) && isset($item['id_producto']) ? $item['id_producto'] : $id, $conexion); if (!$producto) continue; ?>
                <?php $cantidad_num = is_array($item) && isset($item['cantidad']) ? (int)$item['cantidad'] : (int)$item; ?>
                <?php $subtotal = $producto['precio'] * $cantidad_num; $total += $subtotal; ?>
                <div class="producto-item">
                    <div class="producto-info">
                        <strong><?= htmlspecialchars($producto['nombre']) ?></strong><br>
                        Precio: $<?= number_format($producto['precio'], 2) ?><br>
                        Cantidad: <input type="number" min="1" id="cantidad_<?= $id ?>" value="<?= $cantidad_num ?>" style="width:60px;" onchange="editarCantidad(<?= $id ?>)">
                        <span class="subtotal-producto" id="subtotal_<?= $id ?>">Subtotal: $<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="producto-actions">
                        <button class="btn-eliminar" onclick="eliminarItem(<?= $id ?>)">Eliminar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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
            <button class="btn-confirmar" type="submit">Confirmar pedido</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
