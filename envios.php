<?php
session_start();
include 'db.php';

if ($_SESSION['rol'] !== 'competidor') {
    header("Location: dashboard.php");
    exit();
}

// Procesar subida de archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_problema = $_POST['id_problema'];
    $id_usuario = $_SESSION['id_usuario'];
    $enviado_en = date('Y-m-d H:i:s');

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $permitidas = ['cpp', 'py', 'js'];

        if (in_array($extension, $permitidas)) {
            $destino = 'uploads/' . uniqid('envio_', true) . '.' . $extension;
            move_uploaded_file($archivo['tmp_name'], $destino);

            $sql = "INSERT INTO envios (id_usuario, id_problema, archivo_ruta, enviado_en) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiss', $id_usuario, $id_problema, $destino, $enviado_en);
            $stmt->execute();

            echo "Archivo enviado exitosamente.";
        } else {
            echo "Extensión no permitida.";
        }
    } else {
        echo "Error en la subida del archivo.";
    }
}

// Mostrar problemas disponibles
$problemas = $conn->query("SELECT * FROM problemas");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enviar Solución</title>
</head>
<body>
    <h2>Enviar Solución</h2>
    <form action="envios.php" method="POST" enctype="multipart/form-data">
        <label>Problema:</label>
        <select name="id_problema" required>
            <?php while ($prob = $problemas->fetch_assoc()) { ?>
                <option value="<?php echo $prob['id_problema']; ?>">
                    <?php echo $prob['titulo']; ?>
                </option>
            <?php } ?>
        </select>
        <br>
        <label>Archivo:</label>
        <input type="file" name="archivo" required>
        <br>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
