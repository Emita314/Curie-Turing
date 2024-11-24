<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: dashboard.php");
    exit();
}

// Crear problema
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $id_competencia = $_POST['id_competencia'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $puntaje_base = $_POST['puntaje_base'];

    $sql = "INSERT INTO problemas (id_competencia, titulo, descripcion, puntaje_base) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issi', $id_competencia, $titulo, $descripcion, $puntaje_base);

    if ($stmt->execute()) {
        echo "Problema creado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Eliminar problema
if (isset($_GET['eliminar'])) {
    $id_problema = $_GET['eliminar'];
    $sql = "DELETE FROM problemas WHERE id_problema = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_problema);
    $stmt->execute();
    echo "Problema eliminado.";
}

// Obtener problemas y competencias
$competencias = $conn->query("SELECT * FROM competencias");
$problemas = $conn->query("SELECT problemas.*, competencias.nombre AS competencia 
                           FROM problemas 
                           JOIN competencias ON problemas.id_competencia = competencias.id_competencia");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Problemas</title>
</head>
<body>
    <h2>Administrar Problemas</h2>
    <h3>Crear Nuevo Problema</h3>
    <form action="admin_problemas.php" method="POST">
        <label>Competencia:</label>
        <select name="id_competencia" required>
            <?php while ($comp = $competencias->fetch_assoc()) { ?>
                <option value="<?php echo $comp['id_competencia']; ?>">
                    <?php echo $comp['nombre']; ?>
                </option>
            <?php } ?>
        </select>
        <br>
        <label>Título:</label>
        <input type="text" name="titulo" required>
        <br>
        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>
        <br>
        <label>Puntaje Base:</label>
        <input type="number" name="puntaje_base" required>
        <br>
        <button type="submit" name="crear">Crear</button>
    </form>

    <h3>Lista de Problemas</h3>
    <table border="1">
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>Puntaje</th>
            <th>Competencia</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $problemas->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['titulo']; ?></td>
            <td><?php echo $row['descripcion']; ?></td>
            <td><?php echo $row['puntaje_base']; ?></td>
            <td><?php echo $row['competencia']; ?></td>
            <td>
                <a href="admin_problemas.php?eliminar=<?php echo $row['id_problema']; ?>">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
