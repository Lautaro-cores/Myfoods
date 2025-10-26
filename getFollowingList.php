<?php
session_start();
require_once 'includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo json_encode(['success' => false, 'error' => 'missing_username', 'msj' => 'Parámetro username no proporcionado']);
    exit;
}

$username = $_GET['username'];

// Buscar el userId del username
$sqlUser = "SELECT userId FROM users WHERE userName = ? LIMIT 1";
if (!$stmtUser = mysqli_prepare($con, $sqlUser)) {
    echo json_encode(['success' => false, 'error' => 'db_prepare_failed', 'msj' => 'Error preparando consulta']);
    exit;
}
mysqli_stmt_bind_param($stmtUser, 's', $username);
mysqli_stmt_execute($stmtUser);
$resUser = mysqli_stmt_get_result($stmtUser);
if (!$userRow = mysqli_fetch_assoc($resUser)) {
    echo json_encode(['success' => false, 'error' => 'user_not_found', 'msj' => 'Usuario no encontrado']);
    exit;
}

$profileUserId = intval($userRow['userId']);

// Obtener lista de usuarios que el perfil sigue (following)
$sqlFollowing = "SELECT u.userId, u.userName, u.userImage, u.description
FROM users u
INNER JOIN followers f ON u.userId = f.following_userId
WHERE f.follower_userId = ?
ORDER BY f.created_at DESC";

if (!$stmt = mysqli_prepare($con, $sqlFollowing)) {
    echo json_encode(['success' => false, 'error' => 'db_prepare_failed', 'msj' => 'Error preparando consulta following']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $profileUserId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$following = [];
while ($row = mysqli_fetch_assoc($res)) {
    $img = null;
    if (!empty($row['userImage'])) {
        $img = base64_encode($row['userImage']);
    }

    $following[] = [
        'userId' => intval($row['userId']),
        'userName' => $row['userName'],
        'description' => $row['description'],
        'userImage' => $img
    ];
}

echo json_encode(['success' => true, 'count' => count($following), 'following' => $following]);
exit;
?>
