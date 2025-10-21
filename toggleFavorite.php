<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'msj' => 'Debes iniciar sesion para guardar recetas.']);
    exit();
}

if (!isset($_POST['postId'])) {
    echo json_encode(['success' => false, 'msj' => 'postId requerido.']);
    exit();
}

$postId = intval($_POST['postId']);
$userId = intval($_SESSION['userId']);

// Verificar si ya existe
$sql = "SELECT favoriteId FROM favorites WHERE postId = ? AND userId = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $postId, $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($res)) {
    // Ya existe -> eliminar
    $sqlDel = "DELETE FROM favorites WHERE favoriteId = ?";
    $stmtDel = mysqli_prepare($con, $sqlDel);
    mysqli_stmt_bind_param($stmtDel, "i", $row['favoriteId']);
    if (mysqli_stmt_execute($stmtDel)) {
        echo json_encode(['success' => true, 'action' => 'removed']);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al quitar de favoritos.']);
    }
    exit();
} else {
    // Insertar nuevo
    $sqlIns = "INSERT INTO favorites (postId, userId) VALUES (?, ?)";
    $stmtIns = mysqli_prepare($con, $sqlIns);
    mysqli_stmt_bind_param($stmtIns, "ii", $postId, $userId);
    if (mysqli_stmt_execute($stmtIns)) {
        echo json_encode(['success' => true, 'action' => 'added']);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al guardar en favoritos.']);
    }
    exit();
}
