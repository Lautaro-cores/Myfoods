<?php
session_start();
require_once "includes/config.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit();
}

$reporterId = intval($_SESSION['userId']);
$targetType = isset($_POST['target_type']) ? trim($_POST['target_type']) : (isset($_POST['targetType']) ? trim($_POST['targetType']) : 'post');
$targetId = 0;
if (isset($_POST['target_id'])) $targetId = intval($_POST['target_id']);
if ($targetId === 0 && isset($_POST['postId'])) $targetId = intval($_POST['postId']);
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

$allowed = ['post', 'user', 'comment'];
if (!in_array($targetType, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'invalid_target_type']);
    exit();
}
if ($targetId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'invalid_target_id']);
    exit();
}

$targetOwnerId = null;
if ($targetType === 'post') {
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
} elseif ($targetType === 'comment') {
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
} elseif ($targetType === 'user') {
    // target is a user, owner = user itself
    $targetOwnerId = $targetId;
}

// Insert report
$insert = "INSERT INTO reports (reporterId, targetType, targetId, targetOwnerId, reason, status) VALUES (?, ?, ?, ?, ?, 'pending')";
$stmt = mysqli_prepare($con, $insert);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'db_prepare_failed', 'msj' => mysqli_error($con)]);
    exit();
}

mysqli_stmt_bind_param($stmt, 'isiss', $reporterId, $targetType, $targetId, $targetOwnerId, $reason);
$executed = mysqli_stmt_execute($stmt);
if ($executed) {
    $reportId = mysqli_insert_id($con);
    echo json_encode(['success' => true, 'reportId' => $reportId]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'db_execute_failed', 'msj' => mysqli_error($con)]);
}

mysqli_stmt_close($stmt);

?>