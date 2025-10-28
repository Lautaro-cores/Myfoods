<?php
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: logIn.php");
    exit();
}
$username = $_GET['username'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recetas guardadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/backButton.php'; ?>
    <div class="container mt-4">
        <h2><?php echo htmlspecialchars($username); ?></h2>
    <div>
        <a href="followers.php?username=<?php echo urlencode($username); ?>">Ver seguidores</a>
        <a href="following.php?username=<?php echo urlencode($username); ?>">Ver siguiendo</a>
    </div>

        <div id="followingList">Cargando...</div>

    </div>
    <script type="module" src="../js/account/followersList.js"></script>
</body>

</html>