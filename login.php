<?php
session_start();
include 'db.php'; // Archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta para verificar el usuario por email
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        // Verificar contraseña cifrada
        if (password_verify($password, $usuario['contrasena'])) {
            // Inicio de sesión exitoso
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['nombre'] = $usuario['nombre'];

            // Redirigir según el rol del usuario
            if ($usuario['rol'] == 'administrador') {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_competidor.php");
            }
            exit;
        } else {
            echo "<p style='color: red;'>Credenciales incorrectas.</p>";
        }
    } else {
        echo "<p style='color: red;'>No se encontró el usuario.</p>";
    }
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciar sesion</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <h2>Iniciar Sesion</h2>
    <form action="login.php" method="POST">      
        <label>Email:</label>
        <input type="email" name="email" placeholder="ejemplo@correo.com" required>
        
        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="Crea una contraseña" required>

        <button type="submit">Iniciar sesion</button>
    </form>
</body>
</html>