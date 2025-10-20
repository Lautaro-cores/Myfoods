<?php
session_start();
require_once "includes/config.php";

if (isset($_POST["userName"], $_POST["userPassword"], $_POST["userEmail"])) {
    $userName = $_POST["userName"];
    $userPassword = $_POST["userPassword"];
    $userEmail = $_POST["userEmail"];

    // Verifica si el usuario o correo ya existen
    $sql = "SELECT * FROM users WHERE userName = ? OR userEmail = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $userName, $userEmail);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        echo json_encode(["error" => true, "msj" => "El usuario o correo ya existe."]);
        exit();
    }

    // Inserta el nuevo usuario
    $sql = "INSERT INTO users (userName, userPassword, userEmail) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $userName, $userPassword, $userEmail);
    if (mysqli_stmt_execute($stmt)) {
        // Iniciar sesión automáticamente después de registrar
        $userId = mysqli_insert_id($con);
        $_SESSION['userLogged'] = $userName;
        $_SESSION['userId'] = $userId;
        $_SESSION['userType'] = 'user';

        echo json_encode(["success" => true, "msj" => "Registro exitoso."]);
    } else {
        echo json_encode(["error" => true, "msj" => "Error al registrar usuario."]);
    }
} else {
    echo json_encode(["error" => true, "msj" => "Faltan datos."]);
}
