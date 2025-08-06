<?php
session_start(); 
include 'conexion.php';

$conexion = new mysqli("localhost", "root", "", "go_michelas");



if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Subida de imagen

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $categoria = $_POST["categoria"];
    $precio = $_POST["precio"];
    $descripcion = $_POST["descripcion"];

    $imagen_nombre = $_FILES["imagen"]["name"];
    $imagen_temp = $_FILES["imagen"]["tmp_name"];
    $carpeta_destino = "uploads/";

    // Crear carpeta si no existe
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $ruta_imagen = $carpeta_destino . basename($imagen_nombre);
    move_uploaded_file($imagen_temp, $ruta_imagen);

    $stmt = $conexion->prepare("INSERT INTO productos (nombre, categoria, precio, descripcion, imagen_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $nombre, $categoria, $precio, $descripcion, $ruta_imagen);
    $stmt->execute();
}
$productos = $conexion->query("SELECT * FROM productos");
?>
<!DOCTYPE html>

<html lang="es">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<head>
    <meta charset="UTF-8">
    <title>Administrar Productos - Go Michelas</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
    body {
        margin: 0;
        font-family: 'Arial', sans-serif;
        background-color: #111;
        color: #fff;
    }

    /* === NUEVO NAVBAR (idéntico al index) === */
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
        margin: 0;
        padding: 0;
    }

    .nav-links li a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s;
        cursor: pointer;
    }

    .nav-links li a:hover {
        color: #00ff00;
    }
    /* === FIN NUEVO NAVBAR === */

    h2 {
        text-align: center;
        color: #ff00ff;
        text-shadow: 0 0 10px #ff00ff, 0 0 18px #ff00ff;
        font-family: 'Dancing Script', 'Poppins', cursive, sans-serif;
        font-size: 2.5em;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .form-container, .table-container {
        width: 80%;
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
        background: #ff00ff;
        border: none;
        padding: 12px 20px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        border-radius: 10px;
        box-shadow: 0 0 10px #ff00ff;
    }
    /* Estilo especial para el botón de categoría */
    .btn-estilo {
        background: linear-gradient(90deg, #00ff00 0%, #ff00ff 100%);
        color: #111;
        font-weight: bold;
        border: 2px solid #00ff00;
        border-radius: 12px;
        box-shadow: 0 0 16px #00ff00, 0 0 8px #ff00ff inset;
        transition: background 0.3s, color 0.3s, box-shadow 0.3s;
        text-shadow: 0 0 4px #fff;
    }
    .btn-estilo:hover, .btn-estilo:focus {
        background: linear-gradient(90deg, #ff00ff 0%, #00ff00 100%);
        color: #fff;
        box-shadow: 0 0 24px #ff00ff, 0 0 12px #00ff00 inset;
    }
    /* Dropdown personalizado */
    .dropdown-menu {
        background: #222;
        border: 2px solid #00ff00;
        border-radius: 10px;
        box-shadow: 0 0 12px #00ff00;
        color: #fff;
    }
    .dropdown-item {
        color: #fff;
        font-weight: bold;
        transition: background 0.2s, color 0.2s;
    }
    .dropdown-item:hover {
        background: #00ff00;
        color: #111;
        text-shadow: 0 0 4px #ff00ff;
    }

    table {
        width: 100%;
        text-align: center;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
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

    .acciones button {
        padding: 5px 10px;
        margin: 2px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
    }

    .editar {
        background-color: #00ffaa;
        color: #000;
    }

    .eliminar {
        background-color: #ff0044;
        color: #fff;
    }
</style>

<style>
.toast-exito {
    position: fixed;
    top: 30px;
    right: 30px;
    background: #111;
    color: #00ff00;
    border: 2px solid #00ff00;
    border-radius: 12px;
    box-shadow: 0 0 16px #00ff00;
    padding: 18px 32px;
    font-size: 1.15em;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s, top 0.3s;
}
.toast-exito.show {
    opacity: 1;
    pointer-events: auto;
    top: 50px;
}
</style>
</head>
<body>
    <?php if ($_SERVER["REQUEST_METHOD"] === "POST") : ?>
    <div id="toastExito" class="toast-exito show">¡Producto agregado con éxito! ✅</div>
    <script>
      setTimeout(function() {
        document.getElementById('toastExito').classList.remove('show');
      }, 2000);
    </script>
    <?php endif; ?>
    <header>
        <nav class="navbar">
      <a href="index.php" class="logo" style="text-decoration:none;">Go Michelas</a>
            <ul class="nav-links">
                <li><a href="index.php">Inicio</a></li>

                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li><a href="productos.php">Agregar Productos</a></li>
                    <li><a href="gestionar_pedidos.php">Gestionar Pedidos</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['usuario'])): ?>
                    <li><a href="logout.php">👤Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#modalLogin">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
<br>
</header>
    <h2>🛠 Administrar Productos</h2>
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Categoría</label>
                    <div class="dropdown">
                        <button class="btn btn-estilo w-100 dropdown-toggle" type="button" id="dropdownCategoria" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="categoriaSeleccionada">Selecciona una categoría</span>
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
                        <input type="hidden" name="categoria" id="inputCategoria" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" placeholder="Precio (COP)" required>
                </div>
            </div>

            <textarea name="descripcion" placeholder="Ingredientes" required></textarea>
            <input type="file" name="imagen" accept="image/*" required>
            <button type="submit">+ Agregar Producto</button>
        </form>
    </div>

    <div class="table-container">
        <h2>📁 Productos Registrados</h2>
        <table>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
            <?php while($row = $productos->fetch_assoc()): ?>
            <tr>
                <td><img src="<?= $row["imagen_url"] ?>" alt="Imagen"></td>
                <td><?= htmlspecialchars($row["nombre"]) ?></td>
                <td><?= htmlspecialchars($row["categoria"]) ?></td>
                <td>$<?= number_format($row["precio"], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($row["descripcion"]) ?></td>
                <td>
               <a href="editar.php?id=<?= $row["id"] ?>"><button class="editar">✏️</button></a>
               <a href="eliminar.php?id=<?= $row["id"] ?>" onclick="return confirm('¿Eliminar este producto?')">
            <button class="eliminar">🗑️</button>
              </a>
             </td>

            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
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
</body>
</html>
