<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    header('Location: ../visual/index.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { header('Location: reports.php'); exit(); }

// Delete images linked to comment if table exists
$sqlImgs = "DELETE FROM commentimages WHERE commentId = ?";
if ($st = mysqli_prepare($con, $sqlImgs)) {
    mysqli_stmt_bind_param($st, 'i', $id);
    mysqli_stmt_execute($st);
    mysqli_stmt_close($st);
}

$sql = "DELETE FROM comment WHERE commentId = ?";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: reports.php'); exit();
