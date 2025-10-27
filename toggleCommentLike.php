<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'msj' => 'Ocurrió un error inesperado.'
];

// Verificar autenticación
if (!isset($_SESSION['userId'])) {
    $response['msj'] = 'Debes iniciar sesión para dar like.';
    echo json_encode($response);
    exit();
}

// Verificar datos
if (!isset($_POST['commentId'])) {
    $response['msj'] = 'ID de comentario no proporcionado.';
    echo json_encode($response);
    exit();
}

$commentId = intval($_POST['commentId']);
$userId = $_SESSION['userId'];

// Verificar si ya existe el like
$checkSql = "SELECT likeId FROM commentLikes WHERE userId = ? AND commentId = ?";
$checkStmt = mysqli_prepare($con, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $userId, $commentId);
mysqli_stmt_execute($checkStmt);
$existingLike = mysqli_stmt_get_result($checkStmt)->fetch_assoc();
mysqli_stmt_close($checkStmt);

if ($existingLike) {
    // Quitar like
    $deleteSql = "DELETE FROM commentLikes WHERE userId = ? AND commentId = ?";
    $deleteStmt = mysqli_prepare($con, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "ii", $userId, $commentId);
    
    if (mysqli_stmt_execute($deleteStmt)) {
        $response['success'] = true;
        $response['msj'] = 'Like removido.';
        $response['liked'] = false;
    } else {
        $response['msj'] = 'Error al remover like.';
    }
    mysqli_stmt_close($deleteStmt);
} else {
    // Agregar like
    $insertSql = "INSERT INTO commentLikes (userId, commentId) VALUES (?, ?)";
    $insertStmt = mysqli_prepare($con, $insertSql);
    mysqli_stmt_bind_param($insertStmt, "ii", $userId, $commentId);
    
    if (mysqli_stmt_execute($insertStmt)) {
        $response['success'] = true;
        $response['msj'] = 'Like agregado.';
        $response['liked'] = true;
    } else {
        $response['msj'] = 'Error al agregar like.';
    }
    mysqli_stmt_close($insertStmt);
}

// Obtener el conteo actual de likes
$countSql = "SELECT COUNT(*) as likeCount FROM commentLikes WHERE commentId = ?";
$countStmt = mysqli_prepare($con, $countSql);
mysqli_stmt_bind_param($countStmt, "i", $commentId);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt)->fetch_assoc();
$response['likeCount'] = $countResult['likeCount'];
mysqli_stmt_close($countStmt);

echo json_encode($response);
?>

