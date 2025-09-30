<?php
session_start();
require_once "includes/config.php";

if (!isset($_GET["id"])) {
    echo "Receta no encontrada.";
    exit;
}

$postId = intval($_GET["id"]);
// Determinar si el usuario ha iniciado sesión
$isUserLogged = isset($_SESSION['userLogged']); 

$sql = "SELECT title, description FROM post WHERE postId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $description);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($title) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    </head>
<body>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($description)); ?></p>

    <section id="commentsSection">
        <h2>Comentarios</h2>
        
        <?php if ($isUserLogged): ?>
            <form id="commentForm">
                <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                <textarea name="content" id="commentContent" placeholder="Escribe tu comentario..." rows="4" required></textarea>
                <br>
                <button type="submit" id="submitCommentBtn">Publicar Comentario</button>
            </form>
            <div id="commentMessage" role="alert" aria-live="polite"></div>
        <?php else: ?>
            <p>Debes <a href="visual/logIn.php">iniciar sesión</a> para comentar.</p>
        <?php endif; ?>

        <div id="commentsContainer">
            <p>Cargando comentarios...</p>
        </div>
    </section>

    <script 
        src="js/viewRecipe.js"
        data-default-image-url="img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg"
    ></script>
</body>
</html>
<?php
} else {
    echo "Receta no encontrada.";
}
?>