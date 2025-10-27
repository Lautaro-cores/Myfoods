<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET["commentId"])) {
    echo json_encode(["error" => "ID de comentario no proporcionado."]);
    exit();
}

$commentId = intval($_GET["commentId"]);

// Obtener todas las respuestas de este comentario específico (anidadas)
$sql = "SELECT c.commentId, c.userId, c.postId, c.content, c.parentId,
               u.userName, u.displayName, u.userImage,
               COALESCE(cl.likeCount, 0) as likeCount,
               CASE WHEN cl_user.likeId IS NOT NULL THEN 1 ELSE 0 END as isLiked
        FROM comment c
        JOIN users u ON c.userId = u.userId
        LEFT JOIN (
            SELECT commentId, COUNT(*) as likeCount 
            FROM commentLikes 
            GROUP BY commentId
        ) cl ON c.commentId = cl.commentId
        LEFT JOIN commentLikes cl_user ON c.commentId = cl_user.commentId AND cl_user.userId = ?
        WHERE c.parentId = ?
        ORDER BY c.commentId ASC";

$stmt = mysqli_prepare($con, $sql);
$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
mysqli_stmt_bind_param($stmt, "ii", $userId, $commentId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$allReplies = [];
while ($row = mysqli_fetch_assoc($res)) {
    // Convertir la imagen BLOB a base64
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    $row['replies'] = []; // Inicializar array de respuestas
    $allReplies[] = $row;
}

// Crear estructura anidada
$repliesTree = [];
$indexedReplies = [];

// Indexar respuestas por ID
foreach ($allReplies as $reply) {
    $indexedReplies[$reply['commentId']] = $reply;
}

// Construir árbol de respuestas
foreach ($indexedReplies as $id => &$reply) {
    if ($reply['parentId'] == $commentId) {
        // Respuesta directa al comentario principal
        $repliesTree[] = &$reply;
    } else {
        // Respuesta a otra respuesta - agregar al padre
        $parentId = $reply['parentId'];
        if (isset($indexedReplies[$parentId])) {
            $indexedReplies[$parentId]['replies'][] = &$reply;
        }
    }
}

echo json_encode($repliesTree);
?>