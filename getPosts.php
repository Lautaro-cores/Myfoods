<?php

require_once "../connection.php";

$sql = "SELECT post.title, post.postDate, user.userName 
        FROM post 
        JOIN user ON post.userId = user.userId
        ORDER BY post.postDate DESC";
$res = mysqli_query($con, $sql);

$posts = [];
while ($row = mysqli_fetch_assoc($res)) {
    $posts[] = $row;
}
echo json_encode($posts);
?>