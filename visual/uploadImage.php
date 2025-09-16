<?php
session_start();
require_once "../connection.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'msj' => 'Ocurrió un error inesperado.',
    'imageUrl' => ''
];

if (!isset($_SESSION['userLogged'])) {
    $response['msj'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}

$userName = $_SESSION['userLogged'];

// Verifica si se subió un archivo
if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['userImage']['tmp_name'];
    $fileContent = file_get_contents($fileTmpPath);

    // Actualiza la imagen del usuario en la base de datos
    $sql = "UPDATE users SET userImage = ? WHERE userName = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "bs", $null, $userName);
    mysqli_stmt_send_long_data($stmt, 0, $fileContent);

    if (mysqli_stmt_execute($stmt)) {
        // Obtenemos los datos de la imagen en base64 para enviarlos de vuelta al cliente
        $image_base64 = base64_encode($fileContent);
        $imageUrl = 'data:image/jpeg;base64,' . $image_base64;
        
        $response['success'] = true;
        $response['msj'] = 'Imagen actualizada con éxito.';
        $response['imageUrl'] = $imageUrl;

    } else {
        $response['msj'] = 'Error al actualizar la imagen en la base de datos: ' . mysqli_error($con);
    }
} else {
    $response['msj'] = 'Error al subir la imagen.';
}

echo json_encode($response);
exit();
?>