<?php
// Desactivar la salida de errores PHP
error_reporting(0);
ini_set('display_errors', 0);

// Función para manejar errores y convertirlos a JSON
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'success' => false,
        'msj' => 'Error del servidor',
        'debug' => [
            'message' => $errstr,
            'file' => basename($errfile),
            'line' => $errline
        ]
    ];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error);
    exit();
}
set_error_handler('handleError');

// Función para manejar excepciones y convertirlas a JSON
function handleException($e) {
    $error = [
        'success' => false,
        'msj' => 'Error del servidor',
        'debug' => [
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error);
    exit();
}
set_exception_handler('handleException');

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
    $cantidades = isset($_POST['cantidades']) ? $_POST['cantidades'] : [];
    $ingredientIds = isset($_POST['ingredientIds']) ? $_POST['ingredientIds'] : [];
    $pasos = isset($_POST['pasos']) ? $_POST['pasos'] : [];
    
    if (!is_array($ingredientes) || count($ingredientes) == 0 || !is_array($pasos) || count($pasos) == 0) {
        $response['msj'] = 'Debes agregar al menos un ingrediente y un paso.';
        echo json_encode($response);
        exit();
    }
    
    if (count($ingredientes) !== count($cantidades)) {
        $response['msj'] = 'La cantidad de ingredientes y cantidades no coinciden.';
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
        $sqlIng = "INSERT INTO ingredientrecipe (postId, ingredientId, customIngredient, quantity) VALUES (?, ?, ?, ?)";
        $stmtIng = mysqli_prepare($con, $sqlIng);
        if ($stmtIng === false) {
            throw new Exception("Error al preparar la consulta de ingredientes: " . mysqli_error($con));
        }

        foreach ($ingredientes as $index => $ingrediente) {
            $ingredientId = !empty($ingredientIds[$index]) && $ingredientIds[$index] !== 'custom' 
                ? intval($ingredientIds[$index]) 
                : null;
            $customIngredient = $ingredientId === null ? $ingrediente : null;
            $cantidad = $cantidades[$index];
            
            mysqli_stmt_bind_param($stmtIng, "iiss", $postId, $ingredientId, $customIngredient, $cantidad);
            if (!mysqli_stmt_execute($stmtIng)) {
                throw new Exception("Error al guardar el ingrediente: " . mysqli_error($con));
            }
        }
        mysqli_stmt_close($stmtIng);



        // Insertar pasos
        // Primero aseguramos que la tabla stepImages existe para guardar múltiples imágenes por paso
        $createStepImgTbl = "CREATE TABLE IF NOT EXISTS stepImages (
            stepImageId INT AUTO_INCREMENT PRIMARY KEY,
            recipeStepId INT NOT NULL,
            imageData LONGBLOB NOT NULL,
            imageOrder INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        if (!mysqli_query($con, $createStepImgTbl)) {
            throw new Exception('No se pudo crear/verificar la tabla stepImages: ' . mysqli_error($con));
        }

        $sqlPaso = "INSERT INTO recipestep (postId, step) VALUES (?, ?)";
        $sqlInsertStepImg = "INSERT INTO stepImages (recipeStepId, imageData, imageOrder) VALUES (?, ?, ?)";

        foreach ($pasos as $idx => $paso) {
            $paso = trim($paso);
            if ($paso === '') continue;

            $stmtPaso = mysqli_prepare($con, $sqlPaso);
            if ($stmtPaso === false) {
                throw new Exception("Error al preparar la consulta de pasos: " . mysqli_error($con));
            }
            mysqli_stmt_bind_param($stmtPaso, "is", $postId, $paso);
            if (!mysqli_stmt_execute($stmtPaso)) {
                $err = mysqli_error($con);
                mysqli_stmt_close($stmtPaso);
                throw new Exception("Error al insertar paso: " . $err);
            }
            // Obtener el ID del paso insertado
            $recipeStepId = mysqli_insert_id($con);
            mysqli_stmt_close($stmtPaso);

            // Si hay archivos para este paso, vienen como $_FILES['stepImages']['name'][$idx][...]
            if (isset($_FILES['stepImages']['name'][$idx]) && is_array($_FILES['stepImages']['name'][$idx])) {
                $countFiles = count($_FILES['stepImages']['name'][$idx]);
                // Limitar a máximo 3 imágenes por paso
                $maxPerStep = 3;
                for ($j = 0; $j < $countFiles && $j < $maxPerStep; $j++) {
                    if ($_FILES['stepImages']['error'][$idx][$j] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['stepImages']['tmp_name'][$idx][$j];
                        $stepImageContent = file_get_contents($tmpName);

                        $stmtImg = mysqli_prepare($con, $sqlInsertStepImg);
                        if ($stmtImg === false) {
                            throw new Exception('Error al preparar inserción de imagen de paso: ' . mysqli_error($con));
                        }
                        $null = NULL;
                        mysqli_stmt_bind_param($stmtImg, "ibi", $recipeStepId, $null, $j);
                        mysqli_stmt_send_long_data($stmtImg, 1, $stepImageContent);
                        if (!mysqli_stmt_execute($stmtImg)) {
                            $err = mysqli_error($con);
                            mysqli_stmt_close($stmtImg);
                            throw new Exception('Error al guardar imagen de paso: ' . $err);
                        }
                        mysqli_stmt_close($stmtImg);
                    }
                }
            }
        }

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
       $response = ["success" => false, "msj" => $e->getMessage() ];
       echo json_encode($response);
    }
}

echo json_encode($response);
?>