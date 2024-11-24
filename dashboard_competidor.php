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

// Obtener las competencias en las que el competidor está inscrito
$sql_competencias = "SELECT c.* FROM competencias c
                     JOIN problemas p ON c.id_competencia = p.id_competencia
                     JOIN envios e ON p.id_problema = e.id_problema
                     WHERE e.id_usuario = ? GROUP BY c.id_competencia";
$stmt = $conn->prepare($sql_competencias);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$competencias_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Competidor</title>
</head>
<body>
    <h2>Bienvenido, Competidor <?php echo $nombre_usuario; ?></h2>
    <h3>Mis Competencias</h3>

    <table border="1">
        <tr>
            <th>Competencia</th>
            <th>Acciones</th>
        </tr>
        <?php while ($competencia = $competencias_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $competencia['nombre']; ?></td>
            <td><a href="enviar_solucion.php?id_competencia=<?php echo $competencia['id_competencia']; ?>">Enviar Solución</a></td>
        </tr>
        <?php } ?>
    </table>

    <h3>Mis Resultados</h3>
    <table border="1">
        <tr>
            <th>Competencia</th>
            <th>Problema</th>
            <th>Puntaje</th>
            <th>Intentos Fallidos</th>
            <th>Fecha de Envío</th>
        </tr>
        <?php
        // Obtener los resultados del competidor
        $sql_resultados = "SELECT c.nombre AS competencia, p.titulo AS problema, e.puntaje, e.intentos_fallidos, e.enviado_en
                           FROM envios e
                           JOIN problemas p ON e.id_problema = p.id_problema
                           JOIN competencias c ON p.id_competencia = c.id_competencia
                           WHERE e.id_usuario = ?";
        $stmt = $conn->prepare($sql_resultados);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $resultados = $stmt->get_result();

        while ($resultado = $resultados->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $resultado['competencia']; ?></td>
            <td><?php echo $resultado['problema']; ?></td>
            <td><?php echo $resultado['puntaje']; ?></td>
            <td><?php echo $resultado['intentos_fallidos']; ?></td>
            <td><?php echo $resultado['enviado_en']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
