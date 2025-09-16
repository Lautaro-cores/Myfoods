<?php

require_once "includes/config.php";

$sql = "SELECT post.postId, post.title, post.description, post.postDate, users.userName, users.userImage 
        FROM post 
        JOIN users ON post.userId = users.userId
        ORDER BY post.postDate DESC";
$res = mysqli_query($con, $sql);

$posts = [];
while ($row = mysqli_fetch_assoc($res)) {
    // Convertir la imagen BLOB a base64
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    $posts[] = $row;
}
echo json_encode($posts);
?>