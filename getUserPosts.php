<?php
// getUserPosts.php
// este archivo obtiene las publicaciones de un usuario especifico por su nombre de usuario

session_start();
//se conecta a la base de datos
require_once 'includes/config.php';
header('Content-Type: application/json');

// se obtiene el nombre de usuario de la url
$username = isset($_GET['username']) ? trim($_GET['username']) : '';
if ($username === '') {
    echo json_encode([]);
    exit;
}

// se crea un array para almacenar las publicaciones
$posts = [];

$userId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

//hace la consulta para obtener las publicaciones del usuario especificado
$sql = "SELECT p.postId, p.title, p.description, p.postDate, u.userName, u.displayName, u.userImage,
             (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount,
             (SELECT COUNT(*) FROM likes l2 WHERE l2.postId = p.postId AND l2.userId = ?) AS userLikedCount
         FROM post p
         JOIN users u ON p.userId = u.userId
         WHERE u.userName = ?
         ORDER BY p.postDate DESC";

$stmt = mysqli_prepare($con, $sql);

//si preparacion de la consulta falla
if ($stmt === false) {
    echo json_encode(['error' => 'db_prepare_failed', 'msj' => mysqli_error($con)]);
    exit();
}

mysqli_stmt_bind_param($stmt, 'is', $userId, $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

// por cada publicación se obtiene la información y sus imágenes
while ($row = mysqli_fetch_assoc($res)) {
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    // se obtienen las imágenes asociadas a la publicación
    $imageData = '';
    $row['images'] = [];
    //hace la consulta para obtener las imágenes de la publicación
    $sqlImg = "SELECT imageData FROM recipeImages WHERE postId = ? ORDER BY imageOrder ASC";
    $stmtImg = mysqli_prepare($con, $sqlImg);
    if ($stmtImg) {
        mysqli_stmt_bind_param($stmtImg, 'i', $row['postId']);
        mysqli_stmt_execute($stmtImg);
        mysqli_stmt_bind_result($stmtImg, $imageData);
        while (mysqli_stmt_fetch($stmtImg)) {
            $row['images'][] = base64_encode($imageData);
        }
        mysqli_stmt_close($stmtImg);
    }

    // se procesan los conteos y estados de "me gusta"
    $row['likesCount'] = isset($row['likesCount']) ? intval($row['likesCount']) : 0;
    $row['userLiked'] = (isset($row['userLikedCount']) && intval($row['userLikedCount']) > 0) ? true : false;
    unset($row['userLikedCount']);
    unset($row['recipeImage']);
    $posts[] = $row;
}
mysqli_stmt_close($stmt);

echo json_encode($posts);

?>
