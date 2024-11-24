<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['id_usuario'])) {
    // Usuario ha iniciado sesión, obtener el rol
    $rol = $_SESSION['rol'];

    // Redirigir dependiendo del rol
    if ($rol == 'administrador') {
        header("Location: dashboard_admin.php");  // Redirige al dashboard de administrador
    } else if ($rol == 'competidor') {
        header("Location: dashboard_competidor.php");  // Redirige al dashboard de competidor
    }
} else {
    // Usuario no ha iniciado sesión, redirigir a login o signup
    header("Location: login.php");
    exit;
}
?>
