<?php
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET["postId"])) {
    echo json_encode(["error" => "ID de receta no proporcionado."]);
    exit();
}

$postId = intval($_GET["postId"]);
//peticion a la BD
$sql = "SELECT comment.content, users.userName, users.userImage 
        FROM comment 
        JOIN users ON comment.userId = users.userId
        WHERE comment.postId = ?
        ORDER BY comment.commentId DESC"; // Ordenar por ID para ver los más recientes primero (o por fecha si se agrega)

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$comments = [];
while ($row = mysqli_fetch_assoc($res)) {
    // Convertir la imagen BLOB a base64
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    $comments[] = $row;
}

mysqli_stmt_close($stmt);

echo json_encode($comments);
?>