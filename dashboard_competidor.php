<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de competidor
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'competidor') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre'];

// Procesar inscripción en una competencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscribirse'])) {
    $id_competencia = $_POST['id_competencia'];

    // Verificar si ya está inscrito en la competencia antes de intentar insertar
    $sql_verificar = "SELECT * FROM usuario_competencia WHERE id_usuario = ? AND id_competencia = ?";
    $stmt = $conn->prepare($sql_verificar);
    $stmt->bind_param('ii', $id_usuario, $id_competencia);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<p style='color: red;'>Ya estás inscrito en esta competencia.</p>";
    } else {
        // Insertar inscripción
        $sql_inscribir = "INSERT INTO usuario_competencia (id_usuario, id_competencia) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_inscribir);
        $stmt->bind_param('ii', $id_usuario, $id_competencia);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Te has inscrito correctamente en la competencia.</p>";
        } else {
            echo "<p style='color: red;'>Error al inscribirse: " . $stmt->error . "</p>";
        }
    }
}

// Obtener competencias disponibles (no inscritas)
$sql_disponibles = "SELECT * FROM competencias WHERE id_competencia NOT IN (
    SELECT id_competencia FROM usuario_competencia WHERE id_usuario = ?
)";
$stmt = $conn->prepare($sql_disponibles);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_disponibles = $stmt->get_result();

// Obtener competencias inscritas
$sql_inscritas = "SELECT c.* FROM competencias c
    JOIN usuario_competencia uc ON c.id_competencia = uc.id_competencia
    WHERE uc.id_usuario = ?";
$stmt = $conn->prepare($sql_inscritas);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_inscritas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Competidor</title>
    <link rel="stylesheet" href="dashboard_competidor.css">
</head>
<body>
    <h2>Bienvenido, Competidor <?php echo $nombre_usuario; ?></h2>

    <!-- Competencias Inscritas -->
    <h3>Mis Competencias Inscritas</h3>
    <table>
        <tr>
            <th>Competencia</th>
            <th>Descripción</th>
            <th>Fecha de Inicio</th>
            <th>Acciones</th>
        </tr>
        <?php while ($competencia = $competencias_inscritas->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $competencia['nombre']; ?></td>
            <td><?php echo $competencia['descripcion']; ?></td>
            <td><?php echo $competencia['fecha_inicio']; ?></td>
            <td>
                <form method="GET" action="ver_competencia.php">
                    <input type="hidden" name="id_competencia" value="<?php echo $competencia['id_competencia']; ?>">
                    <button type="submit">Ver Problemas</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>


    <br><br>

    <!-- Competencias Disponibles -->
    <h3>Competencias Disponibles</h3>
    <table border="1">
        <tr>
            <th>Competencia</th>
            <th>Descripción</th>
            <th>Fecha de Inicio</th>
            <th>Acciones</th>
        </tr>
        <?php while ($competencia = $competencias_disponibles->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $competencia['nombre']; ?></td>
            <td><?php echo $competencia['descripcion']; ?></td>
            <td><?php echo $competencia['fecha_inicio']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="id_competencia" value="<?php echo $competencia['id_competencia']; ?>">
                    <button type="submit" name="inscribirse">Inscribirse</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
