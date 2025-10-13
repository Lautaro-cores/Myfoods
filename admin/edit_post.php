<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $sql = "UPDATE post SET title=?, description=? WHERE postId=?";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ssi', $title, $desc, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: index.php'); exit;
}

$post = null;
$sql = "SELECT postId, title, description FROM post WHERE postId=? LIMIT 1";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid, $ptitle, $pdescription);
    if (mysqli_stmt_fetch($stmt)) {
        $post = ['postId' => $pid, 'title' => $ptitle, 'description' => $pdescription];
    }
    mysqli_stmt_close($stmt);
}
if (!$post) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Editar Publicación</title></head><body>
<h1>Editar Publicación #<?= htmlspecialchars($post['postId']) ?></h1>
<form method="post">
    <label>Título<br><input name="title" value="<?= htmlspecialchars($post['title']) ?>"></label><br>
    <label>Descripción<br><textarea name="description" rows="8" cols="60"><?= htmlspecialchars($post['description']) ?></textarea></label><br>
    <button type="submit">Guardar</button>
    <a href="index.php">Cancelar</a>
</form>
</body></html>
