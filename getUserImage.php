<?php
session_start();
require_once "includes/config.php";

// Si el usuario no ha iniciado sesión, no hay imagen que mostrar
if (!isset($_SESSION['userLogged'])) {
    header("Location: visual/logIn.php");
    exit();
}

$userName = $_SESSION['userLogged'];

$sql = "SELECT userImage FROM users WHERE userName = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

if ($user && !empty($user['userImage'])) {
    header("Content-type: image/jpeg");
    echo $user['userImage'];
} else {
    $defaultImage = 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    header('Content-Type: image/jpeg');
    readfile($defaultImage);
}
exit();
?>