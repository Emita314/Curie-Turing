<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: dashboard.php");
    exit();
}

$id_competencia = $_GET['id_competencia'] ?? null;
if (!$id_competencia) {
    echo "ID de competencia no válido.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $puntaje_base = $_POST['puntaje_base'] ?? 100; // Puntaje base predeterminado

    // Preparar la consulta para insertar el nuevo problema
    $sql = "INSERT INTO problemas (id_competencia, titulo, descripcion, puntaje_base) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $id_competencia, $titulo, $descripcion, $puntaje_base);

    if ($stmt->execute()) {
        echo "Problema agregado exitosamente.";
        header("Location: editar_competencia.php?id_competencia=$id_competencia");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agregar Problema</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h2>Agregar Problema a la Competencia</h2>

    <!-- Formulario para agregar un nuevo problema -->
    <form action="" method="POST">
        <label for="titulo">Título del Problema:</label>
        <input type="text" name="titulo" id="titulo" required>
        
        <label for="descripcion">Descripción del Problema:</label>
        <textarea name="descripcion" id="descripcion" required></textarea>
        
        <label for="puntaje_base">Puntaje Base:</label>
        <input type="number" name="puntaje_base" id="puntaje_base" value="100" min="1" required>

        <button type="submit">Agregar Problema</button>
    </form>

    <br>
    <a href="editar_competencia.php?id_competencia=<?php echo $id_competencia; ?>">Volver a la Competencia</a>
</body>
</html>
