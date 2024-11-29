<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de competidor
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'competidor') {
    header("Location: login.php");
    exit;
}

// Verificar si se recibió el ID de la competencia
if (!isset($_GET['id_competencia'])) {
    echo "<p style='color: red;'>No se especificó ninguna competencia.</p>";
    exit;
}

$id_competencia = $_GET['id_competencia'];

// Obtener información de la competencia
$sql_competencia = "SELECT * FROM competencias WHERE id_competencia = ?";
$stmt = $conn->prepare($sql_competencia);
$stmt->bind_param('i', $id_competencia);
$stmt->execute();
$competencia = $stmt->get_result()->fetch_assoc();

if (!$competencia) {
    echo "<p style='color: red;'>Competencia no encontrada.</p>";
    exit;
}

// Obtener los problemas asociados a la competencia
$sql_problemas = "SELECT * FROM problemas WHERE id_competencia = ?";
$stmt = $conn->prepare($sql_problemas);
$stmt->bind_param('i', $id_competencia);
$stmt->execute();
$problemas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Competencia: <?php echo $competencia['nombre']; ?></title>
    <link rel="stylesheet" href="dashboard_competidor.css"> <!-- Reutiliza el CSS existente -->
</head>
<body>
    <h2>Detalles de Competencia</h2>
    <h3><?php echo htmlspecialchars($competencia['nombre']); ?></h3>
    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($competencia['descripcion']); ?></p>
    <p><strong>Fecha de Inicio:</strong> <?php echo htmlspecialchars($competencia['fecha_inicio']); ?></p>

    <h3>Problemas Asociados</h3>
    <table>
        <tr>
            <th>Problema</th>
            <th>Descripción</th>
            <th>Puntaje Base</th>
        </tr>
        <?php while ($problema = $problemas->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($problema['titulo']); ?></td>
            <td><?php echo htmlspecialchars($problema['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($problema['puntaje_base']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="dashboard_competidor.php" class="logout">Volver al Dashboard</a>
</body>
</html>
