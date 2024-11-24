<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $nombre, $email, $password, $rol);

    if ($stmt->execute()) {
        echo "Usuario registrado exitosamente. <a href='login.php'>Inicia sesión aquí</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
</head>
<body>
    <h2>Registro</h2>
    <form action="signup.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Contraseña:</label>
        <input type="password" name="password" required>
        <br>
        <label>Rol:</label>
        <select name="rol">
            <option value="competidor">Competidor</option>
            <option value="administrador">Administrador</option>
        </select>
        <br>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>
