<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

$posts = [];

if (isset($_SESSION['userId'])) {
    $userId = intval($_SESSION['userId']);
    $sql = "SELECT p.postId, p.title, p.description, p.postDate, u.userName, u.userImage,
             (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount,
             (SELECT COUNT(*) FROM likes l2 WHERE l2.postId = p.postId AND l2.userId = ?) AS userLikedCount
         FROM post p
         JOIN users u ON p.userId = u.userId
         ORDER BY p.postDate DESC";

    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        echo json_encode(['error' => 'db_prepare_failed', 'msj' => mysqli_error($con)]);
        exit();
    }
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['userImage'])) {
            $row['userImage'] = base64_encode($row['userImage']);
        }
        // Obtener todas las imágenes de la receta
        $row['images'] = [];
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
        // normalize userLiked as boolean
        $row['likesCount'] = isset($row['likesCount']) ? intval($row['likesCount']) : 0;
        $row['userLiked'] = (isset($row['userLikedCount']) && intval($row['userLikedCount']) > 0) ? true : false;
        unset($row['userLikedCount']);
        unset($row['recipeImage']);
        $posts[] = $row;
    }
    mysqli_stmt_close($stmt);
}

echo json_encode($posts);
