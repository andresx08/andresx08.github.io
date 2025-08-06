<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conexion.php';
    $carrito = $_SESSION['carrito'] ?? [];
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $metodo_pago = trim($_POST['metodo_pago'] ?? '');

    // Calcular total
    $total = 0;
    foreach ($carrito as $key => $item) {
        $id_producto = is_array($item) && isset($item['id_producto']) ? $item['id_producto'] : $key;
        $producto = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM productos WHERE id = $id_producto"));
        if (!$producto) continue;
        $cantidad_num = is_array($item) && isset($item['cantidad']) ? (int)$item['cantidad'] : (int)$item;
        $total += $producto['precio'] * $cantidad_num;
    }

    // Insertar pedido (ahora con teléfono, dirección y nombre)
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, nombre, telefono, direccion, fecha, total, estado) VALUES (?, ?, ?, ?, NOW(), ?, 'pendiente')");
    $stmt->bind_param("isssd", $usuario_id, $nombre, $telefono, $direccion, $total);
    $stmt->execute();
    $pedido_id = $conexion->insert_id;

    // Insertar detalles
    foreach ($carrito as $key => $item) {
        $id_producto = is_array($item) && isset($item['id_producto']) ? $item['id_producto'] : $key;
        $producto = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM productos WHERE id = $id_producto"));
        if (!$producto) continue;
        $cantidad_num = is_array($item) && isset($item['cantidad']) ? (int)$item['cantidad'] : (int)$item;
        // Insertar detalle normalmente
        $stmt = $conexion->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $pedido_id, $id_producto, $cantidad_num);
        $stmt->execute();
        // Actualizar o insertar el total de veces pedido en una tabla auxiliar
        $sql_check = "SELECT * FROM producto_veces_pedido WHERE producto_id = $id_producto";
        $res_check = mysqli_query($conexion, $sql_check);
        if ($row_check = mysqli_fetch_assoc($res_check)) {
            // Ya existe, actualizar
            $nuevo_total = $row_check['veces_pedido'] + $cantidad_num;
            mysqli_query($conexion, "UPDATE producto_veces_pedido SET veces_pedido = $nuevo_total WHERE producto_id = $id_producto");
        } else {
            // No existe, insertar
            mysqli_query($conexion, "INSERT INTO producto_veces_pedido (producto_id, veces_pedido) VALUES ($id_producto, $cantidad_num)");
        }
    }

    // Vaciar el carrito
    unset($_SESSION['carrito']);

    // Guardar modal de confirmación en sesión y redirigir a carrito.php
    ob_start();
    ?>
    <div class="modal-bg"><div class="modal-content">
        <h1>¡Gracias por tu compra, <?= htmlspecialchars($nombre) ?>!</h1>
        <p>Te contactaremos pronto al <b><?= htmlspecialchars($telefono) ?></b>.</p>
        <a href="index.php" class="btn-cerrar">Volver al inicio</a>
    </div></div>
    <style>
    .modal-bg { position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); display:flex; align-items:center; justify-content:center; z-index:9999; }
    .modal-content { background:#232323; border-radius:18px; box-shadow:0 0 32px #00ff00cc; padding:40px 32px; text-align:center; max-width:400px; }
    .modal-content h1 { color:#0f0; font-size:2em; margin-bottom:18px; }
    .modal-content p { color:#fff; font-size:1.1em; margin-bottom:18px; }
    .btn-cerrar { background:#00ff00; color:#181818; border:none; border-radius:18px; padding:12px 32px; font-size:1.1em; font-weight:bold; cursor:pointer; box-shadow:0 0 12px #00ff00; transition:background 0.2s; }
    .btn-cerrar:hover { background:#ff00ff; color:#fff; }
    </style>
    <?php
    $_SESSION['modal_exito'] = ob_get_clean();
    header('Location: carrito.php');
    exit;
}
?>
