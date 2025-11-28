<?php

session_start();
require_once "../includes/config.php";

if (!isset($_GET["id"])) {
    echo "Receta no encontrada.";
    exit;
}

$postId = intval($_GET["id"]);
// Determinar si el usuario ha iniciado sesión
$isUserLogged = isset($_SESSION['userId']);


// Obtener datos de la receta y el usuario
$sql = "SELECT p.title, p.description, p.userId, u.userName, u.displayName, u.userImage 
        FROM post p 
        LEFT JOIN users u ON p.userId = u.userId 
        WHERE p.postId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $description, $authorId, $authorName, $authorDisplayName, $authorImage);
// Si no hay resultado, mostrar mensaje y salir
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    echo "Receta no encontrada.";
    exit;
}
mysqli_stmt_close($stmt);

// Obtener todas las imágenes de la receta
$images = [];
$sqlImg = "SELECT imageData FROM recipeImages WHERE postId = ? ORDER BY imageOrder ASC";
$stmtImg = mysqli_prepare($con, $sqlImg);
if ($stmtImg) {
    mysqli_stmt_bind_param($stmtImg, "i", $postId);
    mysqli_stmt_execute($stmtImg);
    mysqli_stmt_bind_result($stmtImg, $imageData);
        while (mysqli_stmt_fetch($stmtImg)) {
            if ($imageData !== null && $imageData !== '') {
                $images[] = base64_encode($imageData);
            }
        }
    mysqli_stmt_close($stmtImg);
}

if ($title) {

    // Obtener ingredientes
    $ingredientes = [];
    $sqlIng = "SELECT ir.quantity, 
                      COALESCE(i.name, ir.customIngredient) as ingredient_name,
                      i.ingredientId,
                      CASE WHEN i.ingredientId IS NOT NULL THEN 1 ELSE 0 END as is_structured
               FROM ingredientrecipe ir 
               LEFT JOIN ingredients i ON ir.ingredientId = i.ingredientId 
               WHERE ir.postId = ?
               ORDER BY ir.ingredientRecipeId";
    $stmtIng = mysqli_prepare($con, $sqlIng);
    if ($stmtIng) {
        mysqli_stmt_bind_param($stmtIng, "i", $postId);
        mysqli_stmt_execute($stmtIng);
        $result = mysqli_stmt_get_result($stmtIng);
        while ($row = mysqli_fetch_assoc($result)) {
            $ingredientes[] = [
                'nombre' => $row['ingredient_name'],
                'cantidad' => $row['quantity'],
                'id' => $row['ingredientId'],
                'estructurado' => $row['is_structured']
            ];
        }
        mysqli_stmt_close($stmtIng);
    }

    // Obtener pasos
    $pasos = [];
    $sqlPaso = "SELECT recipeStepId, step FROM recipestep WHERE postId = ? ORDER BY recipeStepId ASC";
    $stmtPaso = mysqli_prepare($con, $sqlPaso);
    if ($stmtPaso) {
        mysqli_stmt_bind_param($stmtPaso, "i", $postId);
        mysqli_stmt_execute($stmtPaso);
        mysqli_stmt_bind_result($stmtPaso, $recipeStepId, $pasoText);
        while (mysqli_stmt_fetch($stmtPaso)) {
            $pasos[] = ['id' => $recipeStepId, 'step' => $pasoText, 'images' => []];
        }
        mysqli_stmt_close($stmtPaso);
    }

    // Cargar imágenes de pasos (si existen) y agruparlas por recipeStepId
    if (!empty($pasos)) {
        $stepIds = array_map(function($p){ return intval($p['id']); }, $pasos);
        $in = implode(',', $stepIds);
        $sqlStepImgs = "SELECT recipeStepId, imageData FROM stepImages WHERE recipeStepId IN ($in) ORDER BY recipeStepId ASC, imageOrder ASC";
        $resStepImgs = mysqli_query($con, $sqlStepImgs);
        if ($resStepImgs) {
            $grouped = [];
            while ($row = mysqli_fetch_assoc($resStepImgs)) {
                $rid = intval($row['recipeStepId']);
                $grouped[$rid][] = base64_encode($row['imageData']);
            }
            mysqli_free_result($resStepImgs);

            // asignar imágenes a los pasos
            foreach ($pasos as &$p) {
                $rid = intval($p['id']);
                if (isset($grouped[$rid])) {
                    $p['images'] = $grouped[$rid];
                }
            }
            unset($p);
        }
    }
    
        // Obtener tags asociados a la receta
        $tags = [];
    $sqlTags = "SELECT t.tagId, t.tagName, c.categoryName FROM tags t LEFT JOIN tagCategories c ON t.categoryId = c.categoryId JOIN postTags pt ON t.tagId = pt.tagId WHERE pt.postId = ? ORDER BY t.tagId ASC";
        $stmtTags = mysqli_prepare($con, $sqlTags);
        if ($stmtTags) {
            mysqli_stmt_bind_param($stmtTags, "i", $postId);
            mysqli_stmt_execute($stmtTags);
            mysqli_stmt_bind_result($stmtTags, $tagId, $tagName, $catName);
            while (mysqli_stmt_fetch($stmtTags)) {
                $tags[] = ['tagId' => $tagId, 'tagName' => $tagName, 'categoryName' => $catName];
            }
            mysqli_stmt_close($stmtTags);
        }
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?></title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- CSS principal -->
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <!-- Bootstrap JS y Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    </head>

    <body>
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/backButton.php'; ?>

        <div class="recipe-header">

          
          

            <div class="recipe-image">
                <?php if (!empty($images)): ?>
                    <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
                        <!-- Indicadores -->
                        <?php if (count($images) > 1): ?>
                            <div class="carousel-indicators">
                                <?php foreach ($images as $img => $imgBase64): ?>
                                    <button type="button" data-bs-target="#recipeCarousel" data-bs-slide-to="<?php echo $img; ?>"<?php echo $img === 0 ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $img + 1; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Imágenes -->
                        <div class="carousel-inner">
                            <?php foreach ($images as $img => $imgBase64): ?>
                                <div class="carousel-item <?php echo $img === 0 ? 'active' : ''; ?>">
                                    <img src="data:image/jpeg;base64,<?php echo $imgBase64; ?>" class="image-recipe" alt="Imagen <?php echo $img + 1; ?> de <?php echo htmlspecialchars($title ?? ''); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($images) > 1): ?>
                            <!-- Controles -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#recipeCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#recipeCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>


        <div class="recipe-info">

        <h1 class="input"><?php echo htmlspecialchars($title ?? ''); ?></h1>

          <div class="recipe-author">
                 <a href="account.php?username=<?php echo urlencode($authorName ?? ''); ?>" class="author-link">
                    <img src="<?php echo !empty($authorImage) ? 'data:image/jpeg;base64,' . base64_encode($authorImage) : '../img/default-profile.jpg'; ?>" 
                         alt="Foto de <?php echo htmlspecialchars($authorName ?? ''); ?>"
                         class="author-image">
                </a>
                <a href="account.php?username=<?php echo urlencode($authorName ?? ''); ?>" class="author-link">
                    <span class="author-name"><?php echo htmlspecialchars($authorDisplayName ?? ''); ?></span>
                    <span class="author-name">@<?php echo htmlspecialchars($authorName ?? ''); ?></span>
                </a>
            </div>            
     
        <?php if (!empty($tags)): ?>
            <div class="recipe-tags">
                <strong>Etiquetas: </strong>
                <?php foreach ($tags as $t): ?>
                    <a href="searchPage.php?activateTag=<?php echo intval($t['tagId'] ?? 0); ?>" class="tag-badge input"><?php echo htmlspecialchars($t['tagName'] ?? ''); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
            <br>
           <p class="input"><?php echo htmlspecialchars($description ?? ''); ?></p>
        </div>

    </div>
    <div class="recipe-content">                
        <div class="recipe-ingredients">
            <h2>Ingredientes</h2>
            <ul>
                <?php foreach ($ingredientes as $ing): ?>
                    <li class="ingredient-input">
                        <?php if ($ing['estructurado']): ?>
                            <a href="searchPage.php?ingredient=<?php echo urlencode($ing['id']); ?>" class="ingredient-link">
                                <?php echo htmlspecialchars($ing['nombre']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($ing['nombre']); ?>
                        <?php endif; ?>
                        <span class="ingredient-quantity"><?php echo htmlspecialchars($ing['cantidad']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="recipe-steps">
            <h2>Pasos</h2>
            <ol>
                <?php foreach ($pasos as $p): ?>
                    <li class="step-input">
                        <?php echo htmlspecialchars($p['step'] ?? ''); ?>
                        <?php if (!empty($p['images'])): ?>
                            <div class="step-image mt-2">
                                <?php foreach ($p['images'] as $imgBase64): ?>
                                    <img src="data:image/jpeg;base64,<?php echo $imgBase64; ?>" alt="Imagen del paso"  onclick="openImageModal('data:image/jpeg;base64,<?php echo $imgBase64; ?>')" style="max-width:300px; border-radius:8px; margin-right:8px;" />
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        </div>

        <div class="interaction-container">
            <div class="like-section">
                <button id="likeBtn" class="like-button" type="button">
                    <i class="bi bi-heart"></i>
                </button>
                <span id="likesCount" class="like-count">0</span>
            </div>
            <div class="favorite-section">
                <button id="favBtn" class="fav-button" type="button" title="Guardar receta">
                    <i class="bi bi-bookmark"></i>
                </button>
            </div>
            <div class="report-section">
                <button class="report-btn btn btn-sm" data-target-type="post" data-target-id="<?php echo $postId; ?>" type="button" aria-label="Denunciar publicación"><i class="bi bi-flag"></i></button>
            </div>
        </div>

        <div class="recipe-comments">
            <h2>Comentarios</h2>

            <form id="commentForm" enctype="multipart/form-data">
                <div class="comment-form">
                    <div>
                    <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                    <input name="content" id="commentContent"  placeholder="Escribe tu comentario..." class="input input-comment"
                        required maxlength="255"></input>

                      <div class="mt-3">
                        <label for="commentImages" class="form-label">
                            <i class="bi bi-image"></i> Agregar imágenes (máximo 3)
                        </label>
                        <input type="file" id="commentImages" name="commentImages[]" 
                               class="form-control" multiple accept="image/*" 
                               onchange="previewCommentImages(this)">
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                    
                    </div>   

                    <div class="summit-section">
  <p class="clasificacion">
    <input id="radio1" type="radio" name="stars" value="5">
        <label class="comment-stars" for="radio1">★</label>
    <input id="radio2" type="radio" name="stars" value="4">
        <label class="comment-stars" for="radio2">★</label>
    <input id="radio3" type="radio" name="stars" value="3">
        <label class="comment-stars" for="radio3">★</label>
    <input id="radio4" type="radio" name="stars" value="2">
        <label class="comment-stars" for="radio4">★</label>
    <input id="radio5" type="radio" name="stars" value="1">
        <label class="comment-stars" for="radio5">★</label>
  </p>

 
                  

                    
                    <button type="submit" id="submitCommentBtn" class="buttono">Publicar Comentario</button>
                                </div>
                </div>
            </form>
            <div id="commentMessage" role="alert" aria-live="polite"></div>

            <div id="commentsContainer">
                <p>Cargando comentarios...</p>
            </div>
        </div>

        

        <script type="module" src="../js/viewRecipes/viewRecipe.js"
            data-default-image-url="../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg"></script>
        
        <script>
        // Función para vista previa de imágenes de comentarios
        function previewCommentImages(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                const maxFiles = Math.min(input.files.length, 3);
                
                for (let i = 0; i < maxFiles; i++) {
                    const file = input.files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail me-2 mb-2';
                        img.style.maxWidth = '100px';
                        img.style.maxHeight = '100px';
                        preview.appendChild(img);
                    };
                    
                    reader.readAsDataURL(file);
                }
                
                if (input.files.length > 3) {
                    const warning = document.createElement('div');
                    warning.className = 'alert alert-warning mt-2';
                    warning.textContent = 'Solo se mostrarán las primeras 3 imágenes.';
                    preview.appendChild(warning);
                }
            }
        }
        </script>
    <?php include '../includes/reportModal.php'; ?>
    <script src="../js/report/report.js" defer></script>
    </body>

    </html>
    <?php
} else {
    echo "Receta no encontrada.";
}
?>