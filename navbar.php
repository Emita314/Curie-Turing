<header class="navbar">
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <a href="index.php">CurieTuring</a>
        </div>

        <!-- Menú de navegación debajo del logo -->
        <nav class="nav-links">
            <ul>
                <li><a href="welcome.php">Inicio</a></li>
                <?php
                // Verificar si el usuario está autenticado
                if (isset($_SESSION['id_usuario'])) {
                    // Mostrar opciones basadas en el rol del usuario
                    if ($_SESSION['rol'] == 'administrador') {
                        echo '<li><a href="dashboard_admin.php">Gestión de Competencias</a></li>';
                    } elseif ($_SESSION['rol'] == 'competidor') {
                        echo '<li><a href="competencias.php">Competencias</a></li>';
                    }
                }
                else{
                    echo '<li><a href="login.php">Iniciar sesion</a></li>';
                    echo '<li><a href="signup.php">Registrar</a></li>';
                }
                ?>
            </ul>
        </nav>

        <!-- Icono de perfil en la derecha -->
        <div class="profile-icon">
            <?php
            if (isset($_SESSION['id_usuario'])) {
                // Mostrar icono de perfil si el usuario está logueado
                echo '
                <li class="dropdown">
                    <a href="#" class="dropbtn">
                        <i class="fas fa-user"></i> Perfil <i class="fas fa-caret-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="perfil.php">Ver Perfil</a>
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </li>';
            }
            ?>
        </div>
    </div>
</header>
