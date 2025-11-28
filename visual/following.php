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
    <title>Siguiendo - <?php echo htmlspecialchars($username); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/backButton.php'; ?>
    
    <div class="followers-following-container">
        <h1><?php echo htmlspecialchars($username); ?> - Siguiendo</h1>
        
        <div class="nav-links">
            <a href="followers.php?username=<?php echo urlencode($username); ?>" 
               class="btn btn-outline-dark rounded-pill">
                <i class="bi bi-people-fill"></i> Seguidores
            </a>
            <a href="following.php?username=<?php echo urlencode($username); ?>" 
               class="btn btn-dark rounded-pill">
                <i class="bi bi-person-plus-fill"></i> Siguiendo
            </a>
        </div>

        <div id="followingList">Cargando...</div>
    </div>

    <script type="module" src="../js/account/followersList.js"></script>
</body>
                            <span class="following-count"></span>
                        </div>
                    </div>
                    <button class="follow-button btn btn-outline-dark rounded-pill">Seguir</button>
                </div>
            </template>
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="../js/account/followersList.js"></script>
</body>
</html>