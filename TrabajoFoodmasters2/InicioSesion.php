<?php
session_start();
require_once "conexion.php";

if (isset($_POST["nombre"]) && isset($_POST["contrasena"])) {
    $nombre = $_POST["nombre"];
    $contrasena = $_POST["contrasena"];

    $sql = "SELECT idUsuario, nombre, contraseña FROM usuario WHERE nombre = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nombre);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $fila = mysqli_fetch_assoc($res);
        
        if ($contrasena === $fila["contraseña"]) {
            $_SESSION['usuario_logueado'] = $fila['nombre'];
            $_SESSION['id_usuario'] = $fila['idUsuario'];

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