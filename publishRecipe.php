<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION['userId'])) {
    echo json_encode(["success" => false, "msj" => "Debes iniciar sesión para publicar."]);
    exit();
}

if (isset($_POST["title"]) && isset($_POST["description"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $userId = $_SESSION["userId"];

    $sql = "INSERT INTO post (userId, title, description) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $description);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "msj" => "Receta publicada con éxito."]);
    } else {
        echo json_encode(["success" => false, "msj" => "Error al publicar receta."]);
    }
} else {
    echo json_encode(["success" => false, "msj" => "Faltan datos."]);
}
?>