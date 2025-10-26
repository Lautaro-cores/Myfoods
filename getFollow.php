<?php
session_start();
require_once "includes/config.php";

header("Content-Type: application/json");

if (!isset($_GET['followingUserId'])) {
    echo json_encode(["error" => "No se especificó el ID del usuario que se sigue."]);
    exit();
}


$followingUserId = (int)$_GET['followingUserId'];
$userId = $_SESSION['userId'] ?? 0;

// Obtener cantidad de seguidores
$sqlFollowers = "SELECT COUNT(*) as followers FROM followers WHERE following_userId = ?";
$stmtFollowers = mysqli_prepare($con, $sqlFollowers);
mysqli_stmt_bind_param($stmtFollowers, "i", $followingUserId);
mysqli_stmt_execute($stmtFollowers);
$followersCount = mysqli_stmt_get_result($stmtFollowers)->fetch_assoc()['followers'];

// Obtener cantidad de seguidos
$sqlFollowing = "SELECT COUNT(*) as following FROM followers WHERE follower_userId = ?";
$stmtFollowing = mysqli_prepare($con, $sqlFollowing);
mysqli_stmt_bind_param($stmtFollowing, "i", $followingUserId);
mysqli_stmt_execute($stmtFollowing);
$followingCount = mysqli_stmt_get_result($stmtFollowing)->fetch_assoc()['following'];

$sql = "SELECT followerId FROM followers WHERE follower_userId = ? AND following_userId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $userId, $followingUserId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

echo json_encode([
    "isFollowing" => mysqli_fetch_assoc($res),
    "followersCount" =>$followersCount,
    "followingCount" => $followingCount
]);