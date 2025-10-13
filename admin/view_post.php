<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: index.php'); exit; }

$post = null;
$sql = "SELECT p.postId, p.title, p.description, p.postDate, p.recipeImage, p.userId, u.userName FROM post p LEFT JOIN users u ON p.userId=u.userId WHERE p.postId=? LIMIT 1";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid, $ptitle, $pdescription, $ppostDate, $precipeImage, $puserId, $puserName);
    if (mysqli_stmt_fetch($stmt)) {
        $post = ['postId' => $pid, 'title' => $ptitle, 'description' => $pdescription, 'postDate' => $ppostDate, 'recipeImage' => $precipeImage, 'userId' => $puserId, 'userName' => $puserName];
    }
    mysqli_stmt_close($stmt);
}
if (!$post) { header('Location: index.php'); exit; }


$ingredients = [];
$sql = "SELECT ingredient FROM ingredientrecipe WHERE postId=? ORDER BY ingredientId ASC";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ingredient);
    while (mysqli_stmt_fetch($stmt)) $ingredients[] = $ingredient;
    mysqli_stmt_close($stmt);
}


$steps = [];
$sql = "SELECT step FROM recipestep WHERE postId=? ORDER BY recipeStepId ASC";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $step);
    while (mysqli_stmt_fetch($stmt)) $steps[] = $step;
    mysqli_stmt_close($stmt);
}


$likesCount = 0;
$sql = "SELECT COUNT(*) FROM likes WHERE postId=?";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    if (mysqli_stmt_fetch($stmt)) $likesCount = intval($count);
    mysqli_stmt_close($stmt);
}


$comments = [];
$sql = "SELECT c.commentId, c.content, c.userId, u.userName FROM comment c LEFT JOIN users u ON c.userId=u.userId WHERE c.postId=? ORDER BY c.commentId ASC";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $commentId, $commentContent, $commentUserId, $commentUserName);
    while (mysqli_stmt_fetch($stmt)) {
        $comments[] = ['commentId' => $commentId, 'content' => $commentContent, 'userId' => $commentUserId, 'userName' => $commentUserName];
    }
    mysqli_stmt_close($stmt);
}

?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Ver Publicación</title></head><body>
<h1><?= htmlspecialchars($post['title']) ?></h1>
<p><strong>Autor:</strong> <?= htmlspecialchars($post['userName'] ?? $post['userId']) ?> — <strong>Fecha:</strong> <?= htmlspecialchars($post['postDate']) ?></p>

<?php if (!empty($post['recipeImage'])): ?>
    <div><img src="data:image/jpeg;base64,<?= base64_encode($post['recipeImage']) ?>" alt="Imagen" style="max-width:400px;height:auto;border:1px solid #ccc;padding:4px"></div>
<?php endif; ?>

<h3>Descripción</h3>
<div><?= nl2br(htmlspecialchars($post['description'])) ?></div>

<?php if (count($ingredients)): ?>
    <h3>Ingredientes</h3>
    <ul><?php foreach ($ingredients as $ing): ?><li><?= htmlspecialchars($ing) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<?php if (count($steps)): ?>
    <h3>Pasos</h3>
    <ol><?php foreach ($steps as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ol>
<?php endif; ?>

<p><strong>Likes:</strong> <?= $likesCount ?></p>

<?php if (count($comments)): ?>
    <h3>Comentarios (<?= count($comments) ?>)</h3>
    <ul>
    <?php foreach ($comments as $c): ?>
        <li><strong><?= htmlspecialchars($c['userName'] ?? $c['userId']) ?>:</strong> <?= htmlspecialchars($c['content']) ?></li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay comentarios.</p>
<?php endif; ?>

<p><a href="index.php">Volver</a> <a href="edit_post.php?id=<?= urlencode($post['postId']) ?>">Editar</a></p>
</body></html>
