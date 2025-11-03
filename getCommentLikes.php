<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET["commentId"])) {
    echo json_encode(["error" => "ID de comentario no proporcionado."]);
    exit();
}

$commentId = intval($_GET["commentId"]);

// Obtener conteo de likes
$sql = "SELECT COUNT(*) as likeCount FROM commentLikes WHERE commentId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $commentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

// Verificar si el usuario actual ha dado like (si está logueado)
$isLiked = false;
if (isset($_SESSION['userId'])) {
    $checkSql = "SELECT likeId FROM commentLikes WHERE userId = ? AND commentId = ?";
    $checkStmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ii", $_SESSION['userId'], $commentId);
    mysqli_stmt_execute($checkStmt);
    $existingLike = mysqli_stmt_get_result($checkStmt)->fetch_assoc();
    $isLiked = !empty($existingLike);
    mysqli_stmt_close($checkStmt);
}

echo json_encode([
    'likeCount' => $result['likeCount'],
    'isLiked' => $isLiked
]);
?>

