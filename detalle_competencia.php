<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener los detalles de la competencia seleccionada
if (isset($_GET['id_competencia'])) {
    $id_competencia = $_GET['id_competencia'];

    // Obtener datos de la competencia
    $sql_competencia = "SELECT * FROM competencias WHERE id_competencia = ?";
    $stmt_competencia = $conn->prepare($sql_competencia);
    $stmt_competencia->bind_param("i", $id_competencia);
    $stmt_competencia->execute();
    $competencia = $stmt_competencia->get_result()->fetch_assoc();

    // Si la competencia no existe, redirigir
    if (!$competencia) {
        header("Location: dashboard_admin.php");
        exit;
    }

    // Verificar si la fecha de inicio ya pasó
    $fecha_actual = date("Y-m-d H:i:s");
    $competencia_iniciada = $fecha_actual > $competencia['fecha_inicio'];

    // Agregar problema nuevo a la competencia
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_nuevo_problema'])) {

        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $puntaje_base = $_POST['puntaje_base'];
        $resultado_esperado = $_POST['resultado_esperado'];
        $tipo_problema = $competencia['tipo_competencia'];

        // Insertar el nuevo problema en la base de datos
        $sql_insertar_problema = "INSERT INTO problemas (titulo, descripcion, puntaje_base, resultado_esperado, tipo_problema) 
                                  VALUES (?, ?, ?, ?, ?)";
        $stmt_insertar = $conn->prepare($sql_insertar_problema);
        $stmt_insertar->bind_param("ssdis", $titulo, $descripcion, $puntaje_base, $resultado_esperado, $tipo_problema);
        $stmt_insertar->execute();

        // Obtener el ID del problema recién insertado
        $id_nuevo_problema = $stmt_insertar->insert_id;

        // Asociar el nuevo problema a la competencia
        $sql_asociar_problema = "INSERT INTO competencia_problema (id_competencia, id_problema) VALUES (?, ?)";
        $stmt_asociar = $conn->prepare($sql_asociar_problema);
        $stmt_asociar->bind_param("ii", $id_competencia, $id_nuevo_problema);
        $stmt_asociar->execute();

        // Redirigir con mensaje de éxito
        header("Location: detalle_competencia.php?id_competencia=$id_competencia&mensaje=Problema%20agregado%20exitosamente");
        exit();
    }

    // Obtener problemas relacionados con esta competencia
    $sql_problemas = "
        SELECT p.*
        FROM problemas p
        INNER JOIN competencia_problema cp ON p.id_problema = cp.id_problema
        WHERE cp.id_competencia = ?";
    $stmt_problemas = $conn->prepare($sql_problemas);
    $stmt_problemas->bind_param("i", $id_competencia);
    $stmt_problemas->execute();
    $problemas_result = $stmt_problemas->get_result();

    // Obtener todos los problemas existentes del mismo tipo de competencia
    $sql_todos_problemas = "SELECT * FROM problemas WHERE tipo_problema = ? AND id_problema NOT IN (SELECT id_problema FROM competencia_problema WHERE id_competencia = ?)";
    $stmt_todos_problemas = $conn->prepare($sql_todos_problemas);
    $stmt_todos_problemas->bind_param("si", $competencia['tipo_competencia'], $id_competencia);
    $stmt_todos_problemas->execute();
    $todos_problemas_result = $stmt_todos_problemas->get_result();

    // Agregar problema existente a la competencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_existente'])) {
    $id_problema = $_POST['id_problema'];

    // Verificar si el problema ya está asociado a la competencia
    $sql_verificar = "SELECT * FROM competencia_problema WHERE id_competencia = ? AND id_problema = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $id_competencia, $id_problema);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();

    if ($resultado_verificar->num_rows === 0) {
        // Asociar el problema a la competencia
        $sql_asociar = "INSERT INTO competencia_problema (id_competencia, id_problema) VALUES (?, ?)";
        $stmt_asociar = $conn->prepare($sql_asociar);
        $stmt_asociar->bind_param("ii", $id_competencia, $id_problema);
        $stmt_asociar->execute();

        // Redirigir con mensaje de éxito
        header("Location: detalle_competencia.php?id_competencia=$id_competencia&mensaje=Problema%20agregado%20exitosamente");
        exit();
    } else {
        // Redirigir con mensaje de error si el problema ya está asociado
        header("Location: detalle_competencia.php?id_competencia=$id_competencia&mensaje=El%20problema%20ya%20está%20asociado%20a%20la%20competencia");
        exit();
    }
}

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Competencia</title>
    <link rel="stylesheet" href="detalle_competencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border-radius: 10px;
        }
        .modal.active {
            display: block;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        .modal-overlay.active {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Mostrar Snackbar si hay mensaje en la URL -->
    <?php if (isset($_GET['mensaje'])) { ?>
        <div id="snackbar"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php } ?>

    <h2>Detalles de Competencia: <?php echo htmlspecialchars($competencia['nombre']); ?></h2>

    <h3>Problemas de la Competencia</h3>
    <table>
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>Puntaje Base</th>
            <th>Resultado Esperado</th>
        </tr>
        <?php while ($problema = $problemas_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($problema['titulo']); ?></td>
            <td><?php echo htmlspecialchars($problema['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($problema['puntaje_base']); ?></td>
            <td><?php echo htmlspecialchars($problema['resultado_esperado']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <h3>Problemas Existentes (Tipo: <?php echo htmlspecialchars($competencia['tipo_competencia']); ?>)</h3>
    <table>
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>Puntaje Base</th>
            <th>Agregar</th>
        </tr>
        <?php while ($problema = $todos_problemas_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($problema['titulo']); ?></td>
            <td><?php echo htmlspecialchars($problema['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($problema['puntaje_base']); ?></td>
            <td>
                <form action="detalle_competencia.php?id_competencia=<?php echo $id_competencia; ?>" method="POST">
                    <input type="hidden" name="id_problema" value="<?php echo $problema['id_problema']; ?>">
                    <?php if ($competencia_iniciada): ?>
                    <!-- Botón deshabilitado si la competencia ya inició -->
                    <button type="submit" disabled style="background-color: #ccc; cursor: not-allowed;">Agregar a competencia</button>
                    <?php else: ?>
                    <!-- Botón habilitado si la competencia no ha iniciado -->
                    <button type="submit" name="agregar_existente">Agregar a Competencia</button>
                <?php endif; ?>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <!-- Botón para abrir el modal -->
    <?php if ($competencia_iniciada): ?>
        <!-- Botón deshabilitado si la competencia ya inició -->
        <button id="openModal" disabled style="background-color: #ccc; cursor: not-allowed;">Agregar Nuevo Problema</button>
    <?php else: ?>
        <!-- Botón habilitado si la competencia no ha iniciado -->
        <button id="openModal">Agregar Nuevo Problema</button>
    <?php endif; ?>

    <!-- Modal para agregar problema -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal" id="modal">
        <h3>Agregar Nuevo Problema</h3>
        <form action="detalle_competencia.php?id_competencia=<?php echo $id_competencia; ?>" method="POST">
            <label>Título:</label>
            <input type="text" name="titulo" required>

            <label>Descripción:</label>
            <textarea name="descripcion" required></textarea>

            <label>Puntaje Base:</label>
            <input type="number" name="puntaje_base" required>

            <label>Resultado Esperado:</label>
            <input type="number" name="resultado_esperado" required>

            <button type="submit" name="agregar_nuevo_problema">Agregar Problema</button>
        </form>
        <button id="closeModal">Cancelar</button>
    </div>


    <a href="dashboard_admin.php" class="back">Volver al Dashboard</a>

    <script>
        // Modal Script
        const modal = document.getElementById('modal');
        const modalOverlay = document.getElementById('modalOverlay');
        const openModalButton = document.getElementById('openModal');
        const closeModalButton = document.getElementById('closeModal');

        openModalButton.addEventListener('click', () => {
            modal.classList.add('active');
            modalOverlay.classList.add('active');
        });

        closeModalButton.addEventListener('click', () => {
            modal.classList.remove('active');
            modalOverlay.classList.remove('active');
        });
    </script>
</body>
</html>
