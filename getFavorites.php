<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['error' => 'not_authenticated']);
    exit();
}

$userId = intval($_SESSION['userId']);

$posts = [];

$sql = "SELECT p.postId, p.title, p.description, p.postDate, u.displayName, u.userImage,
         (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount
     FROM post p
     JOIN favorites f ON f.postId = p.postId
     JOIN users u ON p.userId = u.userId
     WHERE f.userId = ?
     ORDER BY f.created_at DESC";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($res)) {
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    // Obtener todas las imágenes de la receta
    $row['images'] = [];
    $sqlImg = "SELECT imageData FROM recipeImages WHERE postId = ? ORDER BY imageOrder ASC";
    $stmtImg = mysqli_prepare($con, $sqlImg);
    if ($stmtImg) {
        mysqli_stmt_bind_param($stmtImg, 'i', $row['postId']);
        mysqli_stmt_execute($stmtImg);
        mysqli_stmt_bind_result($stmtImg, $imageData);
        while (mysqli_stmt_fetch($stmtImg)) {
            $row['images'][] = base64_encode($imageData);
        }
        mysqli_stmt_close($stmtImg);
    }
    $row['likesCount'] = isset($row['likesCount']) ? intval($row['likesCount']) : 0;
    $posts[] = $row;
}

echo json_encode($posts);
