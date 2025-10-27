<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET["commentId"])) {
    echo json_encode(["error" => "ID de comentario no proporcionado."]);
    exit();
}

$commentId = intval($_GET["commentId"]);

// Obtener imágenes del comentario
$sql = "SELECT imageId, imageData, imageType, imageSize 
        FROM commentImages 
        WHERE commentId = ? 
        ORDER BY imageId ASC";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $commentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$images = [];
while ($row = mysqli_fetch_assoc($result)) {
    $images[] = [
        'imageId' => $row['imageId'],
        'imageData' => base64_encode($row['imageData']),
        'imageType' => $row['imageType'],
        'imageSize' => $row['imageSize']
    ];
}

mysqli_stmt_close($stmt);

echo json_encode($images);
?>

