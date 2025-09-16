<?php
session_start();
require_once "../connection.php";

// Si el usuario no ha iniciado sesión, no hay imagen que mostrar
if (!isset($_SESSION['userLogged'])) {
    header("Location: logIn.html");
    exit();
}

$userName = $_SESSION['userLogged'];

// Obtener la imagen del usuario de la base de datos
$sql = "SELECT userImage FROM users WHERE userName = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

// Si se encontró una imagen, la servimos con la cabecera correcta
if ($user && !empty($user['userImage'])) {
    // Establece el encabezado para que el navegador sepa que es una imagen
    header("Content-type: image/jpeg"); 
    echo $user['userImage'];
} else {
    // Si no hay imagen, servimos la imagen por defecto
    $defaultImage = '../icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    header('Content-Type: image/jpeg');
    readfile($defaultImage);
}
exit();
?>