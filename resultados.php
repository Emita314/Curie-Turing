<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador o competidor
$rol = $_SESSION['rol'];
$id_usuario = $_SESSION['id_usuario'];

// Consultar resultados
if ($rol === 'administrador') {
    // Resultados globales por competencia
    $sql = "SELECT e.id_envio, e.puntaje, e.intentos_fallidos, e.enviado_en, 
                   p.titulo AS problema, c.nombre AS competencia, u.nombre AS competidor
            FROM envios e
            JOIN problemas p ON e.id_problema = p.id_problema
            JOIN competencias c ON p.id_competencia = c.id_competencia
            JOIN usuarios u ON e.id_usuario = u.id_usuario
            ORDER BY c.nombre, p.titulo, e.puntaje DESC";
} else {
    // Resultados solo del competidor actual
    $sql = "SELECT e.id_envio, e.puntaje, e.intentos_fallidos, e.enviado_en, 
                   p.titulo AS problema, c.nombre AS competencia
            FROM envios e
            JOIN problemas p ON e.id_problema = p.id_problema
            JOIN competencias c ON p.id_competencia = c.id_competencia
            WHERE e.id_usuario = ?
            ORDER BY c.nombre, p.titulo, e.puntaje DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
}

$result = $result ?? $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultados</title>
</head>
<body>
    <h2>Resultados de las Competencias</h2>
    <table border="1">
        <tr>
            <th>Competencia</th>
            <th>Problema</th>
            <th>Competidor</th>
            <th>Puntaje</th>
            <th>Intentos Fallidos</th>
            <th>Fecha de Envío</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['competencia']; ?></td>
            <td><?php echo $row['problema']; ?></td>
            <td><?php echo $rol === 'administrador' ? $row['competidor'] : 'Tú'; ?></td>
            <td><?php echo $row['puntaje']; ?></td>
            <td><?php echo $row['intentos_fallidos']; ?></td>
            <td><?php echo $row['enviado_en']; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
