<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET["postId"])) {
    echo json_encode(["error" => "ID de receta no proporcionado."]);
    exit();
}

$postId = intval($_GET["postId"]);

// Obtener solo comentarios principales (sin comentarios hijos) para la receta
$sql = "SELECT c.commentId, c.userId, c.postId, c.content, c.parentId, c.rating, u.userName, u.displayName, u.userImage,
           COALESCE(cl.likeCount, 0) as likeCount,
           CASE WHEN cl_user.likeId IS NOT NULL THEN 1 ELSE 0 END as userLiked,
           COALESCE(child_count.childCount, 0) as childCount
    FROM comment c
        JOIN users u ON c.userId = u.userId
        LEFT JOIN (
            SELECT commentId, COUNT(*) as likeCount 
            FROM commentLikes 
            GROUP BY commentId
        ) cl ON c.commentId = cl.commentId
        LEFT JOIN commentLikes cl_user ON c.commentId = cl_user.commentId AND cl_user.userId = ?
        LEFT JOIN (
            SELECT parentId, COUNT(*) as childCount
            FROM comment
            WHERE parentId IS NOT NULL
            GROUP BY parentId
        ) child_count ON c.commentId = child_count.parentId
        WHERE c.postId = ? AND c.parentId IS NULL
        ORDER BY c.commentId DESC";

$stmt = mysqli_prepare($con, $sql);
$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
mysqli_stmt_bind_param($stmt, "ii", $userId, $postId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$allComments = [];
while ($row = mysqli_fetch_assoc($res)) {
    // Convertir la imagen BLOB a base64
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    
    // Obtener imágenes del comentario
    $imageSql = "SELECT imageId, imageData, imageType, imageSize 
                 FROM commentImages 
                 WHERE commentId = ? 
                 ORDER BY imageId ASC";
    $imageStmt = mysqli_prepare($con, $imageSql);
    
    if ($imageStmt) {
        mysqli_stmt_bind_param($imageStmt, "i", $row['commentId']);
        mysqli_stmt_execute($imageStmt);
        $imageResult = mysqli_stmt_get_result($imageStmt);
        
        $images = [];
        while ($imageRow = mysqli_fetch_assoc($imageResult)) {
            $images[] = [
                'imageId' => $imageRow['imageId'],
                'imageData' => base64_encode($imageRow['imageData']),
                'imageType' => $imageRow['imageType'],
                'imageSize' => $imageRow['imageSize']
            ];
        }
        mysqli_stmt_close($imageStmt);
    } else {
        $images = [];
    }
    
    $row['images'] = $images;
    
    $allComments[] = $row;
}

$commentsArray = $allComments;

mysqli_stmt_close($stmt);

echo json_encode($commentsArray);
