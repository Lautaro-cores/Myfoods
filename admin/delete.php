<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$type || !$id) { header('Location: index.php'); exit; }

if ($type === 'user') {
    // Check if the user is an admin
    $sql = "SELECT userType FROM users WHERE userId=?";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userType);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if ($userType === 'admin') {
            header('Location: index.php?error=No se pueden eliminar usuarios administradores');
            exit;
        }
    }

    $sql = "DELETE FROM comment WHERE userId=?";
    if ($stmt = mysqli_prepare($con, $sql)) { mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt); }


    $sql = "DELETE FROM likes WHERE userId=?";
    if ($stmt = mysqli_prepare($con, $sql)) { mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt); }


    $sql = "SELECT postId FROM post WHERE userId=?";
    $posts = [];
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $postId);
        while (mysqli_stmt_fetch($stmt)) $posts[] = $postId;
        mysqli_stmt_close($stmt);
    }

    foreach ($posts as $pid) {
        $sqls = [
            "DELETE FROM comment WHERE postId=?",
            "DELETE FROM ingredientrecipe WHERE postId=?",
            "DELETE FROM recipestep WHERE postId=?",
            "DELETE FROM likes WHERE postId=?",
            "DELETE FROM post WHERE postId=?",
        ];
        foreach ($sqls as $s) {
            if ($st = mysqli_prepare($con, $s)) { mysqli_stmt_bind_param($st, 'i', $pid); mysqli_stmt_execute($st); mysqli_stmt_close($st); }
        }
    }

    $sql = "DELETE FROM users WHERE userId=?";
    if ($stmt = mysqli_prepare($con, $sql)) { mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt); }
}
elseif ($type === 'post') {
    $pid = $id;
    $sqls = [
        "DELETE FROM comment WHERE postId=?",
        "DELETE FROM ingredientrecipe WHERE postId=?",
        "DELETE FROM recipestep WHERE postId=?",
        "DELETE FROM likes WHERE postId=?",
        "DELETE FROM post WHERE postId=?",
        "DELETE FROM recipeImages WHERE postId=?",
        "DELETE FROM stepImages WHERE postId=?",
        "DELETE FROM posttags WHERE postId=?"
    ];
    foreach ($sqls as $s) {
        if ($st = mysqli_prepare($con, $s)) { mysqli_stmt_bind_param($st, 'i', $pid); mysqli_stmt_execute($st); mysqli_stmt_close($st); }
    }
}

header('Location: index.php'); exit;
