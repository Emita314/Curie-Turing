<?php
session_start();
include 'db.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');  // Ajusta según tu zona horaria


// Verificar si el usuario ha iniciado sesión y tiene rol de competidor
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'competidor') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre'];

// Procesar inscripción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscribirse'])) {
    $id_competencia = $_POST['id_competencia'];

    // Verificar si la competencia ya ha iniciado o no
    $sql_competencia = "SELECT fecha_inicio FROM competencias WHERE id_competencia = ?";
    $stmt = $conn->prepare($sql_competencia);
    $stmt->bind_param('i', $id_competencia);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    // Si la fecha de inicio es menor o igual a la fecha actual, es una competencia en curso
    if (strtotime($resultado['fecha_inicio']) <= time()) {
        // Insertar en la tabla de competencias en curso
        $sql_inscripcion = "INSERT INTO resultados (id_usuario, id_competencia) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_inscripcion);
        $stmt->bind_param('ii', $id_usuario, $id_competencia);
        $stmt->execute();
    } else {
        // Insertar en la tabla de competencias inscritas (no iniciadas)
        $sql_inscripcion = "INSERT INTO resultados (id_usuario, id_competencia) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_inscripcion);
        $stmt->bind_param('ii', $id_usuario, $id_competencia);
        $stmt->execute();
    }
}

// Competencias disponibles
$sql_disponibles = "SELECT * FROM competencias WHERE id_competencia NOT IN (
    SELECT id_competencia FROM resultados WHERE id_usuario = ?
)";
$stmt = $conn->prepare($sql_disponibles);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_disponibles = $stmt->get_result();

// Competencias inscritas (aún no iniciadas)
$sql_inscritas = "SELECT c.* FROM competencias c
    JOIN resultados uc ON c.id_competencia = uc.id_competencia
    WHERE uc.id_usuario = ? AND c.fecha_inicio > NOW()";
$stmt = $conn->prepare($sql_inscritas);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_inscritas = $stmt->get_result();

// Competencias en curso (ya iniciadas)
$sql_en_curso = "SELECT c.* FROM competencias c
    JOIN resultados uc ON c.id_competencia = uc.id_competencia
    WHERE uc.id_usuario = ? AND c.fecha_inicio <= NOW()";
$stmt = $conn->prepare($sql_en_curso);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_en_curso = $stmt->get_result();
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

    <!-- Competencias Disponibles -->
    <div class="table-wrapper">
        <h3>Competencias Disponibles</h3>
        <table>
            <tr>
                <th>Competencia</th>
                <th>Descripción</th>
                <th>Fecha de Inicio</th>
                <th>Acciones</th>
            </tr>
            <?php while ($competencia = $competencias_disponibles->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($competencia['nombre']); ?></td>
                <td><?php echo htmlspecialchars($competencia['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($competencia['fecha_inicio']); ?></td>
                <td>
                    <form method="POST" action="dashboard_competidor.php">
                        <input type="hidden" name="id_competencia" value="<?php echo $competencia['id_competencia']; ?>">
                        <button type="submit" name="inscribirse">Inscribirse</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Contenedor para tablas de Inscritas y En Curso -->
    <div class="tables-container">
        <!-- Competencias Inscritas -->
<div class="table-wrapper">
    <h3>Competencias Inscritas</h3>
    <table>
        <tr>
            <th>Competencia</th>
            <th>Descripción</th>
            <th>Fecha de Inicio</th>
            <th>Acciones</th>
        </tr>
        <?php while ($competencia = $competencias_inscritas->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($competencia['nombre']); ?></td>
            <td><?php echo htmlspecialchars($competencia['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($competencia['fecha_inicio']); ?></td>
            <td>
                <!-- Mostrar el botón solo si la competencia ya ha iniciado -->
                <?php if (strtotime($competencia['fecha_inicio']) <= time()) { ?>
                    <form method="GET" action="ver_competencia.php">
                        <input type="hidden" name="id_competencia" value="<?php echo $competencia['id_competencia']; ?>">
                        <button type="submit">Ver Problemas</button>
                    </form>
                <?php } else { ?>
                    <span style="color: gray;">Esperando inicio...</span>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>


        <!-- Competencias en Curso -->
        <div class="table-wrapper">
            <h3>Competencias en Curso</h3>
            <table>
                <tr>
                    <th>Competencia</th>
                    <th>Descripción</th>
                    <th>Fecha de Inicio</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($competencia = $competencias_en_curso->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($competencia['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($competencia['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($competencia['fecha_inicio']); ?></td>
                    <td>
                        <!-- Mostrar el botón de "Ver Problemas" solo si la competencia ha comenzado -->
                        <form method="GET" action="ver_competencia.php">
                            <input type="hidden" name="id_competencia" value="<?php echo $competencia['id_competencia']; ?>">
                            <button type="submit">Ver Problemas</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <br>
    <a href="logout.php" class="logout">Cerrar Sesión</a>
</body>
</html>
