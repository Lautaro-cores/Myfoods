<?php
require_once "conexion.php";

if(!empty($_POST["nombre"]) && !empty($_POST["correo"]) && !empty($_POST["contrasena"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];


    $sql_check = "SELECT * FROM usuario WHERE nombre = ? OR correo = ?";
    $stmt_check = mysqli_prepare($con, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ss", $nombre, $correo);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($res_check) > 0) {
        echo json_encode(["error" => "existente", "msj" => "El usuario o correo electrónico ya están registrados."]);
        exit();
    }

  
    $sql_insert = "INSERT INTO usuario (nombre, contraseña, correo) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sss", $nombre, $contrasena, $correo);

    if(mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["msj" => "¡Registro exitoso! Ya puedes iniciar sesión."]);
    } else {
        echo json_encode(["error" => "db_error", "msj" => "Ocurrió un error al registrarse."]);
    }
} else {
    echo json_encode(["error" => "parametros_faltantes", "msj" => "Por favor, completa todos los campos."]);
}
?>