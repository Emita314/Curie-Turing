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
<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
    <link rel="stylesheet" type="text/css" href="signup.css">
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form action="signup.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" placeholder="Tu nombre completo" required>
        
        <label>Email:</label>
        <input type="email" name="email" placeholder="ejemplo@correo.com" required>
        
        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="Crea una contraseña" required>
        
        <label>Rol:</label>
        <select name="rol" required>
            <option value="competidor">Competidor</option>
            <option value="administrador">Administrador</option>
        </select>
        
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>