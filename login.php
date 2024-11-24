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

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<main class="main-content">
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit" class="button">Ingresar</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>
