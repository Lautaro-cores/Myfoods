<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'msj' => 'Debes iniciar sesión para esta acción.']);
    exit();
}

if (!isset($_POST['followingUserId'])) {
    echo json_encode(['success' => false, 'msj' => 'followingUserId requerido.']);
    exit();
}

$followingUserId = intval($_POST['followingUserId']);
$userId = intval($_SESSION['userId']);

if ($followingUserId === $userId) {
    echo json_encode(['success' => false, 'msj' => 'No puedes seguirte a ti mismo.']);
    exit();
}

// La tabla en la base de datos se llama `followers` (ver bd/myfoods.sql)
$sql = "SELECT followerId FROM followers WHERE follower_userId = ? AND following_userId = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'msj' => 'db_prepare_failed', 'error' => mysqli_error($con)]);
    exit();
}
mysqli_stmt_bind_param($stmt, 'ii', $userId, $followingUserId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($res)) {
    // Ya sigue -> eliminar
    mysqli_stmt_close($stmt);
    $sqlDel = "DELETE FROM followers WHERE follower_userId = ? AND following_userId = ?";
    $stmtDel = mysqli_prepare($con, $sqlDel);
    if ($stmtDel === false) {
        echo json_encode(['success' => false, 'msj' => 'db_prepare_failed', 'error' => mysqli_error($con)]);
        exit();
    }
    mysqli_stmt_bind_param($stmtDel, 'ii', $userId, $followingUserId);
    if (mysqli_stmt_execute($stmtDel)) {
        // Obtener contadores actualizados
        $sqlFollowers = "SELECT COUNT(*) as followers FROM followers WHERE following_userId = ?";
        $stmtFollowers = mysqli_prepare($con, $sqlFollowers);
        mysqli_stmt_bind_param($stmtFollowers, "i", $followingUserId);
        mysqli_stmt_execute($stmtFollowers);
        $followersCount = mysqli_stmt_get_result($stmtFollowers)->fetch_assoc()['followers'];

        $sqlFollowing = "SELECT COUNT(*) as following FROM followers WHERE follower_userId = ?";
        $stmtFollowing = mysqli_prepare($con, $sqlFollowing);
        mysqli_stmt_bind_param($stmtFollowing, "i", $followingUserId);
        mysqli_stmt_execute($stmtFollowing);
        $followingCount = mysqli_stmt_get_result($stmtFollowing)->fetch_assoc()['following'];

        echo json_encode([
            'success' => true, 
            'action' => 'unfollow',
            'msj' => 'Dejaste de seguir al usuario.',
            'followers' => intval($followersCount),
            'following' => intval($followingCount)
        ]);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al dejar de seguir al usuario.']);
    }
    mysqli_stmt_close($stmtDel);
    exit();
} else {
    // No sigue -> insertar
    mysqli_stmt_close($stmt);
    $sqlIns = "INSERT INTO followers (follower_userId, following_userId, created_at) VALUES (?, ?, NOW())";
    $stmtIns = mysqli_prepare($con, $sqlIns);
    if ($stmtIns === false) {
        echo json_encode(['success' => false, 'msj' => 'db_prepare_failed', 'error' => mysqli_error($con)]);
        exit();
    }
    mysqli_stmt_bind_param($stmtIns, 'ii', $userId, $followingUserId);
    if (mysqli_stmt_execute($stmtIns)) {
        // Obtener contadores actualizados
        $sqlFollowers = "SELECT COUNT(*) as followers FROM followers WHERE following_userId = ?";
        $stmtFollowers = mysqli_prepare($con, $sqlFollowers);
        mysqli_stmt_bind_param($stmtFollowers, "i", $followingUserId);
        mysqli_stmt_execute($stmtFollowers);
        $followersCount = mysqli_stmt_get_result($stmtFollowers)->fetch_assoc()['followers'];

        $sqlFollowing = "SELECT COUNT(*) as following FROM followers WHERE follower_userId = ?";
        $stmtFollowing = mysqli_prepare($con, $sqlFollowing);
        mysqli_stmt_bind_param($stmtFollowing, "i", $followingUserId);
        mysqli_stmt_execute($stmtFollowing);
        $followingCount = mysqli_stmt_get_result($stmtFollowing)->fetch_assoc()['following'];

        echo json_encode([
            'success' => true,
            'action' => 'follow',
            'msj' => 'Ahora sigues al usuario.',
            'followers' => intval($followersCount),
            'following' => intval($followingCount)
        ]);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al seguir al usuario.']);
    }
    mysqli_stmt_close($stmtIns);
    exit();
}
