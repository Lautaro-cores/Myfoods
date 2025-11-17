<?php
//getUserImage.php
//este archivo obtiene la imagen de perfil del usuario de la sesión activa

session_start();
//se conecta a la base de datos
require_once "includes/config.php";

//si no está autenticado devolvemos la imagen por defecto
if (!isset($_SESSION['userId'])) {
    $defaultImage = __DIR__ . '/img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    if (is_file($defaultImage)) {
        header('Content-Type: image/jpeg');
        readfile($defaultImage);
        exit();
    }
    http_response_code(404);
    exit();
}

//obtiene el userId de la sesión
$userId = intval($_SESSION['userId']);

//hace la consulta para obtener la imagen del usuario
$sql = "SELECT userImage FROM users WHERE userId = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

// userImage guardado como blob binario
if ($user && !empty($user['userImage'])) {
    header('Content-Type: image/jpeg');
    echo $user['userImage'];
    exit();
}
// si no tiene imagen de perfil, devolvemos la imagen por defecto
else {
    $defaultImage = __DIR__ . '/img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    if (is_file($defaultImage)) {
        header('Content-Type: image/jpeg');
        readfile($defaultImage);
        exit();
    }
    http_response_code(404);
    exit();
}
