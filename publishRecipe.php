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
    if (!isset($_FILES['recipeImages']) || empty($_FILES['recipeImages']['name'][0])) {
        $response = ["success" => false, "msj" => "Debes subir al menos una imagen."];
        echo json_encode($response);
        exit();
    }

    // Validar que no haya más de 3 imágenes
    if (count($_FILES['recipeImages']['name']) > 3) {
        $response = ["success" => false, "msj" => "Máximo 3 imágenes permitidas."];
        echo json_encode($response);
        exit();
    }

    // Iniciar transacción
    mysqli_begin_transaction($con);
    try {
        // Insertar la receta
        $sql = "INSERT INTO post (userId, title, description) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta: " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $description);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al publicar receta: " . mysqli_error($con));
        }
        $postId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        // Procesar cada imagen
        for ($i = 0; $i < count($_FILES['recipeImages']['name']); $i++) {
            if ($_FILES['recipeImages']['error'][$i] === UPLOAD_ERR_OK) {
                $imageContent = file_get_contents($_FILES['recipeImages']['tmp_name'][$i]);
                
                $sql = "INSERT INTO recipeImages (postId, imageData, imageOrder) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                if ($stmt === false) {
                    throw new Exception("Error al preparar la consulta de imágenes: " . mysqli_error($con));
                }
                $null = NULL;
                mysqli_stmt_bind_param($stmt, "ibi", $postId, $null, $i);
                mysqli_stmt_send_long_data($stmt, 1, $imageContent);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error al guardar la imagen " . ($i + 1));
                }
                mysqli_stmt_close($stmt);
            }
        }

        // Insertar ingredientes
        $sqlIng = "INSERT INTO ingredientrecipe (postId, ingredient) VALUES (?, ?)";
        $stmtIng = mysqli_prepare($con, $sqlIng);
        if ($stmtIng === false) {
            throw new Exception("Error al preparar la consulta de ingredientes: " . mysqli_error($con));
        }

        foreach ($ingredientes as $ing) {
            $ing = trim($ing);
            if ($ing !== '') {
                mysqli_stmt_bind_param($stmtIng, "is", $postId, $ing);
                if (!mysqli_stmt_execute($stmtIng)) {
                    throw new Exception("Error al insertar ingrediente: " . mysqli_error($con));
                }
            }
        }
        mysqli_stmt_close($stmtIng);

        // Insertar pasos
        $sqlPaso = "INSERT INTO recipestep (postId, step) VALUES (?, ?)";
        $stmtPaso = mysqli_prepare($con, $sqlPaso);
        if ($stmtPaso === false) {
            throw new Exception("Error al preparar la consulta de pasos: " . mysqli_error($con));
        }

        foreach ($pasos as $paso) {
            $paso = trim($paso);
            if ($paso !== '') {
                mysqli_stmt_bind_param($stmtPaso, "is", $postId, $paso);
                if (!mysqli_stmt_execute($stmtPaso)) {
                    throw new Exception("Error al insertar paso: " . mysqli_error($con));
                }
            }
        }
        mysqli_stmt_close($stmtPaso);

        // Insertar relaciones post-tags si se enviaron
        if (isset($_POST['tags']) && is_array($_POST['tags']) && count($_POST['tags']) > 0) {
            // Usar INSERT IGNORE para evitar errores si ya existe la relación (si se creó UNIQUE index)
            $sqlPostTag = "INSERT IGNORE INTO postTags (postId, tagId) VALUES (?, ?)";
            $stmtPT = mysqli_prepare($con, $sqlPostTag);
            if ($stmtPT === false) {
                // Si el servidor MySQL no permite INSERT IGNORE en prepared statements por alguna razón,
                // caemos a la inserción segura por comprobación previa.
                foreach ($_POST['tags'] as $tagId) {
                    $tagId = intval($tagId);
                    if ($tagId > 0) {
                        $checkSql = "SELECT 1 FROM postTags WHERE postId = ? AND tagId = ? LIMIT 1";
                        $chk = mysqli_prepare($con, $checkSql);
                        mysqli_stmt_bind_param($chk, "ii", $postId, $tagId);
                        mysqli_stmt_execute($chk);
                        mysqli_stmt_store_result($chk);
                        if (mysqli_stmt_num_rows($chk) === 0) {
                            $ins = mysqli_prepare($con, "INSERT INTO postTags (postId, tagId) VALUES (?, ?)");
                            mysqli_stmt_bind_param($ins, "ii", $postId, $tagId);
                            if (!mysqli_stmt_execute($ins)) {
                                throw new Exception("Error al insertar post-tag: " . mysqli_error($con));
                            }
                            mysqli_stmt_close($ins);
                        }
                        mysqli_stmt_close($chk);
                    }
                }
            } else {
                foreach ($_POST['tags'] as $tagId) {
                    $tagId = intval($tagId);
                    if ($tagId > 0) {
                        mysqli_stmt_bind_param($stmtPT, "ii", $postId, $tagId);
                        if (!mysqli_stmt_execute($stmtPT)) {
                            throw new Exception("Error al insertar post-tag: " . mysqli_error($con));
                        }
                    }
                }
                mysqli_stmt_close($stmtPT);
            }
        }

        mysqli_commit($con);
        $response = [
            "success" => true,
            "msj" => "Receta publicada exitosamente.",
            "postId" => $postId
        ];
    } catch (Exception $e) {
        mysqli_rollback($con);
        $response = ["success" => false, "msj" => $e->getMessage()];
    }
}

echo json_encode($response);
?>