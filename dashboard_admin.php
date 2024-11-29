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

// Verificar si se ha solicitado eliminar una competencia
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_competencia = intval($_GET['eliminar']);

    // Verificar que la competencia pertenece al administrador antes de eliminarla
    $stmt = $conn->prepare("DELETE FROM competencias WHERE id_competencia = ? AND creado_por = ?");
    $stmt->bind_param("ii", $id_competencia, $id_usuario);

    if ($stmt->execute()) {
        $mensaje = "Competencia eliminada con éxito.";
    } else {
        $mensaje = "Error al eliminar la competencia.";
    }
    $stmt->close();
}

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
    <style>
        /* Estilos para el Snack Bar */
        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #4CAF50;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
            opacity: 0;
            transition: opacity 0.5s, bottom 0.5s;
        }

        /* Mostrar el snack bar cuando se activa */
        #snackbar.show {
            visibility: visible;
            opacity: 1;
            bottom: 50px;
        }
    </style>
</head>
<body>
    <h2>Bienvenido, Administrador <?php echo htmlspecialchars($nombre_usuario); ?></h2>
    <h3>Opciones</h3>

    <h4>Gestión de Competencias</h4>
    <ul>
        <li><a href="admin_competencias.php">Crear Nueva Competencia</a></li>
    </ul>

    <h4>Resultados de Competencias</h4>
    
    <!-- Snack bar para el mensaje -->
    <?php if (isset($mensaje)) { ?>
        <div id="snackbar"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if (isset($_GET['mensaje'])) { ?>
    <div id="snackbar"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php } ?>


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

                <!-- Enlace a detalle_competencia.php -->
                <a href="detalle_competencia.php?id_competencia=<?php echo $competencia['id_competencia']; ?>"          title="Ver Detalles">
                    <i class="fas fa-info-circle"></i>
                </a>

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
                <a href="dashboard_admin.php?eliminar=<?php echo $competencia['id_competencia']; ?>" 
                   onclick="return confirm('¿Estás seguro de que deseas eliminar esta competencia?');" 
                   title="Eliminar" style="color: red;">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <a href="logout.php" class="logout">Cerrar Sesión</a>

    <script>
        // Mostrar el snack bar cuando se carga la página si hay un mensaje
        window.onload = function() {
            var snackbar = document.getElementById("snackbar");
            if (snackbar) {
                snackbar.className = "show";
                setTimeout(function() {
                    snackbar.className = snackbar.className.replace("show", "");
                }, 3000); // El snack bar desaparece después de 3 segundos
            }
        }
    </script>
</body>
</html>
