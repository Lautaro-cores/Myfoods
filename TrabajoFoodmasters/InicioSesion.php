<?php
require_once "conexion.php";

$sql = "SELECT * FROM usuarios WHERE pais='Argentina'";
$res = mysqli_query($con, $sql);
$arr = [];
while ($fila = mysqli_fetch_assoc($res)) {
    $arr[] = $fila["nombre"];
}