<?php
session_start();
require_once "includes/config.php";

// Mostrar imagen del usuario autenticado (por userId), si existe en DB
if (!isset($_SESSION['userId'])) {
    // Si no está autenticado devolvemos la imagen por defecto
    $defaultImage = __DIR__ . '/img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    if (is_file($defaultImage)) {
        header('Content-Type: image/jpeg');
        readfile($defaultImage);
        exit();
    }
    http_response_code(404);
    exit();
}

$userId = intval($_SESSION['userId']);
$sql = "SELECT userImage FROM users WHERE userId = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

if ($user && !empty($user['userImage'])) {
    // userImage guardado como blob binario
    header('Content-Type: image/jpeg');
    echo $user['userImage'];
    exit();
} else {
        $defaultImage = __DIR__ . '/img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    if (is_file($defaultImage)) {
        header('Content-Type: image/jpeg');
        readfile($defaultImage);
        exit();
    }
    http_response_code(404);
    exit();
}
