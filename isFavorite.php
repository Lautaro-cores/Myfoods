<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET['postId'])) {
    echo json_encode(['error' => 'postId_required']);
    exit();
}

$postId = intval($_GET['postId']);
$isFav = false;

if (isset($_SESSION['userId'])) {
    $userId = intval($_SESSION['userId']);
    $sql = "SELECT 1 FROM favorites WHERE postId = ? AND userId = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $postId, $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_fetch_assoc($res)) $isFav = true;
}

echo json_encode(['isFavorite' => $isFav]);
