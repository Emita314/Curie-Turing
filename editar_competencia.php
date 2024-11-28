<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: dashboard.php");
    exit();
}

// Obtener ID de competencia
$id_competencia = $_GET['id_competencia'] ?? null;
if (!$id_competencia) {
    echo "ID de competencia no válido.";
    exit();
}

// Cargar datos de la competencia
$sql = "SELECT * FROM competencias WHERE id_competencia = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_competencia);
$stmt->execute();
$result = $stmt->get_result();
$competencia = $result->fetch_assoc();
if (!$competencia) {
    echo "Competencia no encontrada.";
    exit();
}

// Actualizar datos de la competencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $tiempo_limite = $_POST['tiempo_limite'];

    $sql_update = "UPDATE competencias 
                   SET nombre = ?, descripcion = ?, fecha_inicio = ?, tiempo_limite = ? 
                   WHERE id_competencia = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('sssii', $nombre, $descripcion, $fecha_inicio, $tiempo_limite, $id_competencia);

    if ($stmt->execute()) {
        echo "Competencia actualizada exitosamente.";
        // Recargar los datos actualizados
        header("Location: editar_competencia.php?id_competencia=$id_competencia");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Competencia</title>
    <link rel="stylesheet" href="admin_competencias.css">
</head>
<body>
    <h2>Editar Competencia</h2>
    <form action="" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($competencia['nombre']); ?>" required>
        
        <label>Descripción:</label>
        <textarea name="descripcion" required><?php echo htmlspecialchars($competencia['descripcion']); ?></textarea>
        
        <label>Fecha de Inicio:</label>
        <input type="datetime-local" name="fecha_inicio" 
               value="<?php echo date('Y-m-d\TH:i', strtotime($competencia['fecha_inicio'])); ?>" required>
        
        <label>Duración (minutos):</label>
        <input type="number" name="tiempo_limite" value="<?php echo $competencia['tiempo_limite']; ?>" required>
        
        <button type="submit">Guardar Cambios</button>
    </form>

    <h3>Problemas Asociados</h3>
    <ul>
        <?php
        $sql_problemas = "SELECT * FROM problemas WHERE id_competencia = ?";
        $stmt = $conn->prepare($sql_problemas);
        $stmt->bind_param('i', $id_competencia);
        $stmt->execute();
        $result_problemas = $stmt->get_result();

        while ($problema = $result_problemas->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($problema['titulo']) . " 
                  <a href='eliminar_problema.php?id_problema=" . $problema['id_problema'] . "'>Eliminar</a></li>";
        }
        ?>
    </ul>
    <a href="crear_problema.php?id_competencia=<?php echo $id_competencia; ?>">Agregar Nuevo Problema</a>
</body>
</html>
