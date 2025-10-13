<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'msj' => 'Ocurrió un error inesperado.'
];

// 1. Verificar autenticación
if (!isset($_SESSION['userId'])) {
    $response['msj'] = 'Debes iniciar sesión para comentar.';
    echo json_encode($response);
    exit();
}

// 2. Verificar datos
if (!isset($_POST['postId'], $_POST['content'])) {
    $response['msj'] = 'Faltan datos requeridos.';
    echo json_encode($response);
    exit();
}

$postId = intval($_POST['postId']);
$content = trim($_POST['content']);
$userId = $_SESSION['userId'];

// Validaciones
if (empty($content)) {
    $response['msj'] = 'El comentario no puede estar vacío.';
    echo json_encode($response);
    exit();
}
if (strlen($content) > 255) {
    $response['msj'] = 'El comentario es demasiado largo (máx. 255 caracteres).';
    echo json_encode($response);
    exit();
}

// 3. Insertar comentario en la base de datos
$sql = "INSERT INTO comment (userId, postId, content) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($con, $sql);

if ($stmt === false) {
    $response['msj'] = 'Error de preparación de la consulta: ' . mysqli_error($con);
    echo json_encode($response);
    exit();
}

mysqli_stmt_bind_param($stmt, "iis", $userId, $postId, $content);

if (mysqli_stmt_execute($stmt)) {
    $commentId = mysqli_insert_id($con);
    // Obtener el comentario insertado con datos de usuario
    $sql2 = "SELECT c.commentId, c.userId, c.postId, c.content, u.userName, u.userImage
             FROM comment c
             JOIN users u ON c.userId = u.userId
             WHERE c.commentId = ? LIMIT 1";
    $stmt2 = mysqli_prepare($con, $sql2);
    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, 'i', $commentId);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);
        $newComment = mysqli_fetch_assoc($res2);
        if ($newComment) {
            if (!empty($newComment['userImage'])) {
                $newComment['userImage'] = base64_encode($newComment['userImage']);
            }
            $response['success'] = true;
            $response['msj'] = 'Comentario publicado con éxito.';
            $response['comment'] = $newComment;
        } else {
            $response['success'] = true;
            $response['msj'] = 'Comentario publicado (no se pudo recuperar).';
        }
        mysqli_stmt_close($stmt2);
    } else {
        $response['success'] = true;
        $response['msj'] = 'Comentario publicado (recuperación no disponible).';
    }
} else {
    $response['msj'] = 'Error al publicar el comentario: ' . mysqli_error($con);
}

mysqli_stmt_close($stmt);

echo json_encode($response);
