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
 
  <!-- Estilos de publicaciones -->
  <link rel="stylesheet" href="../css/styleT.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
        
</head>

<body>
<?php include '../nawbar.php'; ?>

      <div id="topBar">
    
    <div id="buttonGroup"><button id="post">Publica tu receta</button><button id="login">Iniciar Sesión</button></div>

    </div>

    <div id="content">

    <div>
    <img src="../img/logo.png" alt="" id="logo">
    <h1 id="titulo">MyFoods</h1>
    </div>

    <br>

    <div id="buscador">
    <input type="text" placeholder="buscar recetas" id="searchInput">
    <button id="searchButton">Buscar</button>
    </div>

    <a href="publishRecipe.php"><button>Publicar receta</button></a>

    <a href="account.php">
        <button>Ir a tu perfil</button>
    </a>

    <h2>Recetas recientes</h2>
    <div id="posts"></div>
    <script src="../js/posts.js"></script>
</body>

</html>