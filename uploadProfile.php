<?php
//uploadProfile.php
//este archivo actualiza la imagen de perfil, la descripción y el nombre en la base de datos cuando el usuario modifica su perfil 


session_start();
//se conecta a la base de datos
require_once "includes/config.php";
header('Content-Type: application/json');


if (!isset($_SESSION['userId'])) {
    $response['msj'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}

//se obtiene los datos del formulario enviado desde el js
$displayName = isset($_POST['displayName']) ? trim($_POST['displayName']) : '';
$userId = intval($_SESSION['userId']);
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// 1. si se subio una nueva imagen de perfil se actualiza junto con la descripcion y el nombre
if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
    //obtiene la direccion temporal del archivo subido
    $fileTmpPath = $_FILES['userImage']['tmp_name'];
    //lee el binario de la direccion temporal
    $fileContent = file_get_contents($fileTmpPath);
    //hace la consulta para actualizar la imagen, descripcion y nombre en la base de datos
    $sql = "UPDATE users SET userImage = ?, description = ?, displayName = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);

    //si la consulta falla manda error
    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    $null = NULL;
    mysqli_stmt_bind_param($stmt, "bssi", $null, $description, $displayName, $userId);
    mysqli_stmt_send_long_data($stmt, 0, $fileContent);
    // ejecuta la consulta
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['msj'] = 'Perfil actualizado con éxito.';
    } else {
        $response['msj'] = 'Error al actualizar la imagen: ' . mysqli_error($con);
    }

} 
// 2. si no se subio una nueva imagen de perfil, solo se actualiza la descripcion y el nombre
else if (!empty($description)) {
    //hace la consulta para actualizar la descripcion y el nombre en la base de datos
    $sql = "UPDATE users SET description = ?, displayName = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);

    //si la preparacion de la consulta falla
    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssi", $description, $displayName, $userId);
// ejecuta la consulta
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['msj'] = 'Descripción actualizada con éxito.';
    } else {
        $response['msj'] = 'Error al actualizar la descripción: ' . mysqli_error($con);
    }
} 
// 3. si no se subio una nueva imagen de perfil ni de descripcion, solo se actualiza el nombre
else if (!empty($displayName)) {
    //hace la consulta para actualizar el nombre en la base de datos
    $sql = "UPDATE users SET displayName = ? WHERE userId = ?";
    $stmt = mysqli_prepare($con, $sql);

    //si la preparacion de la consulta falla
    if ($stmt === false) {
        $response['msj'] = 'Error al preparar la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "si", $displayName, $userId);

    // ejecuta la consulta
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['msj'] = 'Nombre a mostrar actualizado con éxito.';
    } else {
        $response['msj'] = 'Error al actualizar el nombre a mostrar: ' . mysqli_error($con);
    }
} else {
    $response['msj'] = 'No se envió ningún cambio.';
}

echo json_encode($response);
exit();