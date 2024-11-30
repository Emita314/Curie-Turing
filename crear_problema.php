<?php
session_start();
include 'db.php';

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $puntaje_base = $_POST['puntaje_base'] ?? 100; // Puntaje base predeterminado
    $resultado_esperado = $_POST['resultado_esperado'];
    $tipo_problema = $_POST['tipo_problema'];

    // Validar tipo_problema para asegurar que sea un valor permitido
    $tipos_permitidos = ['Informatica', 'Fisica', 'Quimica', 'Matematicas'];
    if (!in_array($tipo_problema, $tipos_permitidos)) {
        echo "Error: Tipo de problema no válido.";
        exit();
    }

    // Preparar la consulta para insertar el nuevo problema
    $sql = "INSERT INTO problemas (titulo, descripcion, puntaje_base, resultado_esperado, tipo_problema) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiss', $titulo, $descripcion, $puntaje_base, $resultado_esperado, $tipo_problema);

    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        echo "Problema agregado exitosamente.";
        header("Location: admin_problemas.php");
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
<h2>Agregar Problema</h2>

    <!-- Formulario para agregar un nuevo problema -->
    <form action="" method="POST">
        <label for="titulo">Título del Problema:</label>
        <input type="text" name="titulo" id="titulo" required>
        
        <label for="descripcion">Descripción del Problema:</label>
        <textarea name="descripcion" id="descripcion" required></textarea>
        
        <label for="puntaje_base">Puntaje Base:</label>
        <input type="number" name="puntaje_base" id="puntaje_base" value="100" min="1" required>

        <label for="resultado_esperado">Resultado esperado:</label>
        <input type="text" name="resultado_esperado" id="resultado_esperado" required>

        <label for="tipo_problema">Tipo de Problema:</label>
        <select name="tipo_problema" id="tipo_problema" required>
            <option value="">Seleccione un tipo</option>
            <option value="Matematicas">Matemáticas</option>
            <option value="Fisica">Fisica</option>
            <option value="Quimica">Quimica</option>
            <option value="Informatica">Informatica</option>
        </select>

        <button type="submit">Agregar Problema</button>
    </form>
    <br>
    <a href="admin_problemas.php?id_competencia=<?php echo $id_competencia; ?>">Volver</a>
</body>
</html>
