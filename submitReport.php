<?php
// submitReport.php
// este archivo maneja la funcionalidad de enviar reportes sobre publicaciones, usuarios o comentarios

session_start();
//se conecta a la base de datos
require_once "includes/config.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit();
}
// se obtienen el id del usuario que reporta y los datos del reporte
$reporterId = intval($_SESSION['userId']);
$targetType = isset($_POST['target_type']) ? trim($_POST['target_type']) : (isset($_POST['targetType']) ? trim($_POST['targetType']) : 'post');
$targetId = 0;
if (isset($_POST['target_id'])) $targetId = intval($_POST['target_id']);
if ($targetId === 0 && isset($_POST['postId'])) $targetId = intval($_POST['postId']);
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

// valida que el tipo de objetivo sea válido
$allowed = ['post', 'user', 'comment'];
if (!in_array($targetType, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'invalid_target_type']);
    exit();
}
// valida que el ID del objetivo sea válido
if ($targetId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'invalid_target_id']);
    exit();
}

// se obtiene el ID del creador del objetivo del reporte
$targetOwnerId = null;
// 1. si el objetivo es una publicación
if ($targetType === 'post') {
    // hace la consulta para obtener el ID del propietario de la publicación
    $sql = "SELECT userId FROM post WHERE postId = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $targetId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $ownerId);
        if (mysqli_stmt_fetch($stmt)) {
            $targetOwnerId = intval($ownerId);
        }
        mysqli_stmt_close($stmt);
    }
}
// 2. si el objetivo es un comentario
elseif ($targetType === 'comment') {
    $sql = "SELECT userId FROM comment WHERE commentId = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $targetId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $ownerId);
        if (mysqli_stmt_fetch($stmt)) {
            $targetOwnerId = intval($ownerId);
        }
        mysqli_stmt_close($stmt);
    }
} 
// 3. si el objetivo es un usuario
elseif ($targetType === 'user') {

    $targetOwnerId = $targetId;
}

// hace la consulta para insertar el reporte en la base de datos
$insert = "INSERT INTO reports (reporterId, targetType, targetId, targetOwnerId, reason, status) VALUES (?, ?, ?, ?, ?, 'pending')";
$stmt = mysqli_prepare($con, $insert);
//si falla la preparación de la consulta
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'db_prepare_failed', 'msj' => mysqli_error($con)]);
    exit();
}

mysqli_stmt_bind_param($stmt, 'isiss', $reporterId, $targetType, $targetId, $targetOwnerId, $reason);
$executed = mysqli_stmt_execute($stmt);
// ejecuta la consulta
if ($executed) {
    $reportId = mysqli_insert_id($con);
    echo json_encode(['success' => true, 'reportId' => $reportId]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'db_execute_failed', 'msj' => mysqli_error($con)]);
}

mysqli_stmt_close($stmt);

?>