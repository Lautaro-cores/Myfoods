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
$sql = "SELECT p.title, p.description, p.userId, u.userName, u.userImage 
        FROM post p 
        LEFT JOIN users u ON p.userId = u.userId 
        WHERE p.postId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $description, $authorId, $authorName, $authorImage);
mysqli_stmt_fetch($stmt);
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
        $images[] = base64_encode($imageData);
    }
    mysqli_stmt_close($stmtImg);
}

if ($title) {

    // Obtener ingredientes
    $ingredientes = [];
    $sqlIng = "SELECT ingredient FROM ingredientrecipe WHERE postId = ?";
    $stmtIng = mysqli_prepare($con, $sqlIng);
    if ($stmtIng) {
        mysqli_stmt_bind_param($stmtIng, "i", $postId);
        mysqli_stmt_execute($stmtIng);
        mysqli_stmt_bind_result($stmtIng, $ingrediente);
        while (mysqli_stmt_fetch($stmtIng)) {
            $ingredientes[] = $ingrediente;
        }
        mysqli_stmt_close($stmtIng);
    }

    // Obtener pasos
    $pasos = [];
    $sqlPaso = "SELECT step FROM recipestep WHERE postId = ?";
    $stmtPaso = mysqli_prepare($con, $sqlPaso);
    if ($stmtPaso) {
        mysqli_stmt_bind_param($stmtPaso, "i", $postId);
        mysqli_stmt_execute($stmtPaso);
        mysqli_stmt_bind_result($stmtPaso, $paso);
        while (mysqli_stmt_fetch($stmtPaso)) {
            $pasos[] = $paso;
        }
        mysqli_stmt_close($stmtPaso);
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
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <!-- Bootstrap JS y Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    </head>

    <body>
        <?php include '../nawbar.php'; ?>
        <?php include '../backButton.php'; ?>

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
                                    <img src="data:image/jpeg;base64,<?php echo $imgBase64; ?>" class="image-recipe" alt="Imagen <?php echo $img + 1; ?> de <?php echo htmlspecialchars($title); ?>">
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

        <h1 class="input"><?php echo ($title); ?></h1>

          <div class="recipe-author">
                 <a href="account.php?username=<?php echo urlencode($authorName); ?>" class="author-link">
                    <img src="<?php echo !empty($authorImage) ? 'data:image/jpeg;base64,' . base64_encode($authorImage) : '../img/default-profile.jpg'; ?>" 
                         alt="Foto de <?php echo htmlspecialchars($authorName); ?>"
                         class="author-image">
                </a>
                <a href="account.php?username=<?php echo urlencode($authorName); ?>" class="author-link">
                    <span class="author-name">Publicado por: <?php echo htmlspecialchars($authorName); ?></span>
                </a>
            </div>            
     
        <?php if (!empty($tags)): ?>
            <div class="recipe-tags">
                <strong>Etiquetas: </strong>
                <?php foreach ($tags as $t): ?>
                    <a href="searchPage.php?activateTag=<?php echo intval($t['tagId']); ?>" class="tag-badge input"><?php echo htmlspecialchars($t['tagName']); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
            <br>
           <p class="input"><?php echo ($description); ?></p>
        </div>

    </div>
    <div class="recipe-content">                
        <div class="recipe-ingredients">
            <h2>Ingredientes</h2>
            <ul >
                <?php foreach ($ingredientes as $ing): ?>
                    <li class="ingredient-input"><?php echo htmlspecialchars($ing); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="recipe-steps">
            <h2>Pasos</h2>
            <ol>
                <?php foreach ($pasos as $paso): ?>
                    <li class="step-input">
                        <?php echo htmlspecialchars($paso); ?>
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
        </div>

        <div class="recipe-comments">
            <h2>Comentarios</h2>

            <form id="commentForm">
                <div class="comment-form">
                    <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                    <input name="content" id="commentContent"  placeholder="Escribe tu comentario..." class="input"
                        required></input>
                    <br>
                    <button type="submit" id="submitCommentBtn" class="buttono">Publicar Comentario</button>
                </div>
            </form>
            <div id="commentMessage" role="alert" aria-live="polite"></div>

            <div id="commentsContainer">
                <p>Cargando comentarios...</p>
            </div>
        </div>

        

        <script src="../js/viewRecipe.js"
            data-default-image-url="../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg"></script>
    </body>

    </html>
    <?php
} else {
    echo "Receta no encontrada.";
}
?>