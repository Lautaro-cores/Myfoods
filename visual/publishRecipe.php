<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: logIn.php');
    exit();
}

require_once "../includes/config.php";

// Cargar tags agrupados por categoryId (NULL -> 'other'), ordenados por categoryId
$tagsByCategory = [];
$sqlTags = "SELECT t.tagId, t.tagName, c.categoryId, c.categoryName FROM tags t LEFT JOIN tagCategories c ON t.categoryId = c.categoryId ORDER BY (c.categoryId IS NULL), c.categoryId, t.tagId";
$resTags = mysqli_query($con, $sqlTags);
if ($resTags) {
    while ($r = mysqli_fetch_assoc($resTags)) {
        $catId = isset($r['categoryId']) && $r['categoryId'] !== null ? (string) intval($r['categoryId']) : 'other';
        $catName = $r['categoryName'] ?? 'Otros';
        if (!isset($tagsByCategory[$catId])) {
            $tagsByCategory[$catId] = ['id' => $catId, 'name' => $catName, 'tags' => []];
        }
        $tagsByCategory[$catId]['tags'][] = ['tagId' => intval($r['tagId']), 'tagName' => $r['tagName']];
    }
    mysqli_free_result($resTags);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Receta</title>
    <!--icono de la pagina  -->
    <link rel="icon" type="image/x-icon" href="img/gorromostacho 3 (1).png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php include '../nawbar.php'; ?>
    <?php include '../backButton.php'; ?>
    <form id="formPublish" enctype="multipart/form-data" method="post">
        <button type="submit" class="buttono">Publicar</button>
        <button type="reset" class="buttonw">Eliminar</button>

        <div class="publish-header">

        <div class="publish-image">
                <div class="image-upload">
                    <input type="file" name="recipeImages[]" id="imageInput" class="hide" accept="image/*" multiple />
                    <label for="imageInput">Subir imágenes (máximo 3)</label>
                </div>
                <div id="imagePreview" class="image-preview"></div>
            </div>

            <div class="publish-info">
                <input type="text" name="title" id="recipeTitle" class="input" placeholder="Título de la receta" required>
                
                <div class="author-info">
                    <a href="account.php?username=<?php echo urlencode($_SESSION['userName']); ?>" class="author-link">
                        <img src="../getUserImage.php" alt="Foto de perfil" class="author-image">
                        <span class="author-name"><?php echo htmlspecialchars($_SESSION['userName']); ?></span>
                    </a>
                </div>

                <input type="text" name="description" id="recipeDescription" class="input" placeholder="Cuentanos mas acerca de este plato" required>
                
            <div class="publish-tags">
                <button class="btn btn-outline-secondary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#tagsCollapse" aria-expanded="false" aria-controls="tagsCollapse">
                    Etiquetas
                </button>

                <div class="collapse" id="tagsCollapse">
                    <div class="card card-body tags-container p-3">
                        <?php if (empty($tagsByCategory)): ?>
                            <p class="mb-0">No hay etiquetas disponibles.</p>
                        <?php else: ?>
                            <?php foreach ($tagsByCategory as $cat): ?>
                                <div class="tag-category mb-2">
                                    <strong class="d-block mb-1"><?php echo htmlspecialchars($cat['name']); ?></strong>
                                    <div class="tag-list d-flex flex-wrap gap-2">
                                        <?php foreach ($cat['tags'] as $tag): ?>
                                            <label class="tag-item input btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2">
                                                <input type="checkbox" name="tags[]" value="<?php echo intval($tag['tagId']); ?>" data-category="<?php echo htmlspecialchars($cat['id']); ?>" class="me-1"> <?php echo htmlspecialchars($tag['tagName']); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            </div>

        </div>
        <div class="publish-content">
            <div class="publish-ingredients">
                <label>Ingredientes:</label>
                <div id="ingredients-list">
                    <div class="input-container">
                        <div class="input-wrapper">
                            <input type="text" name="ingredientes[]" class="input-ingredient input" placeholder="Ingrediente 1" required>
                        </div>
                        <div class="button-wrapper">
                        </div>
                    </div>
                </div>
                <button type="button" id="addIngrediente" class="buttonw">Agregar ingrediente</button>

            </div>

            <div class="publish-steps">

                <label>Pasos de la receta:</label>
                <div id="steps-list">
                    <div class="input-container">
                        <div class="input-wrapper">
                            <input type="text" name="pasos[]" class="input-step input" placeholder="Paso 1" required>
                        </div>
                        <div class="button-wrapper">
                        </div>
                    </div>
                </div>
                <button type="button" id="addPaso" class="buttonw">Agregar paso</button>


            </div>
    </form>
    <div id="mensaje"></div>



    <script src="../js/publish.js"></script>

</body>

</html>