<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$users = [];
$sql = "SELECT userId, userName, userEmail, userType FROM users ORDER BY userId DESC";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid, $uname, $uemail, $utype);
    while (mysqli_stmt_fetch($stmt)) {
        $users[] = ['userId' => $uid, 'userName' => $uname, 'userEmail' => $uemail, 'userType' => $utype];
    }
    mysqli_stmt_close($stmt);
}


$posts = [];
$sql = "SELECT p.postId, p.title, p.userId, p.postDate, u.userName FROM post p LEFT JOIN users u ON p.userId=u.userId ORDER BY p.postDate DESC";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid, $ptitle, $puserId, $ppostDate, $puserName);
    while (mysqli_stmt_fetch($stmt)) {
        $posts[] = ['postId' => $pid, 'title' => $ptitle, 'userId' => $puserId, 'postDate' => $ppostDate, 'userName' => $puserName];
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
<h1>Panel Admin</h1>

<h2>Usuarios</h2>
<table>
    <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Tipo</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['userId']) ?></td>
            <td><?= htmlspecialchars($u['userName']) ?></td>
            <td><?= htmlspecialchars($u['userEmail']) ?></td>
            <td><?= htmlspecialchars($u['userType']) ?></td>
            <td>
                <a class="btn" href="edit_user.php?id=<?= urlencode($u['userId']) ?>">Editar</a>
                <a class="btn del" href="delete.php?type=user&id=<?= urlencode($u['userId']) ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Publicaciones</h2>
<table>
    <thead><tr><th>ID</th><th>Título</th><th>Autor</th><th>Fecha</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($posts as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['postId']) ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['userName'] ?? $p['userId']) ?></td>
            <td><?= htmlspecialchars($p['postDate']) ?></td>
            <td>
                <a class="btn" href="view_post.php?id=<?= urlencode($p['postId']) ?>">Ver</a>
                <a class="btn" href="edit_post.php?id=<?= urlencode($p['postId']) ?>">Editar</a>
                <a class="btn del" href="delete.php?type=post&id=<?= urlencode($p['postId']) ?>" onclick="return confirm('¿Eliminar publicación?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
