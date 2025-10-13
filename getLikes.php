<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET['postId'])) {
    echo json_encode(['error' => 'postId_required']);
    exit();
}

$postId = intval($_GET['postId']);

// Obtener cantidad de likes
$sql = "SELECT COUNT(*) AS cnt FROM likes WHERE postId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$count = intval($row['cnt'] ?? 0);

$userLiked = false;
if (isset($_SESSION['userId'])) {
    $userId = intval($_SESSION['userId']);
    $sql2 = "SELECT 1 FROM likes WHERE postId = ? AND userId = ? LIMIT 1";
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $postId, $userId);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    if (mysqli_fetch_assoc($res2)) {
        $userLiked = true;
    }
}

echo json_encode([
    'likesCount' => $count,
    'userLiked' => $userLiked
]);
