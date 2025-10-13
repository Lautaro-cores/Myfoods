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


$sql = "SELECT title, description FROM post WHERE postId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $description);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Obtener todas las imágenes de la receta
$images = [];
$imgSql = "SELECT imageData FROM recipe_image WHERE postId = ?";
$imgStmt = mysqli_prepare($con, $imgSql);
if ($imgStmt) {
    mysqli_stmt_bind_param($imgStmt, "i", $postId);
    mysqli_stmt_execute($imgStmt);
    mysqli_stmt_bind_result($imgStmt, $imgData);
    while (mysqli_stmt_fetch($imgStmt)) {
        $images[] = base64_encode($imgData);
    }
    mysqli_stmt_close($imgStmt);
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
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

    <body>
        <h1 id="titulodelacomida"><?php echo htmlspecialchars($title); ?></h1>
        <?php if (!empty($images)) : ?>
            <div id="carouselRecipe" class="carousel slide mb-3" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($images as $idx => $imgBase64): ?>
                        <div class="carousel-item<?php if ($idx === 0) echo ' active'; ?>">
                            <img src="data:image/jpeg;base64,<?php echo $imgBase64; ?>" class="d-block w-100" alt="Imagen <?php echo $idx+1; ?> de la receta" style="max-height:400px; object-fit:contain; border-radius:6px;">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselRecipe" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselRecipe" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($description)); ?></p>

            <?php if (!empty($ingredientes)) : ?>
                <h2>Ingredientes</h2>
                <ul>
                    <?php foreach ($ingredientes as $ing) : ?>
                        <li><?php echo htmlspecialchars($ing); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($pasos)) : ?>
                <h2>Pasos</h2>
                <ol>
                    <?php foreach ($pasos as $paso) : ?>
                        <li><?php echo htmlspecialchars($paso); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

        <section id="commentsSection">
            <h2>Comentarios</h2>

            <form id="commentForm">
                <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                <textarea name="content" id="commentContent" placeholder="Escribe tu comentario..." rows="4" required></textarea>
                <br>
                <button type="submit" id="submitCommentBtn">Publicar Comentario</button>
            </form>
            <div id="commentMessage" role="alert" aria-live="polite"></div>

            <div id="commentsContainer">
                <p>Cargando comentarios...</p>
            </div>
        </section>

        <section id="likesSection">
            <h2>Likes</h2>
            <div id="likesContainer">
                <button id="likeBtn" type="button">❤ Me gusta</button>
                <span id="likesCount">0</span>
            </div>
        </section>

        <script
            src="../js/viewRecipe.js"
            data-default-image-url="../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg"></script>
    </body>

    </html>
<?php
} else {
    echo "Receta no encontrada.";
}
?>