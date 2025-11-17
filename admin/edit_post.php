<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    header('Location: ../visual/index.php'); 
    exit(); 
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';

    // Aceptar tanto el nuevo formato (arrays) como el antiguo (textarea con saltos de línea)
    if (!empty($_POST['pasos']) && is_array($_POST['pasos'])) {
        $steps = array_map('trim', $_POST['pasos']);
    } else {
        $steps = array_map('trim', explode("\n", $_POST['steps'] ?? ''));
    }

    if (!empty($_POST['ingredientes']) && is_array($_POST['ingredientes'])) {
        $ingredients = array_map('trim', $_POST['ingredientes']);
    } else {
        $ingredients = array_map('trim', explode("\n", $_POST['ingredients'] ?? ''));
    }

    // Filtrar vacíos
    $steps = array_values(array_filter($steps, function($s){ return $s !== ''; }));
    $ingredients = array_values(array_filter($ingredients, function($i){ return $i !== ''; }));

    // Iniciamos una transacción
    mysqli_begin_transaction($con);
    try {
        // Actualizar post
        $sql = "UPDATE post SET title=?, description=? WHERE postId=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $title, $desc, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Manejar la imagen si se subió una nueva
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            $sql = "UPDATE recipeImages SET imageData=? WHERE postId=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'si', $imageData, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

    // Eliminar imágenes de pasos anteriores y pasos anteriores
    $sql = "DELETE si FROM stepimages si JOIN recipestep rs ON si.recipeStepId = rs.recipeStepId WHERE rs.postId = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM recipestep WHERE postId=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

        // Insertar nuevos pasos y sus imágenes (si se subieron)
        if (!empty($steps)) {
            $sql = "INSERT INTO recipestep (postId, step) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            for ($si = 0; $si < count($steps); $si++) {
                $stepText = trim($steps[$si]);
                if ($stepText !== '') {
                    mysqli_stmt_bind_param($stmt, 'is', $id, $stepText);
                    mysqli_stmt_execute($stmt);
                    $recipeStepId = mysqli_insert_id($con);
                    // Manejar imagen del paso (archivo único por paso: stepImages[])
                    if (isset($_FILES['stepImages']) && isset($_FILES['stepImages']['error'][$si]) && $_FILES['stepImages']['error'][$si] === UPLOAD_ERR_OK) {
                        $tmp = $_FILES['stepImages']['tmp_name'][$si];
                        $imgData = file_get_contents($tmp);
                        // Insertar imagen del paso
                        $imgEsc = mysqli_real_escape_string($con, $imgData);
                        $insertImgSql = "INSERT INTO stepimages (recipeStepId, imageData, imageOrder) VALUES ($recipeStepId, '$imgEsc', 0)";
                        mysqli_query($con, $insertImgSql);
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }

        // Eliminar ingredientes anteriores
        $sql = "DELETE FROM ingredientrecipe WHERE postId=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

            // Insertar nuevos ingredientes (estructura nueva)
            if (!empty($_POST['ingredientes']) && is_array($_POST['ingredientes'])) {
                $ingredientes = $_POST['ingredientes'];
                $cantidades = $_POST['cantidades'] ?? [];
                $ids = $_POST['ingredientIds'] ?? [];
                $sql = "INSERT INTO ingredientrecipe (postId, ingredientId, customIngredient, quantity) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                foreach ($ingredientes as $idx => $ing) {
                    $cantidad = isset($cantidades[$idx]) ? $cantidades[$idx] : '';
                    $ingredientId = isset($ids[$idx]) && $ids[$idx] !== 'custom' ? intval($ids[$idx]) : null;
                    $customIngredient = $ingredientId === null ? $ing : null;
                    mysqli_stmt_bind_param($stmt, "iiss", $id, $ingredientId, $customIngredient, $cantidad);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);
            }

        // Commit la transacción
        mysqli_commit($con);
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        // Si hay error, hacer rollback
        mysqli_rollback($con);
        $error = "Error al actualizar la receta: " . $e->getMessage();
    }
}
    

$post = null;
// Obtener datos del post
$sql = "SELECT p.postId, p.title, p.description
        FROM post p 
        WHERE p.postId=? LIMIT 1";
$images = [];
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid, $ptitle, $pdescription);
    if (mysqli_stmt_fetch($stmt)) {
        $post = [
            'postId' => $pid,
            'title' => $ptitle,
            'description' => $pdescription,
        ];
    }
    mysqli_stmt_close($stmt);
}

// Si no existe el post redirigir de inmediato (evita usar $post null más abajo)
if (!$post) { header('Location: index.php'); exit; }


// ahora obtener imágenes
$sqlImg = "SELECT imageData FROM recipeImages WHERE postId = ? ORDER BY imageOrder ASC";
$stmtImg = mysqli_prepare($con, $sqlImg);
if ($stmtImg) {
    mysqli_stmt_bind_param($stmtImg, "i", $id);
    mysqli_stmt_execute($stmtImg);
    mysqli_stmt_bind_result($stmtImg, $imageData);
    while (mysqli_stmt_fetch($stmtImg)) {
        // store raw image data and encode in the view to avoid double-encoding
        $images[] = $imageData;
    }
    mysqli_stmt_close($stmtImg);
}


// Obtener pasos de la receta junto con sus imágenes
$steps = [];
$sql = "SELECT recipeStepId, step FROM recipestep WHERE postId=? ORDER BY recipeStepId";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $recipeStepId = $row['recipeStepId'];
        $stepText = $row['step'];
        // obtener imágenes del paso
        $stepImgs = [];
        $sqlImgs = "SELECT imageData FROM stepimages WHERE recipeStepId = ? ORDER BY imageOrder ASC";
        if ($stmtImgs = mysqli_prepare($con, $sqlImgs)) {
            mysqli_stmt_bind_param($stmtImgs, 'i', $recipeStepId);
            mysqli_stmt_execute($stmtImgs);
            mysqli_stmt_bind_result($stmtImgs, $imgData);
            while (mysqli_stmt_fetch($stmtImgs)) {
                $stepImgs[] = $imgData;
            }
            mysqli_stmt_close($stmtImgs);
        }
        $steps[] = ['recipeStepId' => $recipeStepId, 'step' => $stepText, 'images' => $stepImgs];
    }
    mysqli_stmt_close($stmt);
}
// para compatibilidad con código anterior
$post['steps'] = implode("\n", array_map(function($s){ return $s['step']; }, $steps));

// Obtener ingredientes

$ingredients = [];
$sql = "SELECT ir.ingredientId, ir.customIngredient, ir.quantity, i.name FROM ingredientrecipe ir LEFT JOIN ingredients i ON ir.ingredientId = i.ingredientId WHERE ir.postId=? ORDER BY ir.ingredientRecipeId";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $ingredients[] = [
            'ingredientId' => $row['ingredientId'],
            'name' => $row['name'],
            'customIngredient' => $row['customIngredient'],
            'quantity' => $row['quantity']
        ];
    }
    mysqli_stmt_close($stmt);
}

if (!$post) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Publicación</title>
    <link href="../css/main.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-header">
        <h1>Editar Publicación #<?= htmlspecialchars($post['postId'] ?? 0) ?></h1>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="description" rows="8" cols="60" required><?= htmlspecialchars($post['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">

                <label>Ingredientes:</label>
                <div id="ingredients-list">
                    <?php if (!empty($ingredients)): ?>
                        <?php foreach ($ingredients as $idx => $ing): ?>
                            <div class="input-container">
                                <div class="input-wrapper">
                                    <select name="ingredientIds[]" class="input-ingredient-select">
                                        <option value="custom" <?= empty($ing['ingredientId']) ? 'selected' : '' ?>>Personalizado</option>
                                        <?php
                                        // Obtener lista de ingredientes estructurados
                                        $sqlIngList = "SELECT ingredientId, name FROM ingredients ORDER BY name ASC";
                                        $resultIngList = mysqli_query($con, $sqlIngList);
                                        while ($rowIng = mysqli_fetch_assoc($resultIngList)) {
                                            $selected = ($rowIng['ingredientId'] == $ing['ingredientId']) ? 'selected' : '';
                                            echo "<option value='" . $rowIng['ingredientId'] . "' $selected>" . htmlspecialchars($rowIng['name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="text" name="ingredientes[]" class="input-ingredient input" placeholder="Nombre" value="<?= htmlspecialchars($ing['customIngredient'] ?: $ing['name']) ?>" <?= empty($ing['ingredientId']) ? '' : 'readonly' ?> required>
                                    <input type="text" name="cantidades[]" class="input-quantity input" placeholder="Cantidad" value="<?= htmlspecialchars($ing['quantity']) ?>" required>
                                </div>
                                <div class="button-wrapper">
                                    <button type="button" class="delete-item buttono">&times;</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="input-container">
                            <div class="input-wrapper">
                                <select name="ingredientIds[]" class="input-ingredient-select">
                                    <option value="custom" selected>Personalizado</option>
                                    <?php
                                    $sqlIngList = "SELECT ingredientId, name FROM ingredients ORDER BY name ASC";
                                    $resultIngList = mysqli_query($con, $sqlIngList);
                                    while ($rowIng = mysqli_fetch_assoc($resultIngList)) {
                                        echo "<option value='" . $rowIng['ingredientId'] . "'>" . htmlspecialchars($rowIng['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                                <input type="text" name="ingredientes[]" class="input-ingredient input" placeholder="Nombre" required>
                                <input type="text" name="cantidades[]" class="input-quantity input" placeholder="Cantidad" required>
                            </div>
                            <div class="button-wrapper">
                                <button type="button" class="delete-item buttono">&times;</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" id="addIngrediente" class="buttonw">Agregar ingrediente</button>
            </div>

        <div class="form-group">
            <label>Pasos de la receta:</label>
            <div id="steps-list">
                <?php if (!empty($steps)): ?>
                    <?php foreach ($steps as $idx => $st): ?>
                        <div class="input-container">
                            <div class="input-wrapper">
                                <input type="text" name="pasos[]" class="input-step input" placeholder="Paso <?= ($idx+1) ?>" value="<?= htmlspecialchars($st['step']) ?>" required>
                                <?php if (!empty($st['images'])): ?>
                                    <div class="step-images-preview">
                                        <?php foreach ($st['images'] as $img): ?>
                                            <img src="data:image/jpeg;base64,<?= base64_encode($img) ?>" alt="Imagen paso" style="max-width:150px;display:block;margin-top:6px;">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div style="margin-top:6px;">
                                    <label>Cambiar / agregar imagen del paso</label>
                                    <input type="file" name="stepImages[]" accept="image/*">
                                </div>
                            </div>
                            <div class="button-wrapper">
                                <button type="button" class="delete-item buttono">&times;</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="input-container">
                        <div class="input-wrapper">
                            <input type="text" name="pasos[]" class="input-step input" placeholder="Paso 1" required>
                            <div style="margin-top:6px;">
                                <label>Imagen del paso</label>
                                <input type="file" name="stepImages[]" accept="image/*">
                            </div>
                        </div>
                        <div class="button-wrapper">
                            <button type="button" class="delete-item buttono">&times;</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="addPaso" class="buttonw">Agregar paso</button>
        </div>
        
        <div class="form-group">
            <label>Imagen de la receta</label>
                <div class="current-image">
                    <p>Imagen actual:</p>
                    <?php foreach ($images as $img): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($img) ?>" alt="Imagen actual">
                    <?php endforeach; ?>
                </div>
            <input type="file" name="image" accept="image/*">
            <small>(Deja en blanco para mantener la imagen actual)</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="admin-btn">Guardar cambios</button>
            <a href="index.php" class="admin-btn secondary">Cancelar</a>
        </div>
    </form>
    <?php
    // Exponer lista de ingredientes al JS para poder poblar selects dinámicamente
    $ingredientList = [];
    $sqlIngList = "SELECT ingredientId, name FROM ingredients ORDER BY name ASC";
    $resIngList = mysqli_query($con, $sqlIngList);
    while ($r = mysqli_fetch_assoc($resIngList)) {
        $ingredientList[] = ['id' => $r['ingredientId'], 'name' => $r['name']];
    }
    ?>
    <script>
        window.ingredientOptions = <?= json_encode($ingredientList, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
    </script>
    <script src="../admin/js/edit_post_dynamic.js"></script>
</body>
</html>

