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
        echo "Competencia creada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Eliminar competencia
if (isset($_GET['eliminar'])) {
    $id_competencia = $_GET['eliminar'];
    $sql = "DELETE FROM competencias WHERE id_competencia = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_competencia);
    $stmt->execute();
    echo "Competencia eliminada.";
}

// Obtener competencias
$result = $conn->query("SELECT * FROM competencias");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Competencias</title>
</head>
<body>
    <h2>Administrar Competencias</h2>
    <h3>Crear Nueva Competencia</h3>
    <form action="admin_competencias.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <br>
        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>
        <br>
        <label>Fecha de Inicio:</label>
        <input type="datetime-local" name="fecha_inicio" required>
        <br>
        <label>Duración (minutos):</label>
        <input type="number" name="tiempo_limite" required>
        <br>
        <button type="submit" name="crear">Crear</button>
    </form>

    <h3>Lista de Competencias</h3>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha de Inicio</th>
            <th>Duración</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['nombre']; ?></td>
            <td><?php echo $row['descripcion']; ?></td>
            <td><?php echo $row['fecha_inicio']; ?></td>
            <td><?php echo $row['tiempo_limite']; ?> minutos</td>
            <td>
                <a href="admin_competencias.php?eliminar=<?php echo $row['id_competencia']; ?>">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
