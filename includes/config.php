<?php
$server = "localhost";
$user = "root";
$pass = "";
$bd = "myfoods";

$con = mysqli_connect($server, $user, $pass, $bd);
if (!$con) {
    echo json_encode(["error"=>"db_error","msj"=>"Error al conectar a la base de datos"]);
    exit();
}