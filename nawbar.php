<?php
session_start();
?>

<nav class="navbar position-fixed start-0 top-0 bottom-0 h-100" style="width: 120px; background-color: #fae1da !important; border-radius: 0 15px 15px 0; z-index: 1000;">
    <div class="container-fluid d-flex flex-column h-100 p-0">
        <!-- Logo -->
        <a class="navbar-brand text-center py-3" href="../index.php">
            <img src="../img/logo.png" alt="Myfoods Logo" style="width: 80px;">
        </a>


        <!-- Iconos de cuenta y otros en la parte inferior -->
        <div class="nav-bottom mb-3 text-center w-100">
            <!-- Icono de cuenta -->
            <a href="visual/account.php" class="nav-link p-2" title="Cuenta">
                <i class="bi bi-person-circle fs-4"></i>
            </a>
            <!-- Icono placeholder -->
            <a href="#" class="nav-link p-2" title="Próximamente">
                <i class="bi bi-three-dots fs-4"></i>
            </a>
        </div>
    </div>
</nav>

<!-- Agrega los íconos de Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Estilos específicos para el navbar -->
<style>
.navbar {
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.nav-link {
    color: rgba(0, 0, 0, 0.7);
    transition: color 0.2s;
    padding: 0.5rem;
}

.nav-link:hover {
    color: rgba(0, 0, 0, 1);
}

/* Ajuste para el contenido principal */
body {
    padding-left: 120px;
}

/* Estilos para los iconos */
.bi {
    font-size: 1.5rem;
}
</style>
