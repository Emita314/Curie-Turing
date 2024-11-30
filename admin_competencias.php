<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: dashboard.php");
    exit();
}

// Crear competencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'];
    $tiempo_limite = $_POST['tiempo_limite'];
    $tipo_competencia = $_POST['tipo_competencia'];
    $creado_por = $_SESSION['id_usuario'];

    // Validar que los campos requeridos estén presentes
    if (empty($nombre) || empty($fecha_inicio) || empty($tiempo_limite) || empty($tipo_competencia)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } else {
        // Insertar nueva competencia en la base de datos
        $sql = "INSERT INTO competencias (nombre, descripcion, fecha_inicio, tiempo_limite, creado_por, tipo_competencia) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiss', $nombre, $descripcion, $fecha_inicio, $tiempo_limite, $creado_por, $tipo_competencia);

        if ($stmt->execute()) {
            // Redirigir a dashboard_admin.php después de crear la competencia
            header("Location: dashboard_admin.php?mensaje=Competencia%20creada%20exitosamente");
            exit();
        } else {
            $error = "Error al crear la competencia: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Competencias</title>
    <link rel="stylesheet" href="admin_competencias.css">
</head>
<body>
    <h2>Administrar Competencias</h2>

    <?php if (isset($error)) { ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <h3>Crear Nueva Competencia</h3>
    <form action="admin_competencias.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        
        <label>Descripción (opcional):</label>
        <textarea name="descripcion"></textarea>
        
        <label>Fecha de Inicio:</label>
        <input type="datetime-local" name="fecha_inicio" required>
        
        <label>Duración (minutos):</label>
        <input type="number" name="tiempo_limite" required>
        
        <label>Tipo de Competencia:</label>
        <select name="tipo_competencia" required>
            <option value="">Seleccionar tipo</option>
            <option value="Informatica">Informática</option>
            <option value="Fisica">Física</option>
            <option value="Quimica">Química</option>
            <option value="Matematicas">Matemáticas</option>
        </select>
        
        <button type="submit" name="crear">Crear</button>
    </form>
</body>
</html>