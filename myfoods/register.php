<?php
require_once "conection.php";

if(!empty($_POST["name"]) && !empty($_POST["mail"]) && !empty($_POST["password"])) {
    $nombre = $_POST["name"];
    $correo = $_POST["mail"];
    $contrasena = $_POST["password"];


    $sql_check = "SELECT * FROM usuario WHERE nombre = ? OR correo = ?";
    $stmt_check = mysqli_prepare($con, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ss", $name, $mail);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($res_check) > 0) {
        echo json_encode(["error" => "existente", "msj" => "El usuario o correo electrónico ya están registrados."]);
        exit();
    }

  
    $sql_insert = "INSERT INTO usuario (nombre, contraseña, correo) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sss", $name, $password, $mail);

    if(mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["msj" => "¡Registro exitoso! Ya puedes iniciar sesión."]);
    } else {
        echo json_encode(["error" => "db_error", "msj" => "Ocurrió un error al registrarse."]);
    }
} else {
    echo json_encode(["error" => "parametros_faltantes", "msj" => "Por favor, completa todos los campos."]);
}
?>