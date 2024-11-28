<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre'];

// Obtener todas las competencias
$sql_competencias = "SELECT * FROM competencias";
$competencias_result = $conn->query($sql_competencias);

// Obtener todos los usuarios
$sql_usuarios = "SELECT * FROM usuarios";
$usuarios_result = $conn->query($sql_usuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="dashboard_admin.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <h2>Bienvenido, Administrador <?php echo htmlspecialchars($nombre_usuario); ?></h2>
    <h3>Opciones</h3>

    <h4>Gestión de Competencias</h4>
    <ul>
        <li><a href="admin_competencias.php">Crear Nueva Competencia</a></li>
    </ul>

    <h4>Gestión de Usuarios</h4>
    <ul>
        <li><a href="listar_usuarios.php">Listar Usuarios</a></li>
    </ul>

    <h4>Resultados de Competencias</h4>
    <table>
        <tr>
            <th>Competencia</th>
            <th>Acciones</th>
        </tr>
        <?php while ($competencia = $competencias_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($competencia['nombre']); ?></td>
            <td>
                <a href="resultados_competencia.php?id_competencia=<?php echo $competencia['id_competencia']; ?>">Ver Resultados</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <a href="logout.php" class="logout">Cerrar Sesión</a>
</body>
</html>

