<?php
// register.php
// este archivo registra un nuevo usuario y inicia sesión automáticamente

session_start();
//se conecta a la base de datos
require_once "includes/config.php";
// verifica si se enviaron los datos del formulario
if (isset($_POST["userName"], $_POST["userPassword"], $_POST["userEmail"])) {
    // obtiene los datos del formulario
    $userName = $_POST["userName"];
    $userPassword = $_POST["userPassword"];
    $userEmail = $_POST["userEmail"];

    // hace la consulta para verificar si el usuario o correo ya existen
    $sql = "SELECT * FROM users WHERE userName = ? OR userEmail = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $userName, $userEmail);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    // si ya existe, devuelve un error
    if (mysqli_num_rows($res) > 0) {
        echo json_encode(["error" => true, "msj" => "El usuario o correo ya existe."]);
        exit();
    }

    // hace la consulta para insertar el nuevo usuario
    $sql = "INSERT INTO users (userName, userPassword, userEmail) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $userName, $userPassword, $userEmail);
    if (mysqli_stmt_execute($stmt)) {
        // inicia la sesión y guarda los datos del usuario en la sesión
        $userId = mysqli_insert_id($con);
        $_SESSION['userName'] = $userName;
        $_SESSION['userId'] = $userId;
        $_SESSION['userType'] = 'user';
        $_SESSION['userImage'] = 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';

        echo json_encode(["success" => true, "msj" => "Registro exitoso."]);
    } else {
        echo json_encode(["error" => true, "msj" => "Error al registrar usuario."]);
    }
} else {
    echo json_encode(["error" => true, "msj" => "Faltan datos."]);
}
