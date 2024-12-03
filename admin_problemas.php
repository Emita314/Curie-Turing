<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Consulta para obtener los problemas agrupados por tipo
$sql = "SELECT tipo_problema, id_problema, titulo 
        FROM problemas 
        ORDER BY tipo_problema, titulo";
$result = $conn->query($sql);

// Array para agrupar problemas por tipo
$problemas_por_tipo = [];

// Procesar los resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tipo_problema = $row['tipo_problema'];
        $problemas_por_tipo[$tipo_problema][] = [
            'id_problema' => $row['id_problema'],
            'titulo' => $row['titulo']
        ];
    }
} else {
    echo "No se encontraron problemas.";
}

// Cerrar conexión
$conn->close();
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<main>
    <h2>Problemas</h2>

    <section class="options">
        <ul>
            <li><a href="crear_problema.php">Crear Nuevo Problema</a></li>
        </ul>
    </section>


    <?php if (!empty($problemas_por_tipo)) { ?>
        <?php foreach ($problemas_por_tipo as $tipo => $problemas) { ?>
            <section>
                <h2><?php echo htmlspecialchars($tipo); ?></h2>
                <ul>
                    <?php foreach ($problemas as $problema) { ?>
                        <li>
                            <strong>ID:</strong> <?php echo $problema['id_problema']; ?> 
                            - <?php echo htmlspecialchars($problema['titulo']); ?>
                        </li>
                    <?php } ?>
                </ul>
            </section>
        <?php } ?>
    <?php } else { ?>
        <p>No hay problemas registrados en la base de datos.</p>
    <?php } ?>

    <a href="dashboard_admin.php" class="back">Volver al Dashboard</a>
</main>

<?php include 'footer.php'; ?>