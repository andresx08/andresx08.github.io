<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo'] ?? '');
    $clave = trim($_POST['contrasena'] ?? '');

    // Guardar datos del formulario en caso de error (excepto contraseña)
    $_SESSION['login_data'] = [
        'correo' => $correo
    ];

    // Verificar si el correo existe
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if (!$usuario) {
        // Correo no registrado
        $_SESSION['login_error'] = [
            'correo' => 'Correo no registrado.'
        ];
    } elseif (!password_verify($clave, $usuario['clave'])) {
        // Contraseña incorrecta
        $_SESSION['login_error'] = [
            'contrasena' => 'Contraseña incorrecta.'
        ];
    } else {
        // Inicio de sesión correcto
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['usuario_id'] = $usuario['id'];

        // Limpiar errores anteriores
        unset($_SESSION['login_error'], $_SESSION['login_data']);

        header("Location: index.php");
        exit;
    }

    // Redirigir de vuelta con errores
    header("Location: index.php");
    exit;
}
?>
