<?php

session_start();
include 'conexion.php';

// Recuperar errores y datos de login de la sesión (si existen)
$loginError = $_SESSION['login_error'] ?? [];
$loginData = $_SESSION['login_data'] ?? [];
unset($_SESSION['login_error'], $_SESSION['login_data']);

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">


    <meta charset="UTF-8">
    <title>Go Michelas</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #111;
            color: #fff;
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
            transition: color 0.3s, text-shadow 0.3s;
        }
        .logo:hover, .logo:focus {
            color: #00ff22ff;
            text-shadow: 0 0 18px #00ff00, 0 0 12px #00ff22ff, 0 0 8px #33ff00ff;
            outline: none;
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

        .contenido {
            padding: 40px;
            text-align: center;
            font-family: 'Dancing Script', cursive;
            font-size: 2.5em;
        }

        .contenido h1 {
            font-size: 36px;
            color: #00ff00;
            text-shadow: 0 0 10px #00ff00;
        }

        .productos-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .producto {
            width: 280px;
            background: #111;
            border: 2px solid #00ff00;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 0 15px #00ff00;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #252828;
            
        }


        .producto-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 12px;
}
.producto-info h3 {
    color: #00ff00;
    font-size: 20px;
    margin-bottom: 6px;
}

.descripcion {
    font-size: 14px;
    color: #bbb;
    margin-bottom: 8px;
    height: 38px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.precio {
    color: #fff;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;
}

    .btn-categoria {
        background: linear-gradient(90deg, #00ff00 0%, #ff00ff 100%);
        color: #111 !important;
        font-weight: bold;
        border: none;
        outline: none;
        border-radius: 10px;
        box-shadow: 0 0 10px #ff00ff, 0 0 6px #00ff00 inset;
        padding: 7px 14px;
        text-decoration: none !important;
        transition: background 0.3s, color 0.3s, box-shadow 0.3s;
        margin-bottom: 3px;
        text-shadow: 0 0 3px #fff;
        font-size: 0.95em;
        display: inline-block;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .btn-categoria:hover, .btn-categoria:focus {
        background: linear-gradient(90deg, #ff00ff 0%, #00ff00 100%);
        color: #fff !important;
        box-shadow: 0 0 20px #00ff00, 0 0 12px #ff00ff inset;
        text-shadow: 0 0 8px #fff;
        border: 2px solid #00ff00;
        outline: none;
        text-decoration: none !important;
    }

        
/* Fondo del modal reforzado */
.modal-content, .modal-header, .modal-footer, .modal-body {
    background-color: #111 !important;
    border: none;
    box-shadow: none;
}
.modal-content {
    border: 2px solid #00ff00 !important;
    border-radius: 18px !important;
    box-shadow: 0 0 30px #00ff00, 0 0 10px #ff00ff inset !important;
    color: #fff !important;
    padding: 0 !important;
}
.modal-title {
    color: #00ff00;
    font-family: 'Dancing Script', cursive;
    font-size: 2em;
    text-shadow: 0 0 8px #00ff00;
}
.modal-content input[type="text"],
.modal-content input[type="password"],
.modal-content input[type="email"] {
    background-color: #111;
    color: #00ff00;
    border: 1.5px solid #ff00ff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1em;
    box-shadow: 0 0 8px #ff00ff inset;
}
.modal-content input[type="text"]::placeholder,
.modal-content input[type="password"]::placeholder,
.modal-content input[type="email"]::placeholder {
    color: #00ff00;
    opacity: 0.7;
}
.modal-content button,
.modal-footer button {
    background: linear-gradient(90deg, #00ff00 0%, #ff00ff 100%);
    color: #111;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    padding: 12px 0;
    box-shadow: 0 0 10px #00ff00;
    font-size: 1.1em;
    transition: background 0.3s, color 0.3s;
}
.modal-content button:hover,
.modal-footer button:hover {
    background: linear-gradient(90deg, #ff00ff 0%, #00ff00 100%);
    color: #fff;
    box-shadow: 0 0 18px #ff00ff;
}

/* Botón de cerrar (X) centrado y verde neón para ambos modales */
.modal-content .btn-close {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 2em !important;
    width: 2.5rem !important;
    height: 2.5rem !important;
    border-radius: 50% !important;
    opacity: 1 !important;
    background: transparent !important;
    border: none !important;
    color: #00ff00 !important;
    transition: background 0.2s, color 0.2s;
}
.modal-content .btn-close:hover {
    background: rgba(123, 255, 0, 1) !important;
    color: #00ff00 !important;
    box-shadow: 0 0 18px #fff, 0 0 8px #00ff00;
    border: none !important;
}
.modal-content a,
.modal-content a:visited {
    color: #00ff00;
    text-decoration: underline;
    font-weight: bold;
}
.modal-content a:hover {
    color: #ff00ff;
    text-shadow: 0 0 6px #ff00ff;
}
.invalid-feedback {
    color: #ff00ff;
    font-size: 0.95em;
    margin-top: -10px;
    margin-bottom: 10px;
}
.toast-carrito {
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
.toast-carrito.show {
    opacity: 1;
    pointer-events: auto;
    top: 50px;
}







/* Eliminar reglas duplicadas y dejar solo las de arriba */


    </style>
</head>
<body>
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
        <li class="nav-item"><a class="nav-link" href="logout.php">👤Cerrar Sesión</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalLogin">Iniciar Sesión</a></li>
        <?php endif; ?>
</ul>

    </nav>
</header>

<main class="contenido">
    <h1>¡Bienvenido a Go Michelas!</h1>
</main>

<!-- Botones de categorías -->
<div class="categorias-botones" style="display:flex;flex-wrap:wrap;justify-content:center;gap:15px;margin:0px 0 10px 0;">
  <a href="index.php" class="btn-categoria">Todos los productos</a>
  <a href="?categoria=dorilocos" class="btn-categoria">Dorilocos</a>
  <a href="?categoria=papa loca" class="btn-categoria">Papa loca</a>
  <a href="?categoria=cervezas" class="btn-categoria">Cervezas</a>
  <a href="?categoria=granizados" class="btn-categoria">Granizados</a>
  <a href="?categoria=bebidas" class="btn-categoria">Bebidas</a>
  <a href="?categoria=pecceeraas" class="btn-categoria">Peceras</a>
  <a href="?categoria=micheladas" class="btn-categoria">Micheladas</a>
  <a href="?categoria=micheladas cosmicas" class="btn-categoria">Micheladas cósmicas</a>
  <a href="?categoria=jugos naturales" class="btn-categoria">Jugos naturales</a>
  <a href="?categoria=limonadas" class="btn-categoria">Limonadas</a>
  <a href="?categoria=adicionales" class="btn-categoria">Adicionales</a>
  <a href="?categoria=sodas italianas" class="btn-categoria">Sodas italianas</a>
</div>

<div class="productos-grid">
<?php
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
if ($categoria) {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE categoria = ?");
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = mysqli_query($conexion, "SELECT * FROM productos");
}
while ($row = mysqli_fetch_assoc($resultado)) {
    echo "<div class='producto'>";
    if (!empty($row['imagen_url'])) {
        echo "<img class='producto-img' src='" . htmlspecialchars($row['imagen_url']) . "' alt='imagen'>";
    }
    echo "<div class='producto-info'>";
    echo "<h3>" . htmlspecialchars($row['nombre']) . "</h3>";
    echo "<p class='descripcion'>" . htmlspecialchars($row['descripcion']) . "</p>";
    echo "<p class='precio'>💲" . number_format($row['precio'], 2) . "</p>";
    echo "<form class='form-agregar-carrito' data-id='" . $row['id'] . "'>";
    echo "<input type='hidden' name='id_producto' value='" . $row['id'] . "'>";
    echo "<button type='submit' class='btn-comprar-neon'><span class='icono-carrito'>🛒</span> Agregar al carrito</button>";
    echo "</form>";
    echo "</div></div>";
}
?>
</div>

<!-- Toast de carrito (fuera del bucle) -->
<div id="toastCarrito" class="toast-carrito">Producto agregado al carrito 🛒</div>
<script>
document.querySelectorAll('.form-agregar-carrito').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = this.querySelector('input[name="id_producto"]').value;
        fetch('agregar_carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_producto=' + encodeURIComponent(id)
        })
        .then(res => res.ok ? res.text() : Promise.reject())
        .then(() => {
            const toast = document.getElementById('toastCarrito');
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 1800);
            // Actualizar contador del carrito en el nuevo botón
            let contador = document.querySelector('.btn-carrito-neon .contador-carrito');
            if(contador) {
                let match = contador.textContent.match(/\((\d+)\)/);
                if(match) {
                    let count = parseInt(match[1]) + 1;
                    contador.textContent = '(' + count + ')';
                }
            }
        })
        .catch(() => alert('Error al agregar al carrito.'));
    });
});
</script>
</div>


<!-- Botón de carrito mejorado -->
<a href="carrito.php" class="btn-carrito-neon">
  <span class="icono-carrito-neon">🛒</span>
  <span class="texto-carrito">Mi carrito</span>
  <span class="contador-carrito">(<?php echo count($_SESSION['carrito']); ?>)</span>
</a>

<style>
.btn-carrito-neon {
    position: fixed;
    bottom: 18px;
    left: 18px;
    display: flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(90deg, #00ff00 0%, #00c3ff 100%);
    color: #111;
    font-weight: bold;
    font-size: 1em;
    border: 2px solid #00ff00;
    border-radius: 10px;
    box-shadow: 0 0 14px #00ff00, 0 0 4px #00c3ff inset;
    padding: 8px 18px 8px 12px;
    text-decoration: none;
    z-index: 999;
    transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.18s;
    text-shadow: 0 0 2px #fff;
    letter-spacing: 0.3px;
    animation: neon-glow 2.2s infinite alternate;
    outline: none;
}
.btn-carrito-neon:hover, .btn-carrito-neon:focus {
    background: linear-gradient(90deg, #00c3ff 0%, #00ff00 100%);
    color: #fff;
    box-shadow: 0 0 22px #00ff00, 0 0 10px #00c3ff inset;
    transform: scale(1.04);
    border: 2px solid #00c3ff;
}
@keyframes neon-glow {
    0% { box-shadow: 0 0 8px #00ff00, 0 0 2px #00c3ff inset; }
    100% { box-shadow: 0 0 18px #00ff00, 0 0 8px #00c3ff inset; }
}
.icono-carrito-neon {
    font-size: 1.3em;
    filter: drop-shadow(0 0 4px #00ff00);
    margin-right: 1px;
    transition: transform 0.2s;
}
.btn-carrito-neon:hover .icono-carrito-neon {
    transform: scale(1.12) rotate(-8deg);
}
.texto-carrito {
    font-size: 1em;
    font-weight: bold;
    color: #111;
    text-shadow: 0 0 1px #fff, 0 0 4px #00ff00;
}
.contador-carrito {
    font-size: 1em;
    font-weight: bold;
    color: #111;
    margin-left: 1px;
    text-shadow: 0 0 1px #fff, 0 0 4px #00ff00;
}
</style>


<!-- Modal de Inicio de Sesión -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg rounded-4">
      <div class="modal-header bg-pink text-white">
        <h5 class="modal-title" id="modalLoginLabel">Iniciar Sesión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="color:#00ff00;font-size:2em;display:flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;background:transparent;border:none;">✖️</button>
      </div>
      <form action="login.php" method="POST">
        <div class="modal-body p-4">

          <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control <?php if(isset($loginError['correo'])) echo 'is-invalid'; ?>"
              name="correo" id="correo"
              value="<?= htmlspecialchars($loginData['correo'] ?? '') ?>" required>
            <?php if (isset($loginError['correo'])): ?>
              <div class="invalid-feedback" style="display:block;"> <?= $loginError['correo'] ?> </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control <?php if(isset($loginError['contrasena'])) echo 'is-invalid'; ?>"
              name="contrasena" id="contrasena" required>
            <?php if (isset($loginError['contrasena'])): ?>
              <div class="invalid-feedback" style="display:block;"> <?= $loginError['contrasena'] ?> </div>
            <?php endif; ?>
          </div>

          <div class="text-center">
            <small>¿No tienes una cuenta? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalRegistro">Regístrate</a></small>
          </div>

        </div>
        <div class="modal-footer p-3">
          <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php if (!empty($loginError)): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var loginModal = new bootstrap.Modal(document.getElementById('modalLogin'), { backdrop: 'static', keyboard: false });
    loginModal.show();
    // Ahora el usuario puede cerrar el modal aunque haya error
  });
</script>
<?php endif; ?>

<!-- Estilos para la flecha -->
<style>
  a[data-bs-target="#modalLogin"] svg {
    color: rgba(255, 255, 255, 0.6); /* mismo tono opaco que la X */
    transition: color 0.3s ease;
    cursor: pointer;
  }

  a[data-bs-target="#modalLogin"]:hover svg {
    color: rgba(255, 255, 255, 0.9); /* tono más claro al pasar el mouse */
  }
</style>
<style>
.btn-comprar-neon {
    width: 100%;
    background: linear-gradient(90deg, #00ff00 0%, #00c3ff 100%);
    color: #111;
    font-weight: bold;
    font-size: 1.08em;
    border: 2px solid #00ff00;
    border-radius: 10px;
    box-shadow: 0 0 12px #00ff00, 0 0 4px #00c3ff inset;
    padding: 12px 0 12px 0;
    margin-top: 10px;
    margin-bottom: 2px;
    transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.15s;
    text-shadow: 0 0 2px #fff;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    outline: none;
}
.btn-comprar-neon:hover, .btn-comprar-neon:focus {
    background: linear-gradient(90deg, #00c3ff 0%, #00ff00 100%);
    color: #fff;
    box-shadow: 0 0 24px #00ff00, 0 0 12px #00c3ff inset;
    transform: scale(1.04);
    border: 2px solid #00c3ff;
}
.btn-comprar-neon .icono-carrito {
    font-size: 1.25em;
    filter: drop-shadow(0 0 4px #00ff00);
    margin-right: 4px;
}
</style>

<!-- Modal de Registro -->
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg rounded-4">
      <div class="modal-header bg-pink text-white justify-content-between align-items-center">
        <h5 class="modal-title mb-0" id="modalRegistroLabel">Registrarse</h5>
        <div class="d-flex align-items-center gap-3">
          <a href="#" class="text-white text-decoration-none small" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalLogin">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
            </svg>
          </a>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="color:#00ff00;font-size:2em;display:flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;background:transparent;border:none;">✖️</button>
        </div>
      </div>
      <form action="registro.php" method="POST">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="nuevo_usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" name="nuevo_usuario" id="nuevo_usuario" required>
          </div>
          <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="correo" id="correo" required>
          </div>
          <div class="mb-3">
            <label for="nueva_contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="nueva_contrasena" id="nueva_contrasena" required>
          </div>
          
        </div>
        <div class="modal-footer p-3">
          <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function toggleDescripcion(id, descripcionCompleta, descripcionCorta, boton) {
    const desc = document.getElementById('desc-' + id);
    if (boton.innerText === 'Ver más') {
      desc.innerText = descripcionCompleta;
      boton.innerText = 'Ver menos';
    } else {
      desc.innerText = descripcionCorta;
      boton.innerText = 'Ver más';
    }
  }
  
</script>
</body>
</html>
<footer class="footer-michelas">
  <div class="footer-container">
    <div class="footer-info">
      <h4>Contáctanos</h4>
      <div class="footer-redes-centered">
        <a href="https://wa.me/5210000000000" target="_blank" title="WhatsApp" class="icono-red"><img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/whatsapp.svg" alt="WhatsApp"></a>
        <a href="https://instagram.com/" target="_blank" title="Instagram" class="icono-red"><img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram"></a>
        <a href="https://tiktok.com/" target="_blank" title="TikTok" class="icono-red"><img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/tiktok.svg" alt="TikTok"></a>
        <a href="https://facebook.com/" target="_blank" title="Facebook" class="icono-red"><img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook"></a>
      </div>
    </div>
    <div class="footer-mapa">
      <iframe id="iframeMapa" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.563884193139!2d-75.26677882596647!3d2.940842754399789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3b7500105e2d19%3A0xd6494afcdb9bc91c!2sGOMICHELAS!5e0!3m2!1ses!2sco!4v1754419214653!5m2!1ses!2sco" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</footer>
<style>
.footer-redes-centered {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 32px;
  margin-top: 30px;
  margin-bottom: 10px;
}
.footer-michelas {
  background: #181818;
  border-top: 2px solid #00ff00;
  color: #fff;
  padding: 0;
  margin-top: 40px;
  font-family: 'Arial', sans-serif;
  box-shadow: 0 -2px 18px #00ff0044;
}
.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: stretch;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px 0 24px;
  min-height: 260px;
}
.footer-info {
  flex: 1 1 320px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 32px 0 32px 0;
  gap: 18px;
}
.footer-info h4 {
  color: #00ff00;
  font-family: 'Dancing Script', cursive;
  font-size: 2em;
  margin-bottom: 10px;
  text-shadow: 0 0 8px #00ff00;
}
.ubicacion-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 10px;
}
.ubicacion-form label {
  font-size: 1em;
  color: #fff;
}
.ubicacion-form input[type="text"] {
  padding: 8px 10px;
  border-radius: 7px;
  border: 1.5px solid #00ff00;
  background: #222;
  color: #00ff00;
  font-size: 1em;
  outline: none;
  margin-bottom: 2px;
}
.ubicacion-form button {
  background: linear-gradient(90deg, #00ff00 0%, #00c3ff 100%);
  color: #111;
  font-weight: bold;
  border: none;
  border-radius: 7px;
  padding: 7px 0;
  font-size: 1em;
  box-shadow: 0 0 8px #00ff00;
  transition: background 0.3s, color 0.3s;
  cursor: pointer;
}
.ubicacion-form button:hover {
  background: linear-gradient(90deg, #00c3ff 0%, #00ff00 100%);
  color: #fff;
}
.footer-redes {
  display: none;
}
.icono-red img {
  width: 32px;
  height: 32px;
  filter: drop-shadow(0 0 6px #00ff00);
  transition: filter 0.2s, transform 0.2s;
}
.icono-red:hover img {
  filter: drop-shadow(0 0 16px #00ff00) brightness(1.2);
  transform: scale(1.13) rotate(-8deg);
}
.footer-mapa {
  flex: 1 1 420px;
  min-width: 320px;
  max-width: 520px;
  height: 220px;
  margin: 32px 0 32px 24px;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 0 18px #00ff00, 0 0 8px #00c3ff inset;
  border: 2px solid #00ff00;
  background: #222;
  display: flex;
  align-items: stretch;
}
.footer-mapa iframe {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 18px;
}
@media (max-width: 900px) {
  .footer-container {
    flex-direction: column;
    align-items: stretch;
    padding: 0 8px;
  }
  .footer-mapa {
    margin: 0 0 24px 0;
    min-width: 0;
    max-width: 100%;
    height: 200px;
  }
  .footer-redes-centered {
    gap: 18px;
    margin-top: 18px;
    margin-bottom: 8px;
  }
}
</style>

