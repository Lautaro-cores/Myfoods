<?php
session_start();
require_once "includes/config.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userId'])) {
    echo json_encode(["success" => false, "msj" => "Debes iniciar sesión para publicar."]);
    exit();
}

$response = ["success" => false, "msj" => "Faltan datos."];

if (isset($_POST["title"]) && isset($_POST["description"])) {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $userId = intval($_SESSION["userId"]);

    if ($title === '' || $description === '') {
        $response['msj'] = 'Título y descripción no pueden estar vacíos.';
        echo json_encode($response);
        exit();
    }

    // Validar ingredientes y pasos
    $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : [];
    $pasos = isset($_POST['pasos']) ? $_POST['pasos'] : [];
    if (!is_array($ingredientes) || count($ingredientes) == 0 || !is_array($pasos) || count($pasos) == 0) {
        $response['msj'] = 'Debes agregar al menos un ingrediente y un paso.';
        echo json_encode($response);
        exit();
    }

    

    // Validar que haya al menos una imagen
    if (!isset($_FILES['image']) || !isset($_FILES['image']['tmp_name']) || count($_FILES['image']['tmp_name']) == 0) {
        $response['msj'] = 'Debes subir al menos una imagen para la receta.';
        echo json_encode($response);
        exit();
    }

    // Insertar receta principal (sin recipeImage)
    $sql = "INSERT INTO post (userId, title, description) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        $response['msj'] = 'Error en la preparación de la consulta: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }
    mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $description);
    if (!mysqli_stmt_execute($stmt)) {
        $response['msj'] = 'Error al publicar receta: ' . mysqli_error($con);
        echo json_encode($response);
        mysqli_stmt_close($stmt);
        exit();
    }
    $postId = mysqli_insert_id($con);
    mysqli_stmt_close($stmt);

    // Guardar la primera imagen subida en la columna post.recipeImage
    $firstImageData = null;
    if (isset($_FILES['image'])) {
        if (is_array($_FILES['image']['tmp_name'])) {
            // image[] input
            foreach ($_FILES['image']['tmp_name'] as $tmp) {
                if (is_uploaded_file($tmp)) { $firstImageData = file_get_contents($tmp); break; }
            }
        } else {
            if (is_uploaded_file($_FILES['image']['tmp_name'])) {
                $firstImageData = file_get_contents($_FILES['image']['tmp_name']);
            }
        }
    }

    if ($firstImageData !== null) {
        $sqlUpd = "UPDATE post SET recipeImage = ? WHERE postId = ?";
        $stmtUpd = mysqli_prepare($con, $sqlUpd);
        if ($stmtUpd) {
            // bind as blob: use 'b' in parameter type and send long data
            mysqli_stmt_bind_param($stmtUpd, 'bi', $null, $postId);
            // send blob data for param index 0
            mysqli_stmt_send_long_data($stmtUpd, 0, $firstImageData);
            mysqli_stmt_execute($stmtUpd);
            mysqli_stmt_close($stmtUpd);
        }
    }

    // Insertar ingredientes
    $sqlIng = "INSERT INTO ingredientrecipe (postId, ingredient) VALUES (?, ?)";
    $stmtIng = mysqli_prepare($con, $sqlIng);
    if ($stmtIng === false) {
        $response['msj'] = 'Error al preparar la consulta de ingredientes: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }
    
    foreach ($ingredientes as $ing) {
        $ing = trim($ing);
        if ($ing !== '') {
            mysqli_stmt_bind_param($stmtIng, "is", $postId, $ing);
            if (!mysqli_stmt_execute($stmtIng)) {
                $response['msj'] = 'Error al insertar ingrediente: ' . mysqli_error($con);
                echo json_encode($response);
                mysqli_stmt_close($stmtIng);
                exit();
            }
        }
    }
    mysqli_stmt_close($stmtIng);

    // Insertar pasos
    $sqlPaso = "INSERT INTO recipestep (postId, step) VALUES (?, ?)";
    $stmtPaso = mysqli_prepare($con, $sqlPaso);
    if ($stmtPaso === false) {
        $response['msj'] = 'Error al preparar la consulta de pasos: ' . mysqli_error($con);
        echo json_encode($response);
        exit();
    }

    foreach ($pasos as $paso) {
        $paso = trim($paso);
        if ($paso !== '') {
            mysqli_stmt_bind_param($stmtPaso, "is", $postId, $paso);
            if (!mysqli_stmt_execute($stmtPaso)) {
                $response['msj'] = 'Error al insertar paso: ' . mysqli_error($con);
                echo json_encode($response);
                mysqli_stmt_close($stmtPaso);
                exit();
            }
        }
    }
    mysqli_stmt_close($stmtPaso);

    $response['success'] = true;
    $response['msj'] = 'Receta publicada con éxito.';
    $response['postId'] = $postId;
    echo json_encode($response);
    exit();
}

echo json_encode($response);
?>