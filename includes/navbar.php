<nav class="navbar position-fixed start-0 top-0 bottom-0 "
    style="width: auto; background-color: #fda087 !important; border-radius: 0 15px 15px 0; z-index: 1000;">
    <div class="container-fluid d-flex flex-column  p-0">

        <a class=" text-center py-3" href="../visual/index.php">
            <img src="../img/logo.png" class="logo-nawbar" alt="Myfoods" >
    
        </a>
        <div class="nav-links d-flex flex-column align-items-center ">

            <a href="../visual/index.php" class="nav-link p-3" title="Inicio">
                <i class="bi bi-house-door fs-2"></i> <span class="d-none d-md-inline">Inicio</span>
            </a>

            <a href="../visual/publishRecipe.php" class="nav-link p-3" title="Publicar receta">
                <i class="bi bi-plus-circle fs-2"></i> <span class="d-none d-md-inline">Publicar receta</span>
            </a>

            <a href="../visual/searchPage.php" class="nav-link p-3" title="Buscar recetas" id="scrollTopBtn">
                <i class="bi bi-search fs-2"></i> <span class="d-none d-md-inline">Buscar recetas</span>
            </a>

            <a href="../visual/savedRecipes.php" class="nav-link p-3" title="recetas guardadas">
                <i class="bi bi-bookmark fs-2"></i> <span class="d-none d-md-inline">Recetas guardadas</span>
            </a>

            <a href="../visual/account.php?username=<?php echo urlencode($_SESSION['userName']); ?>"
                class="nav-link p-3" title="Cuenta">
                <i class="bi bi-person-circle fs-2"></i> <span class="d-none d-md-inline">Cuenta</span>
            </a>

            <a href="../visual/exposuiza.php" class="nav-link p-3" title="exposuiza">
            
                <img src="../img/exposuiza.png" class="logo-exposuiza"></img> <span class="d-none d-md-inline">Exposuiza</span>

            </a>    
            <a href="../logout.php" class="nav-link p-3" title="Sobre Nosotros">
                <i class="bi bi-box-arrow-right fs-2"></i> <span class="d-none d-md-inline">cerrar sesion</span>
            </a>
        </div>


        <div class="nav-bottom mt-auto  text-center w-100 dropup">
            <hr>
            <div>v2025.11.4</div>
            <div> © MyFoods 2025</div>
            <div><a href="../visual/terms.php" class="author-link">Terminos y condiciones
            </a></div>
        </div>

    </div>
</nav>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


<script src="../js/scrollTop.js" defer></script>