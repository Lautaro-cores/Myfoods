<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$users = [];
$sql = "SELECT u.userId, u.userName, u.displayName, u.userEmail, u.userType, u.userImage,
       (SELECT COUNT(*) FROM post WHERE userId = u.userId) as postCount,
       (SELECT COUNT(*) FROM comment WHERE userId = u.userId) as commentCount
       FROM users u 
       ORDER BY u.userId DESC";
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
$sql = "SELECT p.postId, p.title, p.description, p.userId, p.postDate, u.userName, 
       (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) as likesCount,
       (SELECT COUNT(*) FROM comment c WHERE c.postId = p.postId) as commentsCount,
       (SELECT COUNT(*) FROM favorites f WHERE f.postId = p.postId) as favoritesCount
       FROM post p 
       LEFT JOIN users u ON p.userId = u.userId 
       ORDER BY p.postDate DESC";
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
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Admin - Myfoods</title>
    <link rel="stylesheet" href="../css/styleP.css">
    <style>table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #ddd;text-align:left}a.btn{padding:4px 8px;background:#2d89ef;color:#fff;text-decoration:none;border-radius:4px;margin-right:6px}a.del{background:#e04343}</style>
</head>
<body>
    <h1>Panel Administrativo de Myfoods</h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="stats-panel">
        <div class="stat-card">
            <div class="stat-number"><?= count($users) ?></div>
            <div class="stat-label">Usuarios totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count($posts) ?></div>
            <div class="stat-label">Recetas publicadas</div>
        </div>
    </div>

<h2>Usuarios</h2>
<table>
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
                <a class="btn btn-primary" href="edit_user.php?id=<?= urlencode($u['userId']) ?>">Editar</a>
                <a class="btn btn-danger" href="delete.php?type=user&id=<?= urlencode($u['userId']) ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Publicaciones</h2>
<table>
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
                <a class="btn btn-primary" href="view_post.php?id=<?= urlencode($p['postId']) ?>">Ver</a>
                <a class="btn btn-primary" href="edit_post.php?id=<?= urlencode($p['postId']) ?>">Editar</a>
                <a class="btn btn-danger" href="delete.php?type=post&id=<?= urlencode($p['postId']) ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar esta receta?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
