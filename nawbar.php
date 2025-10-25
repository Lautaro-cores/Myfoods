<nav class="navbar position-fixed start-0 top-0 bottom-0 h-100"
    style="width: 120px; background-color: #fda087 !important; border-radius: 0 15px 15px 0; z-index: 1000;">
    <div class="container-fluid d-flex flex-column h-100 p-0">

        <a class=" text-center py-3" href="../visual/index.php">
            <img src="../img/logo.png" alt="Myfoods" >
        </a>
        <div class="nav-links d-flex flex-column align-items-center ">

            <a href="../visual/index.php" class="nav-link p-3" title="Inicio">
                <i class="bi bi-house-door fs-2"></i>
            </a>

            <a href="../visual/publishRecipe.php" class="nav-link p-3" title="Publicar receta">
                <i class="bi bi-plus-circle fs-2"></i>
            </a>

            <a href="../visual/searchPage.php" class="nav-link p-3" title="Buscar recetas" id="scrollTopBtn">
                <i class="bi bi-search fs-2"></i>
            </a>

            <a href="../visual/savedRecipes.php" class="nav-link p-3" title="recetas guardadas">
                <i class="bi bi-bookmark fs-2"></i>
            </a>

            <a href="../visual/account.php?username=<?php echo urlencode($_SESSION['userName']); ?>"
                class="nav-link p-3" title="Cuenta">
                <i class="bi bi-person-circle fs-2"></i>
            </a>
        </div>


        <div class="nav-bottom mt-auto mb-3 text-center w 100 dropup">
            <button class="nav-link p-2 d-flex justify-content-center align-items-center mx-auto" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots fs-2"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="../visual/aboutUs.php">Sobre Nosotros</a></li>
                <li><a class="dropdown-item" href="../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>

    </div>
</nav>


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

    .container-fluid a img {
        width: 120px;
    }

    /* Estilos para los iconos */
    .bi {
        font-size: 1.5rem;
    }
</style>
<!-- Scroll to top script -->
<script src="../js/scrollTop.js" defer></script>