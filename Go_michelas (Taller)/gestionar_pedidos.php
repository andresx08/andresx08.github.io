<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Obtener pedidos y detalles

// Solo mostrar pedidos pendientes en la tabla principal
$sql = "SELECT p.id, p.usuario_id, u.nombre AS usuario, p.fecha, p.total, p.estado
        FROM pedidos p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        WHERE p.estado = 'pendiente'
        ORDER BY p.fecha DESC";
$result = $conexion->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #111; color: #fff; }
        .table {
            background: #111;
            color: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 0 32px #00ff00cc, 0 2px 0 #00ff00;
            border: 3px solid #00ff00;
            border-collapse: separate;
        }
        .table th, .table td {
            vertical-align: middle;
            border: 2px solid #00ff00;
            font-size: 1.08em;
            padding: 14px 12px;
            background: #111 !important;
        }
        .table td {
            color: #fff !important;
        }
        .table th {
            background: linear-gradient(90deg, #00ff00 80%) !important;
            color: #111 !important;
            font-weight: bold;
            font-size: 1.13em;
            border-top: none;
            letter-spacing: 0.5px;
        }
        .table-striped > tbody > tr:nth-of-type(odd),
        .table-striped > tbody > tr:nth-of-type(even) {
            background: #111 !important;
        }
        .table tbody tr {
            transition: box-shadow 0.2s, background 0.2s;
        }
        .table tbody tr:hover {
            background: #00ff0033 !important;
            box-shadow: 0 0 16px #00ff00aa;
        }
        .table thead tr {
            border-radius: 18px 18px 0 0;
        }
        .btn-ver {
            background: #00ff00;
            color: #111;
            font-weight: bold;
            border-radius: 8px;
        }
        .btn-ver:hover {
            background: #ff00ff;
            color: #fff;
        }
        /* Scroll horizontal en pantallas pequeñas */
        .table-responsive {
            border-radius: 18px;
            box-shadow: 0 4px 32px #00ff0055, 0 1.5px 0 #00ff00;
        }
    </style>
</head>
<body>
<div class="container py-4" style="min-height:100vh;">
    <div class="mb-2" style="text-align:left;">
        <h1 style="color:#00ff00; font-size:2.2em; font-weight:900; letter-spacing:1.5px; text-shadow:0 0 8px #00ff00aa, 0 0 2px #fff; margin-bottom:10px; margin-top:10px;">Pedidos realizados</h1>
        <a href="index.php" class="btn btn-secondary mb-3 px-4 py-2" style="font-size:1.1em; border-radius:10px; background:#222; color:#00ff00; border:2px solid #00ff00; font-weight:bold;">Volver al inicio</a>
    </div>
        <!-- Filtro de ventas por fecha -->
    <div class="ventas-filtros mb-4" style="max-width:600px;margin-left:0;margin-bottom:18px;">
        <form method="get" class="filtro-ventas-form" style="margin-left:8px;">
            <label for="filtro_ventas" class="filtro-label">Ver Registro De Ventas:</label>
            <select name="filtro_ventas" id="filtro_ventas" class="filtro-select" onchange="this.form.submit()">
                <option value="">Selecciona</option>
                <option value="diario" <?php if(isset($_GET['filtro_ventas']) && $_GET['filtro_ventas']==='diario') echo 'selected'; ?>>Diarias</option>
                <option value="semanal" <?php if(isset($_GET['filtro_ventas']) && $_GET['filtro_ventas']==='semanal') echo 'selected'; ?>>Semanales</option>
                <option value="mensual" <?php if(isset($_GET['filtro_ventas']) && $_GET['filtro_ventas']==='mensual') echo 'selected'; ?>>Mensuales</option>
            </select>
        </form>
        <style>
        .filtro-ventas-form {
            display: flex;
            gap: 14px;
            align-items: center;
            justify-content: flex-start;
            margin-left: 0;
            margin-bottom: 0;
            /* Alineación más a la izquierda */
            padding-left: 0;
        }
        .filtro-label {
            color: #00ff00;
            font-weight: bold;
            font-size: 1.13em;
            text-shadow: 0 0 6px #00ff00, 0 0 2px #fff;
            margin-right: 4px;
        }
        .filtro-select {
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 1.08em;
            background: #181818;
            color: #00ff00;
            border: 2px solid #00ff00;
            box-shadow: 0 0 8px #00ff00;
            font-weight: bold;
            outline: none;
            transition: border 0.2s, box-shadow 0.2s, color 0.2s;
        }
        .filtro-select:focus, .filtro-select:hover {
            border: 2px solid #ff00ff;
            color: #fff;
            box-shadow: 0 0 16px #ff00ff;
            background: #232323;
        }
        @media (max-width: 700px) {
            .filtro-ventas-form {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
        </style>
    </div>
    <?php
    if (isset($_GET['filtro_ventas']) && in_array($_GET['filtro_ventas'], ['diario','semanal','mensual'])) {
        $filtro = $_GET['filtro_ventas'];
        $where = '';
        if ($filtro === 'diario') {
            $where = "WHERE estado = 'aprobado' AND DATE(fecha) = CURDATE()";
        } elseif ($filtro === 'semanal') {
            $where = "WHERE estado = 'aprobado' AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($filtro === 'mensual') {
            $where = "WHERE estado = 'aprobado' AND YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())";
        }
        $sql_ventas = "SELECT * FROM pedidos $where ORDER BY fecha DESC";
        $res_ventas = mysqli_query($conexion, $sql_ventas);
        echo '<div class="ventas-lista" style="width:100%;margin-bottom:30px;">';
        echo '<h3 style="color:#ff00ff;text-align:center;margin-bottom:12px;">Ventas ' . htmlspecialchars($filtro) . '</h3>';
        if (mysqli_num_rows($res_ventas) > 0) {
            echo '<table class="table table-dark table-bordered" style="background:#232323;border-radius:12px;overflow:hidden;">';
            echo '<thead><tr><th>ID</th><th>Cliente</th><th>Productos</th><th>Teléfono</th><th>Dirección</th><th>Fecha</th><th>Total</th><th>Acciones</th></tr></thead><tbody>';
            while($venta = mysqli_fetch_assoc($res_ventas)) {
                echo '<tr>';
                echo '<td>' . $venta['id'] . '</td>';
                echo '<td>' . htmlspecialchars($venta['nombre']) . '</td>';
                // Obtener productos del pedido con cantidad
                $det = $conexion->query("SELECT pr.nombre, d.cantidad FROM detalle_pedido d JOIN productos pr ON d.producto_id = pr.id WHERE d.pedido_id = " . $venta['id']);
                $detalle = [];
                while($p = $det->fetch_assoc()) {
                    $detalle[] = htmlspecialchars($p['nombre']) . ' <span style="color:#00ff00;">x' . $p['cantidad'] . '</span>';
                }
                echo '<td>' . implode(', ', $detalle) . '</td>';
                echo '<td>' . htmlspecialchars($venta['telefono']) . '</td>';
                echo '<td>' . htmlspecialchars($venta['direccion']) . '</td>';
                echo '<td>' . $venta['fecha'] . '</td>';
                echo '<td>$' . number_format($venta['total'], 2) . '</td>';
                // Acción eliminar con modal
                echo '<td>';
                echo '<button type="button" class="btn btn-danger btn-sm px-3" style="font-weight:bold; border-radius:8px;" data-bs-toggle="modal" data-bs-target="#modalEliminarVenta' . $venta['id'] . '">Eliminar</button>';
                echo '<div class="modal fade" id="modalEliminarVenta' . $venta['id'] . '" tabindex="-1" aria-labelledby="modalEliminarVentaLabel' . $venta['id'] . '" aria-hidden="true">';
                echo '  <div class="modal-dialog modal-dialog-centered">';
                echo '    <div class="modal-content" style="background:linear-gradient(135deg,#191919 80%,#ff005a 100%); color:#fff; border-radius:18px; border:2px solid #ff005a; box-shadow:0 0 24px #ff005a55;">';
                echo '      <div class="modal-header" style="border-bottom:1px solid #ff005a;">';
                echo '        <h5 class="modal-title d-flex align-items-center gap-2" id="modalEliminarVentaLabel' . $venta['id'] . '">';
                echo '          <span style="font-size:2em; color:#ff005a;">&#9888;</span>';
                echo '          <span style="font-weight:bold; letter-spacing:1px;">Confirmar eliminación</span>';
                echo '        </h5>';
                echo '        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>';
                echo '      </div>';
                echo '      <div class="modal-body text-center" style="font-size:1.15em;">';
                echo '        <p style="margin-bottom:10px; font-weight:500;">¿Estás seguro que deseas <span style=\'color:#ff005a;font-weight:bold;\'>eliminar</span> este pedido?</p>';
                echo '        <p style="font-size:0.95em; color:#ffb3c6;">Esta acción <b>no se puede deshacer</b>.</p>';
                echo '      </div>';
                echo '      <div class="modal-footer justify-content-center" style="border-top:1px solid #ff005a;">';
                echo '        <button type="button" class="btn btn-outline-light" style="border-color:#ff005a; color:#ff005a; font-weight:bold;" data-bs-dismiss="modal">Cancelar</button>';
                echo '        <a href="eliminar_pedido.php?id=' . $venta['id'] . '" class="btn" style="background:#ff005a; color:#fff; font-weight:bold; min-width:110px;">Eliminar</a>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p style="text-align:center;color:#ff00ff;">No hay ventas registradas para este periodo.</p>';
        }
        echo '</div>';
    }
    ?>
    <div class="table-responsive" style="max-width:1200px; width:100%; box-shadow:0 0 32px #00ff00aa, 0 2px 0 #00ff00; border-radius:22px; background:rgba(24,24,24,0.98); padding:0 0 16px 0;">
        <table class="table table-striped align-middle mb-0" style="border-radius:22px; overflow:hidden;">
            <thead>
                <tr>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Cliente</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Productos</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Teléfono</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Dirección</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Fecha</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Precio Total</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Estado</th>
                    <th style="background:linear-gradient(90deg,#00ff00 80%,#00c3ff 100%); color:#111; font-size:1.15em;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr style="box-shadow:0 2px 0 #00ff0022;">
                    <td style="font-weight:600; letter-spacing:0.5px; color:#00ff00; text-align:center; vertical-align:middle;">
                        <?php
                        if (!empty($row['usuario'])) {
                            echo htmlspecialchars($row['usuario']);
                        } else {
                            $pedido_nombre = $conexion->query("SELECT nombre FROM pedidos WHERE id = " . $row['id']);
                            $nombre_row = $pedido_nombre->fetch_assoc();
                            echo htmlspecialchars($nombre_row['nombre'] ?? 'Invitado');
                        }
                        ?>
                    </td>
                    <td style="max-width:220px; white-space:pre-line; text-align:center;">
                        <?php
                        // Mostrar productos en lista vertical para mejor visibilidad y contar globalmente
                        static $contador_productos = [];
                        $det = $conexion->query("SELECT pr.id, pr.nombre, d.cantidad FROM detalle_pedido d JOIN productos pr ON d.producto_id = pr.id WHERE d.pedido_id = " . $row['id']);
                        echo '<ul style="list-style:none;padding-left:0;margin-bottom:0;">';
                        while($p = $det->fetch_assoc()) {
                            // Contador global
                            if (!isset($contador_productos[$p['id']])) {
                                $contador_productos[$p['id']] = [
                                    'nombre' => $p['nombre'],
                                    'total' => 0
                                ];
                            }
                            $contador_productos[$p['id']]['total'] += $p['cantidad'];
                            echo '<li>' . htmlspecialchars($p['nombre']) . ' <span style="color:#00ff00;">x' . $p['cantidad'] . '</span></li>';
                        }
                        echo '</ul>';
                        ?>
                    </td>
                    <td style="color:#00e6ff; font-weight:500;"> 
                        <?php
                        $pedido_tel = $conexion->query("SELECT telefono FROM pedidos WHERE id = " . $row['id']);
                        $tel_row = $pedido_tel->fetch_assoc();
                        echo htmlspecialchars($tel_row['telefono'] ?? '-');
                        ?>
                    </td>
                    <td style="color:#fff; font-size:0.98em;">
                        <?php
                        $pedido_dir = $conexion->query("SELECT direccion FROM pedidos WHERE id = " . $row['id']);
                        $dir_row = $pedido_dir->fetch_assoc();
                        echo htmlspecialchars($dir_row['direccion'] ?? '-');
                        ?>
                    </td>
                    <td style="color:#fff; font-size:0.98em;">
                        <?= $row['fecha'] ?>
                    </td>
                    <td style="color:#00ff00; font-weight:700;">
                        <?php
                        $pedido_total = $conexion->query("SELECT total FROM pedidos WHERE id = " . $row['id']);
                        $total_row = $pedido_total->fetch_assoc();
                        echo '$' . number_format($total_row['total'], 2);
                        ?>
                    </td>
                    <td style="font-size:1.2em;">
                        <?php if ($row['estado'] === 'aprobado'): ?>
                            <span style="color:#00ff00;font-weight:bold;">&#x2611; Aprobado</span>
                        <?php else: ?>
                            <span style="color:orange;font-weight:bold;">&#x23F3; Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex flex-column flex-md-row gap-2 align-items-center justify-content-center">
                        <?php if ($row['estado'] === 'pendiente'): ?>
                            <a href="aprobar_pedido.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm px-3" style="font-weight:bold; border-radius:8px;">Aprobar</a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm px-3" style="font-weight:bold; border-radius:8px;" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $row['id'] ?>">Eliminar</button>
                        </div>
                        <!-- Modal de confirmación mejorado -->
                        <div class="modal fade" id="modalEliminar<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEliminarLabel<?= $row['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="background:linear-gradient(135deg,#191919 80%,#ff005a 100%); color:#fff; border-radius:18px; border:2px solid #ff005a; box-shadow:0 0 24px #ff005a55;">
                              <div class="modal-header" style="border-bottom:1px solid #ff005a;">
                                <h5 class="modal-title d-flex align-items-center gap-2" id="modalEliminarLabel<?= $row['id'] ?>">
                                  <span style="font-size:2em; color:#ff005a;">&#9888;</span>
                                  <span style="font-weight:bold; letter-spacing:1px;">Confirmar eliminación</span>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                              </div>
                              <div class="modal-body text-center" style="font-size:1.15em;">
                                <p style="margin-bottom:10px; font-weight:500;">¿Estás seguro que deseas <span style='color:#ff005a;font-weight:bold;'>eliminar</span> este pedido?</p>
                                <p style="font-size:0.95em; color:#ffb3c6;">Esta acción <b>no se puede deshacer</b>.</p>
                              </div>
                              <div class="modal-footer justify-content-center" style="border-top:1px solid #ff005a;">
                                <button type="button" class="btn btn-outline-light" style="border-color:#ff005a; color:#ff005a; font-weight:bold;" data-bs-dismiss="modal">Cancelar</button>
                                <a href="eliminar_pedido.php?id=<?= $row['id'] ?>" class="btn" style="background:#ff005a; color:#fff; font-weight:bold; min-width:110px;">Eliminar</a>
                              </div>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    // ...
    ?>
</div>
</body>
<!-- Scripts de Bootstrap para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
