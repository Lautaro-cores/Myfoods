<?php
session_start();
require_once "conection.php";

if (isset($_POST["name"]) && isset($_POST["password"])) {
    $nombre = $_POST["name"];
    $contrasena = $_POST["password"];

    $sql = "SELECT userid, nombre, contraseña FROM usuario WHERE nombre = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nombre);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $fila = mysqli_fetch_assoc($res);
        
        if ($contraseña === $fila["password"]) {
            $_SESSION['userLogged'] = $fila['name'];
            $_SESSION['idUser'] = $fila['idUser'];

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