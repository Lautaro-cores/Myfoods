<?php
// toggleCommentLike.php
// este archivo agrega o quita un like a un comentario por el usuario autenticado

session_start();
//se conecta a la base de datos
require_once "includes/config.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'msj' => 'Ocurrió un error inesperado.'
];

// verifica que el usuario ha iniciado sesión
if (!isset($_SESSION['userId'])) {
    $response['msj'] = 'Debes iniciar sesión para dar like.';
    echo json_encode($response);
    exit();
}

// verifica que se proporcionó el ID de comentario
if (!isset($_POST['commentId'])) {
    $response['msj'] = 'ID de comentario no proporcionado.';
    echo json_encode($response);
    exit();
}
// obtiene el ID del comentario y el ID del usuario de la sesión
$commentId = intval($_POST['commentId']);
$userId = $_SESSION['userId'];

//hace la consulta para verificar si ya existe el like
$checkSql = "SELECT likeId FROM commentLikes WHERE userId = ? AND commentId = ?";
$checkStmt = mysqli_prepare($con, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $userId, $commentId);
mysqli_stmt_execute($checkStmt);
$existingLike = mysqli_stmt_get_result($checkStmt)->fetch_assoc();
mysqli_stmt_close($checkStmt);

// 1. si el like ya existe, lo elimina
if ($existingLike) {
    // hace la consulta para eliminar el like
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
} 
// 2. si el like no existe, lo inserta
else {
    // hace la consulta para agregar el like
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

// hacer la consulta para obtener el conteo actual de likes
$countSql = "SELECT COUNT(*) as likeCount FROM commentLikes WHERE commentId = ?";
$countStmt = mysqli_prepare($con, $countSql);
mysqli_stmt_bind_param($countStmt, "i", $commentId);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt)->fetch_assoc();
$response['likeCount'] = $countResult['likeCount'];
mysqli_stmt_close($countStmt);

echo json_encode($response);
?>

