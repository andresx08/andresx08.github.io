<?php
$conexion = new mysqli("localhost", "root", "", "go_michelas");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID inválido.";
    exit;
}

$resultado = $conexion->query("SELECT * FROM productos WHERE id = $id");

if ($resultado->num_rows === 0) {
    echo "Producto no encontrado.";
    exit;
}

$producto = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $categoria = $_POST["categoria"];
    $precio = $_POST["precio"];
    $descripcion = $_POST["descripcion"];

    $imagen_url = $producto['imagen_url'];

    if ($_FILES["imagen"]["name"]) {
        $imagen_nombre = $_FILES["imagen"]["name"];
        $imagen_temp = $_FILES["imagen"]["tmp_name"];
        $carpeta_destino = "uploads/";

        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        $ruta_imagen = $carpeta_destino . basename($imagen_nombre);
        move_uploaded_file($imagen_temp, $ruta_imagen);
        $imagen_url = $ruta_imagen;
    }

    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, categoria=?, precio=?, descripcion=?, imagen_url=? WHERE id=?");
    $stmt->bind_param("ssdssi", $nombre, $categoria, $precio, $descripcion, $imagen_url, $id);
    $stmt->execute();

    header("Location: productos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        body {
            background-color: #0c0c0c;
            color: white;
            font-family: 'Orbitron', sans-serif;
        }
        h2 {
            text-align: center;
            color: #ff00ff;
            text-shadow: 0 0 10px #ff00ff;
        }
        .form-container {
            width: 50%;
            margin: 30px auto;
            background: #1a1a1a;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 20px #ff00ff;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid cyan;
            border-radius: 5px;
            background-color: #111;
            color: white;
        }
        button {
            padding: 12px 20px;
            font-weight: bold;
            border-radius: 10px;
            border: none;
            background: #ff00ff;
            color: white;
            box-shadow: 0 0 10px #ff00ff;
            cursor: pointer;
        }
        a.volver {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: cyan;
            text-decoration: none;
        }
        
        
    </style>
</head>
<body>
    <h2>✏️ Editar Producto</h2>
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nombre" required value="<?= htmlspecialchars($producto['nombre']) ?>">
            <label class="form-label">Categoría</label>
            <div class="dropdown">
                <button class="btn btn-estilo w-100 dropdown-toggle" type="button" id="dropdownCategoria" data-bs-toggle="dropdown" aria-expanded="false" style="background: linear-gradient(90deg, #00ff00 0%, #ff00ff 100%); color: #111; font-weight: bold; border: 2px solid #00ff00; border-radius: 12px; box-shadow: 0 0 16px #00ff00, 0 0 8px #ff00ff inset; transition: background 0.3s, color 0.3s, box-shadow 0.3s; text-shadow: 0 0 4px #fff;">
                    <span id="categoriaSeleccionada"><?= htmlspecialchars($producto['categoria']) ? htmlspecialchars(ucwords($producto['categoria'])) : 'Selecciona una categoría' ?></span>
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="dropdownCategoria">
                    <li><a class="dropdown-item" href="#" data-value="dorilocos">Dorilocos</a></li>
                    <li><a class="dropdown-item" href="#" data-value="papa loca">Papa loca</a></li>
                    <li><a class="dropdown-item" href="#" data-value="cervezas">Cervezas</a></li>
                    <li><a class="dropdown-item" href="#" data-value="granizados">Granizados</a></li>
                    <li><a class="dropdown-item" href="#" data-value="bebidas">Bebidas</a></li>
                    <li><a class="dropdown-item" href="#" data-value="shot 40% de licor">Shot 40% de licor</a></li>
                    <li><a class="dropdown-item" href="#" data-value="pecceeraas">Peceras</a></li>
                    <li><a class="dropdown-item" href="#" data-value="micheladas">Micheladas</a></li>
                    <li><a class="dropdown-item" href="#" data-value="micheladas cosmicas">Micheladas cósmicas</a></li>
                    <li><a class="dropdown-item" href="#" data-value="jugos naturales">Jugos naturales</a></li>
                    <li><a class="dropdown-item" href="#" data-value="limonadas">Limonadas</a></li>
                    <li><a class="dropdown-item" href="#" data-value="adicionales">Adicionales</a></li>
                    <li><a class="dropdown-item" href="#" data-value="sodas italianas">Sodas italianas</a></li>
                    <li><a class="dropdown-item" href="#" data-value="escarchados a escoger">Escarchados a escoger</a></li>
                </ul>
                <input type="hidden" name="categoria" id="inputCategoria" value="<?= htmlspecialchars($producto['categoria']) ?>" required>
            </div>
            <input type="number" name="precio" step="0.01" required value="<?= $producto['precio'] ?>">
            <textarea name="descripcion" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            <label style="color: cyan;">Imagen actual: <?= basename($producto['imagen_url']) ?></label><br>
            <input type="file" name="imagen"><br>
            <button type="submit">Guardar Cambios</button>
        </form>
        <a class="volver" href="productos.php">⬅ Volver a Productos</a>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Script para mostrar la categoría seleccionada en el botón y actualizar el input oculto
document.addEventListener('DOMContentLoaded', function() {
  const categoriaItems = document.querySelectorAll('.dropdown-item');
  const categoriaSpan = document.getElementById('categoriaSeleccionada');
  const inputCategoria = document.getElementById('inputCategoria');
  categoriaItems.forEach(function(item) {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const nombre = item.textContent;
      const valor = item.getAttribute('data-value');
      categoriaSpan.textContent = nombre;
      inputCategoria.value = valor;
    });
  });
});
</script>
</html>
