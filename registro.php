<?php
session_start();
require 'conexion.php'; // Este archivo debe definir la variable $conexion

// Recibir datos del formulario
$usuario     = trim($_POST['nuevo_usuario'] ?? '');
$correo      = trim($_POST['correo'] ?? '');
$contrasena  = $_POST['nueva_contrasena'] ?? '';
$rol         = $_POST['rol'] ?? 'cliente';

// Guardar datos en sesión para rellenar los campos si hay error
$_SESSION['registro_data'] = [
    'usuario' => $usuario,
    'correo'  => $correo,
    'rol'     => $rol
];

// Validar campos vacíos
if (empty($usuario) || empty($correo) || empty($contrasena) || empty($rol)) {
    $_SESSION['registro_error'] = [
        'campo' => 'usuario',
        'mensaje' => 'Todos los campos son obligatorios.'
    ];
    header('Location: index.php');
    exit;
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['registro_error'] = [
        'campo' => 'correo',
        'mensaje' => 'El correo no es válido.'
    ];
    header('Location: index.php');
    exit;
}

// Validar longitud mínima de contraseña
if (strlen($contrasena) < 6) {
    $_SESSION['registro_error'] = [
        'campo' => 'contrasena',
        'mensaje' => 'La contraseña debe tener al menos 6 caracteres.'
    ];
    header('Location: index.php');
    exit;
}

// Verificar si el correo ya está registrado
$sql = "SELECT id FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['registro_error'] = [
        'campo' => 'correo',
        'mensaje' => 'Este correo ya está registrado.'
    ];
    header('Location: index.php');
    exit;
}
$stmt->close(); // Cerrar consulta previa

// Encriptar la contraseña
$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$sqlInsert = "INSERT INTO usuarios (usuario, correo, clave, rol) VALUES (?, ?, ?, ?)";
$stmtInsert = $conexion->prepare($sqlInsert);
$stmtInsert->bind_param('ssss', $usuario, $correo, $contrasenaHash, $rol);

if ($stmtInsert->execute()) {
    // Limpia variables de sesión si el registro fue exitoso
    unset($_SESSION['registro_error'], $_SESSION['registro_data']);
    $_SESSION['registro_exito'] = '¡Registro exitoso! Ya puedes iniciar sesión.';
    header('Location: index.php');
} else {
    $_SESSION['registro_error'] = [
        'campo' => 'usuario',
        'mensaje' => 'Hubo un error al registrar. Intenta nuevamente.'
    ];
    header('Location: index.php');
}
$stmtInsert->close();
$conexion->close();
exit;
