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

        $sql = "SELECT p.postId, p.title, p.description, p.postDate, u.userName, u.userImage,
             (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount,
             (SELECT COUNT(*) FROM likes l2 WHERE l2.postId = p.postId AND l2.userId = ?) AS userLikedCount
         FROM post p
         JOIN users u ON p.userId = u.userId
         LEFT JOIN recipeimages ri ON p.postId = ri.postId
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

        // Después de obtener las recetas básicas
        while ($row = mysqli_fetch_assoc($res)) {
            $postId = $row['postId'];
            $posts[$postId] = [
                'postId' => $postId,
                'title' => $row['title'],
                'description' => $row['description'],
                'postDate' => $row['postDate'],
                'userName' => $row['userName'],
                'images' => [],
                'likesCount' => isset($row['likesCount']) ? intval($row['likesCount']) : 0,
                'userLiked' => (isset($row['userLikedCount']) && intval($row['userLikedCount']) > 0)
            ];

            if (!empty($row['userImage'])) {
                $posts[$postId]['userImage'] = base64_encode($row['userImage']);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Convertir array asociativo a array indexado
$posts = array_values($posts);

echo json_encode($posts);
exit();
?>
