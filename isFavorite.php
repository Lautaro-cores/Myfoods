<?php
// isFavorite.php
// este archivo verifica si una publicación es favorita por el usuario

session_start();
//se conecta a la base de datos
require_once "includes/config.php";

header('Content-Type: application/json');
// verifica si se proporcionó el postId
if (!isset($_GET['postId'])) {
    echo json_encode(['error' => 'postId_required']);
    exit();
}
// obtiene el ID de la publicación
$postId = intval($_GET['postId']);
$isFav = false;
// si el usuario ha iniciado sesión, verifica si la publicación es favorita
if (isset($_SESSION['userId'])) {
    $userId = intval($_SESSION['userId']);
    // hace la consulta para verificar si la publicación es favorita
    $sql = "SELECT 1 FROM favorites WHERE postId = ? AND userId = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $postId, $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_fetch_assoc($res)) $isFav = true;
}

echo json_encode(['isFavorite' => $isFav]);
