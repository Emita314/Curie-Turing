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

// Obtener solo las competencias creadas por el administrador actual
$sql_competencias = "SELECT * FROM competencias WHERE creado_por = ?";
$stmt_competencias = $conn->prepare($sql_competencias);
$stmt_competencias->bind_param("i", $id_usuario);
$stmt_competencias->execute();
$competencias_result = $stmt_competencias->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <!-- Enlace a Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_admin.css">
</head>
<body>
    <h2>Bienvenido, Administrador <?php echo htmlspecialchars($nombre_usuario); ?></h2>
    <h3>Opciones</h3>

    <h4>Gestión de Competencias</h4>
    <ul>
        <li><a href="admin_competencias.php">Crear Nueva Competencia</a></li>
    </ul>

    <h4>Resultados de Competencias</h4>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha de Inicio</th>
            <th>Duración (minutos)</th>
            <th>Acciones</th>
        </tr>
        <?php while ($competencia = $competencias_result->fetch_assoc()) { 
            $fecha_inicio = new DateTime($competencia['fecha_inicio']);
            $fecha_actual = new DateTime();
            $editable = $fecha_inicio > $fecha_actual; // Verifica si se puede editar
        ?>
        <tr>
            <td><?php echo htmlspecialchars($competencia['nombre']); ?></td>
            <td><?php echo htmlspecialchars($competencia['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($competencia['fecha_inicio']); ?></td>
            <td><?php echo htmlspecialchars($competencia['tiempo_limite']); ?></td>
            <td>
                <!-- Botón de resultados -->
                <a href="resultados_competencia.php?id_competencia=<?php echo $competencia['id_competencia']; ?>" title="Ver Resultados">
                    <i class="fas fa-chart-bar"></i>
                </a>

                <!-- Botón de edición condicionado -->
                <?php if ($editable) { ?>
                    <a href="editar_competencia.php?id_competencia=<?php echo $competencia['id_competencia']; ?>" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                <?php } else { ?>
                    <i class="fas fa-edit" style="cursor: not-allowed; opacity: 0.5;" title="No editable"></i>
                <?php } ?>

                <!-- Botón de eliminar -->
                <a href="eliminar_competencia.php?id_competencia=<?php echo $competencia['id_competencia']; ?>" 
                   onclick="return confirm('¿Estás seguro de que deseas eliminar esta competencia?');" 
                   title="Eliminar" style="color: red;">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <a href="logout.php" class="logout">Cerrar Sesión</a>
</body>
</html>

