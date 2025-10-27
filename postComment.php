<?php
// Desactivar errores de PHP para evitar HTML en la respuesta JSON
error_reporting(0);
ini_set('display_errors', 0);

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
$parentId = isset($_POST['parentId']) && !empty($_POST['parentId']) ? intval($_POST['parentId']) : null;
$rating = isset($_POST['rating']) && !empty($_POST['rating']) ? intval($_POST['rating']) : null;

// Validar puntuación si se proporciona
if ($rating !== null && ($rating < 1 || $rating > 5)) {
    $response['msj'] = 'La puntuación debe estar entre 1 y 5 estrellas.';
    echo json_encode($response);
    exit();
}

// Solo permitir puntuación en comentarios principales (sin parentId)
if ($rating !== null && $parentId !== null) {
    $response['msj'] = 'Solo se puede puntuar en comentarios principales de la receta.';
    echo json_encode($response);
    exit();
}
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

// Validar imágenes (máximo 3)
$uploadedImages = [];
if (isset($_FILES['commentImages']) && !empty($_FILES['commentImages']['name'][0])) {
    $imageCount = count($_FILES['commentImages']['name']);
    if ($imageCount > 3) {
        $response['msj'] = 'Máximo 3 imágenes por comentario.';
        echo json_encode($response);
        exit();
    }
    
    for ($i = 0; $i < $imageCount; $i++) {
        if ($_FILES['commentImages']['error'][$i] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['commentImages']['type'][$i];
            $fileSize = $_FILES['commentImages']['size'][$i];
            
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes)) {
                $response['msj'] = 'Solo se permiten imágenes (JPG, PNG, GIF, WebP).';
                echo json_encode($response);
                exit();
            }
            
            // Validar tamaño (máximo 5MB por imagen)
            if ($fileSize > 5 * 1024 * 1024) {
                $response['msj'] = 'Cada imagen debe ser menor a 5MB.';
                echo json_encode($response);
                exit();
            }
            
            $uploadedImages[] = [
                'data' => file_get_contents($_FILES['commentImages']['tmp_name'][$i]),
                'type' => $fileType,
                'size' => $fileSize
            ];
        }
    }
}

// 3. Insertar comentario en la base de datos
$sql = "INSERT INTO comment (userId, postId, parentId, content) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $sql);

if ($stmt === false) {
    $response['msj'] = 'Error de preparación de la consulta: ' . mysqli_error($con);
    echo json_encode($response);
    exit();
}

// Manejar el bind_param con NULL correctamente
if ($parentId === null) {
    // Usar una consulta diferente para NULL
    $sql = "INSERT INTO comment (userId, postId, parentId, content) VALUES (?, ?, NULL, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        $response['msj'] = 'Error de preparación de la consulta (NULL): ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }
    mysqli_stmt_bind_param($stmt, "iis", $userId, $postId, $content);
} else {
    mysqli_stmt_bind_param($stmt, "iiis", $userId, $postId, $parentId, $content);
}

if (mysqli_stmt_execute($stmt)) {
    $commentId = mysqli_insert_id($con);
    
    // Insertar imágenes si las hay
    if (!empty($uploadedImages)) {
        $imageSql = "INSERT INTO commentImages (commentId, imageData, imageType, imageSize) VALUES (?, ?, ?, ?)";
        $imageStmt = mysqli_prepare($con, $imageSql);
        
        if ($imageStmt) {
            foreach ($uploadedImages as $image) {
                mysqli_stmt_bind_param($imageStmt, "isbi", $commentId, $image['data'], $image['type'], $image['size']);
                mysqli_stmt_execute($imageStmt);
            }
            mysqli_stmt_close($imageStmt);
        }
    }
    
    // Insertar puntuación si se proporciona (solo para comentarios principales)
    if ($rating !== null && $parentId === null) {
        $ratingSql = "INSERT INTO recipeRatings (userId, postId, rating) VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE rating = ?";
        $ratingStmt = mysqli_prepare($con, $ratingSql);
        
        if ($ratingStmt) {
            mysqli_stmt_bind_param($ratingStmt, "iiii", $userId, $postId, $rating, $rating);
            mysqli_stmt_execute($ratingStmt);
            mysqli_stmt_close($ratingStmt);
        }
    }
    
    // Obtener el comentario insertado con datos de usuario
    $sql2 = "SELECT c.commentId, c.userId, c.postId, c.parentId, c.content, u.userName, u.userImage
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
    $error = mysqli_error($con);
    $response['msj'] = 'Error al publicar el comentario: ' . $error;
}

mysqli_stmt_close($stmt);

echo json_encode($response);
