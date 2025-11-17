<?php
// publishRecipe.php
// este archivo maneja la publicación de un post a la base de datos

session_start();
// se conecta a la base de datos
require_once "includes/config.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userId'])) {
    echo json_encode(["success" => false, "msj" => "Debes iniciar sesión para publicar."]);
    exit();
}

$response = ["success" => false, "msj" => "Faltan datos."];

// verifica que se hayan enviado título y descripción
if (isset($_POST["title"]) && isset($_POST["description"])) {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $userId = intval($_SESSION["userId"]);

    if ($title === '' || $description === '') {
        $response['msj'] = 'Título y descripción no pueden estar vacíos.';
        echo json_encode($response);
        exit();
    }

    // obtiene los ingredientes y pasos ingresados en el formulario
    $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : [];
    $cantidades = isset($_POST['cantidades']) ? $_POST['cantidades'] : [];
    $ingredientIds = isset($_POST['ingredientIds']) ? $_POST['ingredientIds'] : [];
    $pasos = isset($_POST['pasos']) ? $_POST['pasos'] : [];

    // valida que haya al menos un ingrediente y un paso
    if (!is_array($ingredientes) || count($ingredientes) == 0 || !is_array($pasos) || count($pasos) == 0) {
        $response['msj'] = 'Debes agregar al menos un ingrediente y un paso.';
        echo json_encode($response);
        exit();
    }

    // valida que la cantidad de ingredientes y cantidades coincidan
    if (count($ingredientes) !== count($cantidades)) {
        $response['msj'] = 'La cantidad de ingredientes y cantidades no coinciden.';
        echo json_encode($response);
        exit();
    }

    // valida que haya al menos una imagen
    if (!isset($_FILES['recipeImages']) || empty($_FILES['recipeImages']['name'][0])) {
        $response = ["success" => false, "msj" => "Debes subir al menos una imagen."];
        echo json_encode($response);
        exit();
    }

    // valida que no haya más de 3 imágenes
    if (count($_FILES['recipeImages']['name']) > 3) {
        $response = ["success" => false, "msj" => "Máximo 3 imágenes permitidas."];
        echo json_encode($response);
        exit();
    }

    // comienza la transacción a la base de datos para ejecutar todas las consultas juntas
    mysqli_begin_transaction($con);
    try {
        // 1. hace la consulta para insertar la receta
        $sql = "INSERT INTO post (userId, title, description) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        // si la preparación de la consulta falla
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta: " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $description);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al publicar receta: " . mysqli_error($con));
        }
        $postId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        // 2. por cada imagen, la inserta en la base de datos
        for ($i = 0; $i < count($_FILES['recipeImages']['name']); $i++) {
            if ($_FILES['recipeImages']['error'][$i] === UPLOAD_ERR_OK) {
                // obtiene el binario de la imagen
                $imageContent = file_get_contents($_FILES['recipeImages']['tmp_name'][$i]);
                // hace la consulta para insertar la imagen
                $sql = "INSERT INTO recipeImages (postId, imageData, imageOrder) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                // si la preparación de la consulta falla
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

        // 3. hace la consulta para insertar los ingredientes
        $sqlIng = "INSERT INTO ingredientrecipe (postId, ingredientId, customIngredient, quantity) VALUES (?, ?, ?, ?)";
        $stmtIng = mysqli_prepare($con, $sqlIng);
        // si la preparación de la consulta falla
        if ($stmtIng === false) {
            throw new Exception("Error al preparar la consulta de ingredientes: " . mysqli_error($con));
        }
        // 4. por cada ingrediente, se inserta en la base de datos
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

        // 5. hace la consulta para insertar los pasos y sus imágenes
        $sqlPaso = "INSERT INTO recipestep (postId, step) VALUES (?, ?)";
        $sqlInsertStepImg = "INSERT INTO stepImages (recipeStepId, imageData, imageOrder) VALUES (?, ?, ?)";
        // 6. por cada paso, se inserta en la base de datos
        foreach ($pasos as $idx => $paso) {
            $paso = trim($paso);
            if ($paso === '')
                continue;

            $stmtPaso = mysqli_prepare($con, $sqlPaso);
            // si la preparación de la consulta falla
            if ($stmtPaso === false) {
                throw new Exception("Error al preparar la consulta de pasos: " . mysqli_error($con));
            }
            mysqli_stmt_bind_param($stmtPaso, "is", $postId, $paso);
            if (!mysqli_stmt_execute($stmtPaso)) {
                $err = mysqli_error($con);
                mysqli_stmt_close($stmtPaso);
                throw new Exception("Error al insertar paso: " . $err);
            }
            // se obtiene el ID del paso insertado
            $recipeStepId = mysqli_insert_id($con);
            mysqli_stmt_close($stmtPaso);

            // 7. por cada imagen del paso, la inserta en la base de datos
            if (isset($_FILES['stepImages']['name'][$idx]) && is_array($_FILES['stepImages']['name'][$idx])) {
                $countFiles = count($_FILES['stepImages']['name'][$idx]);
                $maxPerStep = 3;
                for ($j = 0; $j < $countFiles && $j < $maxPerStep; $j++) {
                    if ($_FILES['stepImages']['error'][$idx][$j] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['stepImages']['tmp_name'][$idx][$j];
                        $stepImageContent = file_get_contents($tmpName);
                        $stmtImg = mysqli_prepare($con, $sqlInsertStepImg);
                        // si la preparación de la consulta falla
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

        // 8. inserta las etiquetas asociadas al post si las hay
        if (isset($_POST['tags']) && is_array($_POST['tags']) && count($_POST['tags']) > 0) {
            //hace la consulta para insertar las etiquetas
            $sqlPostTag = "INSERT INTO postTags (postId, tagId) VALUES (?, ?)";
            $stmtPT = mysqli_prepare($con, $sqlPostTag);
            // si la preparación de la consulta falla
            if ($stmtPT === false) {
                throw new Exception("Error al preparar la consulta de postTags: " . mysqli_error($con));
            }
            // por cada etiqueta, se inserta la relación post-etiqueta
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
        }
        // si todo sale bien, se confirma la transacción a la base de datos
        mysqli_commit($con);
        $response = [
            "success" => true,
            "msj" => "Receta publicada exitosamente.",
            "postId" => $postId
        ];
    } catch (Exception $e) {
        $response = ["success" => false, "msj" => $e->getMessage()];
        echo json_encode($response);
    }
}

echo json_encode($response);
?>