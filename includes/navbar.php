
<nav class="navbar position-fixed start-0 top-0 bottom-0 "
    style="width: auto; background-color: #fda087; border-radius: 0 15px 15px 0; z-index: 1000;">
    <div class="container-fluid d-flex flex-column  p-0">

        <a class=" text-center py-3" href="../visual/index.php">
            <img src="../img/logo.png" class="logo-nawbar" alt="Myfoods" >
    
        </a>
        <div class="nav-links ">

            <a href="../visual/index.php" class="nav-link p-3" title="Inicio">
                <i class="bi bi-house-door fs-2"></i>
                <span class="d-none d-md-inline">Inicio</span>
            </a>

            <a href="../visual/publishRecipe.php" class="nav-link p-3" title="Publicar receta">
                <i class="bi bi-plus-circle fs-2"></i> <span class="d-none d-md-inline">Publicar receta</span>
            </a>

            <a href="../visual/searchPage.php" class="nav-link p-3" title="Buscar recetas" id="scrollTopBtn">
                <i class="bi bi-search fs-2"></i> <span class="d-none d-md-inline">Buscar recetas</span>
            </a>

            <a href="../visual/savedRecipes.php" class="nav-link p-3 d-none d-md-flex" title="recetas guardadas">
                <i class="bi bi-bookmark fs-2"></i> <span class="d-none d-md-inline">Recetas guardadas</span>
            </a>

            <a href="../visual/account.php?username=<?php echo urlencode($_SESSION['userName']); ?>"
                class="nav-link p-3 d-none d-md-flex" title="Cuenta">
                <i class="bi bi-person-circle fs-2"></i> <span class="d-none d-md-inline">Cuenta</span>
            </a>

            <a href="../visual/exposuiza.php" class="nav-link p-3 d-none d-md-flex" title="exposuiza">
            
                <img src="../img/exposuiza.png" class="logo-exposuiza fs-2"></img> <span class="d-none d-md-inline">Exposuiza</span>

            </a>    
            <a href="../logout.php" class="nav-link p-3 d-none d-md-flex" title="Sobre Nosotros">
                <i class="bi bi-box-arrow-right fs-2"></i> <span class="d-none d-md-inline">cerrar sesion</span>
            </a>
        </div>

        <!-- Botón de perfil para celular -->
        <button class="profile-button d-md-none" id="profileToggleBtn" title="Menú">
            <img src="../getUserImage.php" alt="Foto de perfil" class="profile-btn-image">
        </button>

        <div class="nav-bottom mt-auto  text-center w-100 dropup d-none d-md-block">
            <hr>
            <div>v2025.11.4</div>
            <div> © MyFoods 2025</div>
            <div><a href="../visual/terms.php" class="author-link">Terminos y condiciones
            </a></div>
        </div>

    </div>
</nav>

<!-- Navbar expandido para celular -->
<nav class="navbar-expanded d-md-none" id="expandedNavbar">
    <div class="expanded-nav-content">
        <button class="close-navbar-btn" id="closeNavbarBtn">
            <i class="bi bi-x-lg"></i>
        </button>
        
        <a class=" text-center " href="../visual/index.php">
            <img src="../img/logo.png" class="logo-nawbar" alt="Myfoods" >
    
        </a>
        <div class="expanded-nav-links">
            <a href="../visual/index.php" class="expanded-nav-link">
                <i class="bi bi-house-door"></i> Inicio
            </a>
            <a href="../visual/publishRecipe.php" class="expanded-nav-link">
                <i class="bi bi-plus-circle"></i> Publicar receta
            </a>
            <a href="../visual/searchPage.php" class="expanded-nav-link">
                <i class="bi bi-search"></i> Buscar recetas
            </a>
            <a href="../visual/savedRecipes.php" class="expanded-nav-link">
                <i class="bi bi-bookmark"></i> Recetas guardadas
            </a>
            <a href="../visual/account.php?username=<?php echo urlencode($_SESSION['userName']); ?>" class="expanded-nav-link">
                <i class="bi bi-person-circle"></i> Cuenta
            </a>
            <a href="../visual/exposuiza.php" class="expanded-nav-link">
                <img src="../img/exposuiza.png" class="exposuiza-icon" alt="Exposuiza"> Exposuiza
            </a>
            <hr class="nav-divider">
            <a href="../logout.php" class="expanded-nav-link logout-link">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </a>
        </div>

        <div class="expanded-nav-footer">
            <div>v2025.11.4</div>
            <div>© MyFoods 2025</div>
            <div><a href="../visual/terms.php" class="author-link">Términos y condiciones</a></div>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="../css/navbar.css">
<!-- Scroll to top script -->
<script src="../js/scrollTop.js" defer></script>
<script>
    // Toggle del navbar expandido en celular
    const profileToggleBtn = document.getElementById('profileToggleBtn');
    const expandedNavbar = document.getElementById('expandedNavbar');
    const closeNavbarBtn = document.getElementById('closeNavbarBtn');

    profileToggleBtn.addEventListener('click', () => {
        expandedNavbar.classList.add('active');
    });

    closeNavbarBtn.addEventListener('click', () => {
        expandedNavbar.classList.remove('active');
    });

    // Cerrar al hacer clic en un link
    document.querySelectorAll('.expanded-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            expandedNavbar.classList.remove('active');
        });
    });
</script>