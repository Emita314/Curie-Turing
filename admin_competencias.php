<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: dashboard.php");
    exit();
}

// Crear competencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $tiempo_limite = $_POST['tiempo_limite'];
    $creado_por = $_SESSION['id_usuario'];

    $sql = "INSERT INTO competencias (nombre, descripcion, fecha_inicio, tiempo_limite, creado_por) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssii', $nombre, $descripcion, $fecha_inicio, $tiempo_limite, $creado_por);

    if ($stmt->execute()) {
        // Redirigir a dashboard_admin.php después de crear la competencia
        header("Location: dashboard_admin.php?mensaje=Competencia%20creada%20exitosamente");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Competencias</title>
    <link rel="stylesheet" href="admin_competencias.css">
</head>
<body>
    <h2>Administrar Competencias</h2>
    <h3>Crear Nueva Competencia</h3>
    <form action="admin_competencias.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        
        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>
        
        <label>Fecha de Inicio:</label>
        <input type="datetime-local" name="fecha_inicio" required>
        
        <label>Duración (minutos):</label>
        <input type="number" name="tiempo_limite" required>
        
        <button type="submit" name="crear">Crear</button>
    </form>
</body>
</html>
