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
    $response['success'] = true;
    $response['msj'] = 'Comentario publicado con éxito.';
} else {
    $response['msj'] = 'Error al publicar el comentario: ' . mysqli_error($con);
}

mysqli_stmt_close($stmt);

echo json_encode($response);
?>