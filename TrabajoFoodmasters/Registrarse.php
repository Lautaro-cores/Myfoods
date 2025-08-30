<?php
require_once "conexion.php";

if(!empty($_POST["nombre"]) && !empty($_POST["correo"]) && !empty($_POST["contraseña"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["pais"];
    $contraseña = $_POST["contraseña"]
    $sql = "INSERT INTO usuarios(nombre, correo, contraseña) VALUES ('$nombre', '$correo', '$contraseña')";
    if(mysqli_query($con, $sql)) {
        echo json_encode(["msj" => "Todo bien"]);
    } else {
        echo json_encode(["error" => "Fallo la consulta","msj" => "No se pudo registrarse"]);
    }
} else {
    echo json_encode(["error" => "No se recibieron parametros", "msj" => "Completa los espacios"]);
}


