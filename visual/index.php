<?php
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: logIn.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>myfoods - Inicio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
        
</head>

<body>
<nav id="myfoods-sidebar" class="d-flex flex-column p-3 bg-light border-end" style="width: 250px; height: 100vh; position: fixed; top:0; left:0; z-index: 1040; overflow-y:auto;">
  <a href="/visual/index.php" class="d-flex align-items-center mb-3 text-dark text-decoration-none">
    <span class="fs-4 fw-bold">MyFoods</span>
  </a>

  <hr>

  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="/visual/index.php" class="nav-link">Inicio</a>
    </li>
    <li class="nav-item">
      <a href="/publishRecipe.php" class="nav-link">Publicar receta</a>
    </li>
    <li class="nav-item">
      <a href="/perfil.php" class="nav-link">Perfil</a>
    </li>
    <li class="nav-item">
      <a href="/favoritos.php" class="nav-link">Favoritos</a>
    </li>
  </ul>

  <hr>
  <div class="mt-auto">
    <a href="/logout.php" class="btn btn-outline-secondary w-100">Cerrar sesión</a>
  </div>
</nav>

<!-- Ajuste para que el contenido no quede debajo del sidebar -->
<style>
  /* Important: usa padding-left en body o en un .content wrapper */
  body { padding-left: 250px !important; }

  /* Para pantallas pequeñas convertir el sidebar en un menú superior ocultable */
  @media (max-width: 768px) {
    #myfoods-sidebar { position: relative; width: 100%; height: auto; padding-bottom: 0; }
    body { padding-left: 0 !important; }
  }
</style>

    <h1>¡Bienvenido a myfoods!</h1>
    <p>La comunidad donde puedes compartir y descubrir recetas.</p>

    <a href="publishRecipe.php"><button>Publicar receta</button></a>

    <a href="account.php">
        <button>Ir a tu perfil</button>
    </a>

    <h2>Recetas recientes</h2>
    <div id="posts"></div>
    <script src="../js/posts.js"></script>
</body>

</html>