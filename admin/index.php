<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$users = [];

$users = [];
$usersPerPage = 10;
$usersPage = isset($_GET['usersPage']) ? max(1, intval($_GET['usersPage'])) : 1;
$usersOffset = ($usersPage - 1) * $usersPerPage;
$sqlCountUsers = "SELECT COUNT(*) as total FROM users";
$resultCountUsers = mysqli_query($con, $sqlCountUsers);
$totalUsers = mysqli_fetch_assoc($resultCountUsers)['total'];
$totalUsersPages = ceil($totalUsers / $usersPerPage);
$sql = "SELECT u.userId, u.userName, u.displayName, u.userEmail, u.userType, u.userImage,
       (SELECT COUNT(*) FROM post WHERE userId = u.userId) as postCount,
       (SELECT COUNT(*) FROM comment WHERE userId = u.userId) as commentCount
       FROM users u 
       ORDER BY u.userId DESC
       LIMIT $usersPerPage OFFSET $usersOffset";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = [
            'userId' => $row['userId'],
            'userName' => $row['userName'],
            'displayName' => $row['displayName'],
            'userEmail' => $row['userEmail'],
            'userType' => $row['userType'],
            'postCount' => $row['postCount'],
            'commentCount' => $row['commentCount']
        ];
    }
    mysqli_stmt_close($stmt);
}


$posts = [];

$posts = [];
$postsPerPage = 10;
$postsPage = isset($_GET['postsPage']) ? max(1, intval($_GET['postsPage'])) : 1;
$postsOffset = ($postsPage - 1) * $postsPerPage;
$sqlCountPosts = "SELECT COUNT(*) as total FROM post";
$resultCountPosts = mysqli_query($con, $sqlCountPosts);
$totalPosts = mysqli_fetch_assoc($resultCountPosts)['total'];
$totalPostsPages = ceil($totalPosts / $postsPerPage);
$sql = "SELECT p.postId, p.title, p.description, p.userId, p.postDate, u.userName, 
       (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) as likesCount,
       (SELECT COUNT(*) FROM comment c WHERE c.postId = p.postId) as commentsCount,
       (SELECT COUNT(*) FROM favorites f WHERE f.postId = p.postId) as favoritesCount
       FROM post p 
       LEFT JOIN users u ON p.userId = u.userId 
       ORDER BY p.postDate DESC
       LIMIT $postsPerPage OFFSET $postsOffset";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = [
            'postId' => $row['postId'],
            'title' => $row['title'],
            'description' => $row['description'],
            'userId' => $row['userId'],
            'postDate' => $row['postDate'],
            'userName' => $row['userName'],
            'likesCount' => $row['likesCount'],
            'commentsCount' => $row['commentsCount'],
            'favoritesCount' => $row['favoritesCount']
        ];
    }
    mysqli_stmt_close($stmt);
}

// Obtener ingredientes
$ingredients = [];
$ingredientsPerPage = 10;
$ingredientsPage = isset($_GET['ingredientsPage']) ? max(1, intval($_GET['ingredientsPage'])) : 1;
$ingredientsOffset = ($ingredientsPage - 1) * $ingredientsPerPage;
$sqlCountIngredients = "SELECT COUNT(*) as total FROM ingredients";
$resultCountIngredients = mysqli_query($con, $sqlCountIngredients);
$totalIngredients = mysqli_fetch_assoc($resultCountIngredients)['total'];
$totalIngredientsPages = ceil($totalIngredients / $ingredientsPerPage);
$sql = "SELECT ingredientId, name FROM ingredients ORDER BY name ASC LIMIT $ingredientsPerPage OFFSET $ingredientsOffset";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $ingredients[] = [
            'ingredientId' => $row['ingredientId'],
            'name' => $row['name']
        ];
    }
    mysqli_stmt_close($stmt);
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Admin - Myfoods</title>
    <link href="../css/main.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-header">
        <h1>Panel Administrativo de Myfoods</h1>
        <a href="reports.php" class="admin-btn" style="margin-left:20px;">Ver reportes</a>
    </div>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-label">Usuarios totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $totalPosts ?></div>
            <div class="stat-label">Recetas publicadas</div>
        </div>

    </div>

<h2>Usuarios</h2>
<table class="admin-table">
    <thead><tr><th>ID</th><th>Nombre</th><th>displayName</th><th>Email</th><th>Tipo</th><th>Recetas</th><th>Comentarios</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['userId']) ?></td>
            <td><?= htmlspecialchars($u['userName']) ?></td>
            <td><?= htmlspecialchars($u['displayName']) ?></td>
            <td><?= htmlspecialchars($u['userEmail']) ?></td>
            <td><?= htmlspecialchars($u['userType']) ?></td>
            <td><?= htmlspecialchars($u['postCount']) ?></td>
            <td><?= htmlspecialchars($u['commentCount']) ?></td>
            <td>
                <a class="admin-btn" href="../visual/account.php?username=<?= urlencode($u['userName']) ?>">Ver</a>
                <a class="admin-btn" href="edit_user.php?id=<?= urlencode($u['userId']) ?>">Editar</a>
                <a class="admin-btn secondary" href="delete.php?type=user&id=<?= urlencode($u['userId']) ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalUsersPages; $i++): ?>
        <a href="?usersPage=<?= $i ?>" class="btn<?= $i == $usersPage ? ' btn-info' : '' ?>"> <?= $i ?> </a>
    <?php endfor; ?>
</div>

<h2>Publicaciones</h2>
<table class="admin-table">
    <thead><tr><th>ID</th><th>Título</th><th>Autor</th><th>Fecha</th><th>Me gusta</th><th>Comentarios</th><th>Guardados</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($posts as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['postId']) ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['userName'] ?? $p['userId']) ?></td>
            <td><?= htmlspecialchars($p['postDate']) ?></td>
            <td><?= htmlspecialchars($p['likesCount']) ?></td>
            <td><?= htmlspecialchars($p['commentsCount']) ?></td>
            <td><?= htmlspecialchars($p['favoritesCount']) ?></td>
            <td>
                <a class="admin-btn" href="../visual/viewRecipe.php?id=<?= urlencode($p['postId']) ?>">Ver</a>
                <a class="admin-btn" href="edit_post.php?id=<?= urlencode($p['postId']) ?>">Editar</a>
                <a class="admin-btn secondary" href="delete.php?type=post&id=<?= urlencode($p['postId']) ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar esta receta?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalPostsPages; $i++): ?>
        <a href="?postsPage=<?= $i ?>" class="btn<?= $i == $postsPage ? ' btn-info' : '' ?>"> <?= $i ?> </a>
    <?php endfor; ?>
</div>

<h2>Ingredientes</h2>
<button id="addIngredientToTable" class="admin-btn" style="margin-bottom:20px;">Agregar ingrediente</button>
<table id="ingredients-table" class="admin-table">
    <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($ingredients as $ing): ?>
        <tr>
            <td><?= htmlspecialchars($ing['ingredientId']) ?></td>
            <td><?= htmlspecialchars($ing['name']) ?></td>
            <td><button class="btn btn-danger delete-ingredient-btn" data-id="<?= $ing['ingredientId'] ?>">Eliminar</button></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalIngredientsPages; $i++): ?>
        <a href="?ingredientsPage=<?= $i ?>" class="btn<?= $i == $ingredientsPage ? ' btn-info' : '' ?>"> <?= $i ?> </a>
    <?php endfor; ?>
</div>

</body>
<script src="js/edit_post_dynamic.js"></script>
</html>
