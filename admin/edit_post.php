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
    $steps = explode("\n", $_POST['steps'] ?? '');
    $ingredients = explode("\n", $_POST['ingredients'] ?? '');
    
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
            $sql = "UPDATE post SET recipeImage=? WHERE postId=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'si', $imageData, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Eliminar pasos anteriores
        $sql = "DELETE FROM recipestep WHERE postId=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Insertar nuevos pasos
        $sql = "INSERT INTO recipestep (postId, step) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        foreach ($steps as $step) {
            $stepText = trim($step);
            if ($stepText !== '') {
                mysqli_stmt_bind_param($stmt, 'is', $id, $stepText);
                mysqli_stmt_execute($stmt);
            }
        }
        mysqli_stmt_close($stmt);

        // Eliminar ingredientes anteriores
        $sql = "DELETE FROM ingredientrecipe WHERE postId=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        // Insertar nuevos ingredientes
        $sql = "INSERT INTO ingredientrecipe (postId, ingredient) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        foreach ($ingredients as $ingredient) {
            $ingText = trim($ingredient);
            if ($ingText !== '') {
                mysqli_stmt_bind_param($stmt, 'is', $id, $ingText);
                mysqli_stmt_execute($stmt);
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt);

        // Commit la transacción
        mysqli_commit($con);
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        // Si hay error, hacer rollback
        mysqli_rollback($con);
        $error = "Error al actualizar la receta: " . $e->getMessage();
    }
$post = null;
// Obtener datos del post
$sql = "SELECT p.postId, p.title, p.description, r.imageData
        FROM post p JOIN recipeImages r ON p.postId = r.postId
        WHERE p.postId=? LIMIT 1";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid, $ptitle, $pdescription, $pimage);
    if (mysqli_stmt_fetch($stmt)) {
        $post = [
            'postId' => $pid,
            'title' => $ptitle,
            'description' => $pdescription,
            'imageData' => $pimage
        ];
    }
    mysqli_stmt_close($stmt);
}

$images = [];
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
        $images[] = base64_encode($imageData);
    }
    mysqli_stmt_close($stmtImg);
}

// Obtener pasos de la receta
$steps = [];
$sql = "SELECT step FROM recipestep WHERE postId=? ORDER BY recipeStepId";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $step);
    while (mysqli_stmt_fetch($stmt)) {
        $steps[] = $step;
    }
    mysqli_stmt_close($stmt);
}
$post['steps'] = implode("\n", $steps);

// Obtener ingredientes
$ingredients = [];
$sql = "SELECT ingredient FROM ingredientrecipe WHERE postId=? ORDER BY ingredientId";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ingredient);
$post['ingredients'] = implode("\n", $ingredients);
    mysqli_stmt_close($stmt);
}
$post['ingredients'] = implode("\n", $ingredients);

if (!$post) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Publicación</title>
   
</head>
<body>
    <h1>Editar Publicación #<?= htmlspecialchars($post['postId']) ?></h1>
    <?php if (isset($error)): ?>
        <div style="color: red; margin-bottom: 1rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="description" rows="8" cols="60" required><?= htmlspecialchars($post['description']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Pasos de preparación (un paso por línea)</label>
            <textarea name="steps" rows="8" cols="60" required><?= htmlspecialchars($post['steps']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Ingredientes (un ingrediente por línea)</label>
            <textarea name="ingredients" rows="8" cols="60" required><?= htmlspecialchars($post['ingredients']) ?></textarea>
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
            <button type="submit">Guardar cambios</button>
            <a href="index.php">Cancelar</a>
        </div>
    </form>
</body>
</html>

