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
$displayName = isset($_POST['displayName']) ? trim($_POST['displayName']) : '';
$userId = intval($_SESSION['userId']);
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// --- Si hay imagen subida ---
if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['userImage']['tmp_name'];
    $fileContent = file_get_contents($fileTmpPath);

    $sql = "UPDATE users SET userImage = ?, description = ?, displayName = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    $null = NULL;
    mysqli_stmt_bind_param($stmt, "bssi", $null, $description, $displayName, $userId);
    mysqli_stmt_send_long_data($stmt, 0, $fileContent);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['msj'] = 'Perfil actualizado con éxito.';
        $response['imageUrl'] = 'data:image/jpeg;base64,' . base64_encode($fileContent);
    } else {
        $response['msj'] = 'Error al actualizar la imagen: ' . mysqli_error($con);
    }

}

else if (!empty($description)) {
    $sql = "UPDATE users SET description = ?, displayName = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssi", $description, $displayName, $userId);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['msj'] = 'Descripción actualizada con éxito.';
    } else {
        $response['msj'] = 'Error al actualizar la descripción: ' . mysqli_error($con);
    }
}
else if (!empty($displayName)) {
    $sql = "UPDATE users SET displayName = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "si", $displayName, $userId);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['msj'] = 'Nombre a mostrar actualizado con éxito.';
    } else {
        $response['msj'] = 'Error al actualizar el nombre a mostrar: ' . mysqli_error($con);
    }
}

else {
    $response['msj'] = 'No se envió ningún cambio.';
}

echo json_encode($response);
exit();