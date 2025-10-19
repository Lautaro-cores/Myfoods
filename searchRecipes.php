<?php
require_once 'includes/config.php';
session_start();


if (!isset($_SESSION['userId'])) {
    header("Location: logIn.php");
    exit();
}

header('Content-Type: application/json');

$posts = [];

$userId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

if (!empty($_GET['contenido'])) {
    $search = trim($_GET['contenido']);
    if ($search !== '') {
        $like = "%" . $search . "%";

        $sql = "SELECT p.postId, p.title, p.description, p.postDate, p.recipeImage, u.userName, u.userImage,
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

        while ($row = mysqli_fetch_assoc($res)) {
            if (!empty($row['userImage'])) {
                $row['userImage'] = base64_encode($row['userImage']);
            }
            $row['images'] = [];
            if (!empty($row['recipeImage'])) {
                $row['images'][] = base64_encode($row['recipeImage']);
            }
            $row['likesCount'] = isset($row['likesCount']) ? intval($row['likesCount']) : 0;
            $row['userLiked'] = (isset($row['userLikedCount']) && intval($row['userLikedCount']) > 0) ? true : false;
            unset($row['userLikedCount']);
            unset($row['recipeImage']);
            $posts[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

echo json_encode($posts);
exit();
?>
