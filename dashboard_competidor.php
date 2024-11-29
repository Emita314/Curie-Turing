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

// Competencias disponibles
$sql_disponibles = "SELECT * FROM competencias WHERE id_competencia NOT IN (
    SELECT id_competencia FROM usuario_competencia WHERE id_usuario = ?
)";
$stmt = $conn->prepare($sql_disponibles);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_disponibles = $stmt->get_result();

// Competencias inscritas (aún no iniciadas)
$sql_inscritas = "SELECT c.* FROM competencias c
    JOIN usuario_competencia uc ON c.id_competencia = uc.id_competencia
    WHERE uc.id_usuario = ? AND c.fecha_inicio > NOW()";
$stmt = $conn->prepare($sql_inscritas);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_inscritas = $stmt->get_result();

// Competencias en curso (ya iniciadas)
$sql_en_curso = "SELECT c.* FROM competencias c
    JOIN usuario_competencia uc ON c.id_competencia = uc.id_competencia
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
            <h3>Competencias en Curso</h3>
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
            <h3>Mis Competencias</h3>
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
