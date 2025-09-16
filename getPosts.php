<?php

require_once "includes/config.php";

$sql = "SELECT post.title, post.postDate, users.userName 
        FROM post 
        JOIN users ON post.userId = user.userId
        ORDER BY post.postDate DESC";
$res = mysqli_query($con, $sql);

$posts = [];
while ($row = mysqli_fetch_assoc($res)) {
    $posts[] = $row;
}
echo json_encode($posts);
?>