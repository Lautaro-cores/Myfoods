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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
<link rel="icon" href="../img/favicon.ico" type="image/x-icon">

</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <a href="publishRecipe.php">
        <button class="buttono">Publicar receta</button>
    </a>

    <a href="account.php?username=<?php echo urlencode($_SESSION['userName']); ?>">
        <button class="buttonw">Ir a tu perfil</button>
    </a>

    <div class="header-container">
        <div class="logo-container">
            <img src="../img/logo.png" alt="" id="logo" class="logo">
            <h1>MyFoods</h1>
        </div>

        <br>

        <div class="search-container">
           
            <input type="text" placeholder="buscar recetas" id="searchInput" class="input">
            <button id="searchButton" class="buttono" type="button">Buscar</button>
            <script>

            </script>
        </div>

    </div>

    <h2>Recetas recientes</h2> 
    <button class="buttono" id="loadMoreButton">Mas Likeados</button>
    <div id="posts"></div>
    <?php include '../includes/reportModal.php'; ?>
    <script src="../js/indexRecipes/posts.js"></script>
    <script src="../js/report/report.js" defer></script>
</body>

</html>