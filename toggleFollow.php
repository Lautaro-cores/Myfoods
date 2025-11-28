<?php
// toggleFollow.php
// este archivo hace la funcionalidad de seguir o dejar de seguir a otro usuario

session_start();
//se conecta a la base de datos
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

// obtiene el ID del usuario a seguir o dejar de seguir y el ID del usuario de la sesión
$followingUserId = intval($_POST['followingUserId']);
$userId = intval($_SESSION['userId']);

// si el usuario intenta seguirse a sí mismo
if ($followingUserId === $userId) {
    echo json_encode(['success' => false, 'msj' => 'No puedes seguirte a ti mismo.']);
    exit();
}

//1. hace la consulta para verificar si el usuario de la sesión ya sigue al usuario especificado
$sql = "SELECT followerId FROM followers WHERE follower_userId = ? AND following_userId = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'msj' => 'db_prepare_failed', 'error' => mysqli_error($con)]);
    exit();
}
mysqli_stmt_bind_param($stmt, 'ii', $userId, $followingUserId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

// 2. si ya el usuario sigue al otro usuario, entonces lo deja de seguir
if ($row = mysqli_fetch_assoc($res)) {


    mysqli_stmt_close($stmt);
    // hace la consulta para eliminar el seguimiento
    $sqlDel = "DELETE FROM followers WHERE follower_userId = ? AND following_userId = ?";
    $stmtDel = mysqli_prepare($con, $sqlDel);
    //si preparacion de la consulta falla
    if ($stmtDel === false) {
        echo json_encode(['success' => false, 'msj' => 'db_prepare_failed', 'error' => mysqli_error($con)]);
        exit();
    }
    mysqli_stmt_bind_param($stmtDel, 'ii', $userId, $followingUserId);
    if (mysqli_stmt_execute($stmtDel)) {

        // se actualizan los contadores de seguidores y seguidos
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
        
        echo json_encode(['success' => true, 'msj' => 'Seguimiento eliminado', 'followersCount' => $followersCount, 'followingCount' => $followingCount]);
    
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al dejar de seguir al usuario.']);
    }
    mysqli_stmt_close($stmtDel);
    exit();
} // 3. si el usuario no sigue al otro usuario, entonces lo sigue
else {

    mysqli_stmt_close($stmt);
    // hace la consulta para seguir al usuario
    $sqlIns = "INSERT INTO followers (follower_userId, following_userId, created_at) VALUES (?, ?, NOW())";
    $stmtIns = mysqli_prepare($con, $sqlIns);
    //si preparacion de la consulta falla
    if ($stmtIns === false) {
        echo json_encode(['success' => false, 'msj' => 'db_prepare_failed', 'error' => mysqli_error($con)]);
        exit();
    }
    mysqli_stmt_bind_param($stmtIns, 'ii', $userId, $followingUserId);
    if (mysqli_stmt_execute($stmtIns)) {
   
        // se actualizan los contadores de seguidores y seguidos
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

        echo json_encode(['success' => true, 'msj' => 'Seguimiento agregado', 'followersCount' => $followersCount, 'followingCount' => $followingCount]);
    } else {
        echo json_encode(['success' => false, 'msj' => 'Error al seguir al usuario.']);
    }
    mysqli_stmt_close($stmtIns);
    exit();
}
