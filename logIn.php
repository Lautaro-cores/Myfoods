<?php
session_start();
require_once "includes/config.php";

if (isset($_POST["userName"]) && isset($_POST["userPassword"])) {
    $userName = $_POST["userName"];
    $userPassword = $_POST["userPassword"];

    $sql = "SELECT userId, userName, userPassword, userType, userImage FROM users WHERE userName = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);

        if ($userPassword === $row["userPassword"]) {
            $_SESSION['userName'] = $row['userName'];
            $_SESSION['userId'] = $row['userId'];
            $_SESSION['userType'] = $row['userType'] ?? 'user';
            $_SESSION['userImage'] = base64_encode($row['userImage']) ?? 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';

            echo json_encode(["success" => true, "msj" => "Login exitoso."]);
        } else {
            echo json_encode(["error" => "incorrecto", "msj" => "Usuario o contraseña incorrectos."]);
        }
    } else {
        echo json_encode(["error" => "no_encontrado", "msj" => "Usuario o contraseña incorrectos."]);
    }
} else {
    echo json_encode(["error" => "faltan_datos", "msj" => "Por favor, completa todos los campos."]);
}
