<?php
session_start();
require_once "includes/config.php";


if (isset($_POST["userName"]) && isset($_POST["userPassword"])) {
    $userName = $_POST["userName"];
    $userPassword = $_POST["userPassword"];

    $sql = "SELECT userId, userName, userPassword FROM users WHERE userName = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);

        if ($userPassword === $row["userPassword"]) { // Considera usar password_hash en producción
            $_SESSION['userLogged'] = $row['userName'];
            $_SESSION['userId'] = $row['userId'];

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
?>