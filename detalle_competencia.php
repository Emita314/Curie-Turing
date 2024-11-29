<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener los detalles de la competencia seleccionada
if (isset($_GET['id_competencia'])) {
    $id_competencia = $_GET['id_competencia'];

    // Obtener datos de la competencia
    $sql_competencia = "SELECT * FROM competencias WHERE id_competencia = ?";
    $stmt_competencia = $conn->prepare($sql_competencia);
    $stmt_competencia->bind_param("i", $id_competencia);
    $stmt_competencia->execute();
    $competencia = $stmt_competencia->get_result()->fetch_assoc();

    // Si la competencia no existe, redirigir
    if (!$competencia) {
        header("Location: dashboard_admin.php");
        exit;
    }

    // Agregar nuevo problema
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_problema'])) {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $puntaje_base = $_POST['puntaje_base'];

        $sql_problema = "INSERT INTO problemas (id_competencia, titulo, descripcion, puntaje_base) VALUES (?, ?, ?, ?)";
        $stmt_problema = $conn->prepare($sql_problema);
        $stmt_problema->bind_param('issi', $id_competencia, $titulo, $descripcion, $puntaje_base);

        if ($stmt_problema->execute()) {
            // Redirigir para mostrar el mensaje de éxito en el snackbar
            header("Location: detalle_competencia.php?id_competencia=$id_competencia&mensaje=Problema%20agregado%20exitosamente");
            exit();
        } else {
            echo "Error: " . $stmt_problema->error;
        }
    }

    // Obtener problemas de la competencia
    $sql_problemas = "SELECT * FROM problemas WHERE id_competencia = ?";
    $stmt_problemas = $conn->prepare($sql_problemas);
    $stmt_problemas->bind_param("i", $id_competencia);
    $stmt_problemas->execute();
    $problemas_result = $stmt_problemas->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Competencia</title>
    <link rel="stylesheet" href="detalle_competencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- Mostrar Snackbar si hay mensaje en la URL -->
    <?php if (isset($_GET['mensaje'])) { ?>
        <div id="snackbar"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php } ?>

    <h2>Detalles de Competencia: <?php echo htmlspecialchars($competencia['nombre']); ?></h2>

    <h3>Datos de la Competencia</h3>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($competencia['nombre']); ?></p>
    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($competencia['descripcion']); ?></p>
    <p><strong>Fecha de Inicio:</strong> <?php echo htmlspecialchars($competencia['fecha_inicio']); ?></p>
    <p><strong>Duración:</strong> <?php echo htmlspecialchars($competencia['tiempo_limite']); ?> minutos</p>

    <h3>Agregar Nuevo Problema</h3>
    <form action="detalle_competencia.php?id_competencia=<?php echo $id_competencia; ?>" method="POST">
        <label>Título:</label>
        <input type="text" name="titulo" required>

        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>

        <label>Puntaje Base:</label>
        <input type="number" name="puntaje_base" required>

        <button type="submit" name="agregar_problema">Agregar Problema</button>
    </form>

    <h3>Problemas de la Competencia</h3>
    <table>
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>Puntaje Base</th>
        </tr>
        <?php while ($problema = $problemas_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($problema['titulo']); ?></td>
            <td><?php echo htmlspecialchars($problema['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($problema['puntaje_base']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <a href="dashboard_admin.php" class="back">Volver al Dashboard</a>

    <script>
        // Snackbar: Mostrar mensaje temporalmente
        setTimeout(function() {
            var x = document.getElementById("snackbar");
            if (x) x.className = x.className + " show";
            setTimeout(function() {
                if (x) x.className = x.className.replace("show", "");
            }, 3000);
        }, 500);
    </script>
</body>
</html>
