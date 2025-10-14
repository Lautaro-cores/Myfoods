<?php

session_start();
require_once "includes/config.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'msj' => 'Ocurrió un error inesperado.',
    'imageUrl' => ''
];

if (!isset($_SESSION['userId'])) {
    $response['msj'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}

$userId = intval($_SESSION['userId']);

// Verifica si se subió un archivo
if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['userImage']['tmp_name'];
    $fileContent = file_get_contents($fileTmpPath);

    // Actualiza la imagen del usuario en la base de datos
    $sql = "UPDATE users SET userImage = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    $null = NULL;
    mysqli_stmt_bind_param($stmt, "bi", $null, $userId);
    mysqli_stmt_send_long_data($stmt, 0, $fileContent);

    if (mysqli_stmt_execute($stmt)) {
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
