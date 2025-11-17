<?php
// getFollowersList.php
//este archivo obtiene la lista de seguidores de un usuario por su username

session_start();
//  se conecta a la base de datos
require_once 'includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo json_encode(['success' => false, 'error' => 'missing_username', 'msj' => 'Parámetro username no proporcionado']);
    exit;
}

// obtiene el username del parámetro GET
$username = $_GET['username'];

// hace la consulta para obtener el userId del username
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
// obtiene el userId del perfil
$profileUserId = intval($userRow['userId']);

// hacer la consulta para obtener la lista de seguidores (usuarios que siguen al perfil)
$sqlFollowers = "SELECT u.userId, u.userName, u.userImage, u.description
FROM users u
INNER JOIN followers f ON u.userId = f.follower_userId
WHERE f.following_userId = ?
ORDER BY f.created_at DESC";

if (!$stmt = mysqli_prepare($con, $sqlFollowers)) {
    echo json_encode(['success' => false, 'error' => 'db_prepare_failed', 'msj' => 'Error preparando consulta seguidores']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $profileUserId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

// crea un array para almacenar la lista de seguidores
$followers = [];
// por cada usuario seguido, se agrega la información
while ($row = mysqli_fetch_assoc($res)) {
    $img = null;
    if (!empty($row['userImage'])) {
        // devolver como base64 sin prefijo para que el cliente lo arme si lo desea
        $img = base64_encode($row['userImage']);
    }
    // agrega la información del seguidor al array
    $followers[] = [
        'userId' => intval($row['userId']),
        'userName' => $row['userName'],
        'description' => $row['description'],
        'userImage' => $img
    ];
}

echo json_encode(['success' => true, 'count' => count($followers), 'followers' => $followers]);
exit;
?>
