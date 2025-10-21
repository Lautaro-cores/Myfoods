<?php
require_once 'includes/config.php';
session_start();

// Nuevo comportamiento: si se pasan tags, filtrar por posts que contengan todas las tags (AND)
$posts = [];

$userId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

$search = isset($_GET['contenido']) ? trim($_GET['contenido']) : '';
$tagsParam = isset($_GET['tags']) ? trim($_GET['tags']) : '';
$tags = [];
if ($tagsParam !== '') {
    $tags = array_filter(array_map('intval', explode(',', $tagsParam)));
}

if (!empty($tags)) {
    // construir lista segura de ids
    $safeIds = implode(',', array_map('intval', $tags));
    $sql = "SELECT DISTINCT p.postId, p.title, p.description, p.postDate, u.userName, u.userImage,
             (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount,
             (SELECT COUNT(*) FROM likes l2 WHERE l2.postId = p.postId AND l2.userId = ?) AS userLikedCount
         FROM post p
         JOIN users u ON p.userId = u.userId
         JOIN postTags pt ON p.postId = pt.postId
         WHERE pt.tagId IN ($safeIds)";

    if ($search !== '') {
        $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    }

    $sql .= " GROUP BY p.postId HAVING COUNT(DISTINCT pt.tagId) = " . count($tags) . " ORDER BY p.postDate DESC";

    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        echo json_encode(['error' => 'db_prepare_failed', 'msj' => mysqli_error($con)]);
        exit();
    }

    if ($search !== '') {
        $like = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, 'iss', $userId, $like, $like);
    } else {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
    }

    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

} else if ($search !== '') {
    $like = "%" . $search . "%";
    $sql = "SELECT p.postId, p.title, p.description, p.postDate, u.userName, u.userImage,
             (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount,
             (SELECT COUNT(*) FROM likes l2 WHERE l2.postId = p.postId AND l2.userId = ?) AS userLikedCount
         FROM post p
         JOIN users u ON p.userId = u.userId
         WHERE p.title LIKE ? OR p.description LIKE ?
         ORDER BY p.postDate DESC";

    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        echo json_encode(['error' => 'db_prepare_failed', 'msj' => mysqli_error($con)]);
        exit();
    }
    mysqli_stmt_bind_param($stmt, 'iss', $userId, $like, $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

} else {
    // No search text nor tags -> devolver vacío
    echo json_encode([]);
    exit();
}

// Después de obtener las recetas básicas
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

echo json_encode($posts);