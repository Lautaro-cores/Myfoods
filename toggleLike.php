<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'msj' => 'Debes iniciar sesion para dar like.']);
    exit();
}

if (!isset($_POST['postId'])) {
    echo json_encode(['success' => false, 'msj' => 'postId requerido.']);
    exit();
}

$postId = intval($_POST['postId']);
$userId = intval($_SESSION['userId']);

// Verificar si ya existe
$sql = "SELECT likeId FROM likes WHERE postId = ? AND userId = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $postId, $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($res)) {
    // Ya existe -> eliminar
    $sqlDel = "DELETE FROM likes WHERE likeId = ?";
    $stmtDel = mysqli_prepare($con, $sqlDel);
    mysqli_stmt_bind_param($stmtDel, "i", $row['likeId']);
    if (mysqli_stmt_execute($stmtDel)) {
        echo json_encode(['success' => true, 'action' => 'unliked']);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al quitar like.']);
    }
    exit();
} else {
    // Insertar nuevo like
    $sqlIns = "INSERT INTO likes (postId, userId) VALUES (?, ?)";
    $stmtIns = mysqli_prepare($con, $sqlIns);
    mysqli_stmt_bind_param($stmtIns, "ii", $postId, $userId);
    if (mysqli_stmt_execute($stmtIns)) {
        echo json_encode(['success' => true, 'action' => 'liked']);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al dar like.']);
    }
    exit();
}
