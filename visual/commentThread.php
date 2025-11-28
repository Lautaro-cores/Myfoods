<?php
session_start();
require_once "../includes/config.php";

if (!isset($_GET["commentId"])) {
    echo "Comentario no encontrado.";
    exit;
}

$commentId = intval($_GET["commentId"]);
$isUserLogged = isset($_SESSION['userId']);

// Obtener datos del comentario principal
$sql = "SELECT c.commentId, c.userId, c.postId, c.content, c.parentId, 
               u.userName, u.displayName, u.userImage,
               p.title as postTitle,
               COALESCE(cl.likeCount, 0) as likeCount,
               CASE WHEN cl_user.likeId IS NOT NULL THEN 1 ELSE 0 END as isLiked
        FROM comment c
        JOIN users u ON c.userId = u.userId
        JOIN post p ON c.postId = p.postId
        LEFT JOIN (
            SELECT commentId, COUNT(*) as likeCount 
            FROM commentLikes 
            GROUP BY commentId
        ) cl ON c.commentId = cl.commentId
        LEFT JOIN commentLikes cl_user ON c.commentId = cl_user.commentId AND cl_user.userId = ?
        WHERE c.commentId = ?";

$stmt = mysqli_prepare($con, $sql);
$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
mysqli_stmt_bind_param($stmt, "ii", $userId, $commentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mainComment = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$mainComment) {
    echo "Comentario no encontrado.";
    exit;
}

// Convertir imagen a base64
if (!empty($mainComment['userImage'])) {
    $mainComment['userImage'] = base64_encode($mainComment['userImage']);
}

// Obtener todas las respuestas de este comentario (anidadas)
$sqlReplies = "SELECT c.commentId, c.userId, c.postId, c.content, c.parentId,
                      u.userName, u.displayName, u.userImage,
                      COALESCE(cl.likeCount, 0) as likeCount,
                      CASE WHEN cl_user.likeId IS NOT NULL THEN 1 ELSE 0 END as isLiked
               FROM comment c
               JOIN users u ON c.userId = u.userId
               LEFT JOIN (
                   SELECT commentId, COUNT(*) as likeCount 
                   FROM commentLikes 
                   GROUP BY commentId
               ) cl ON c.commentId = cl.commentId
               LEFT JOIN commentLikes cl_user ON c.commentId = cl_user.commentId AND cl_user.userId = ?
               WHERE c.parentId = ?
               ORDER BY c.commentId ASC";

$stmtReplies = mysqli_prepare($con, $sqlReplies);
mysqli_stmt_bind_param($stmtReplies, "ii", $userId, $commentId);
mysqli_stmt_execute($stmtReplies);
$resultReplies = mysqli_stmt_get_result($stmtReplies);

$allReplies = [];
while ($row = mysqli_fetch_assoc($resultReplies)) {
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    $row['replies'] = [];
    $allReplies[] = $row;
}
mysqli_stmt_close($stmtReplies);

// Construir estructura anidada de respuestas
$repliesTree = [];
$indexedReplies = [];

foreach ($allReplies as $reply) {
    $indexedReplies[$reply['commentId']] = $reply;
}

foreach ($indexedReplies as $id => &$reply) {
    if ($reply['parentId'] == $commentId) {
        $repliesTree[] = &$reply;
    } else {
        if (isset($indexedReplies[$reply['parentId']])) {
            $indexedReplies[$reply['parentId']]['replies'][] = &$reply;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentario - <?php echo htmlspecialchars($mainComment['userName']); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/backButton.php'; ?>
    

    <div class="container mt-4">
    
        <!-- Comentario principal -->
        <div class="main-comment-container">
            <div class="comment main-comment" data-comment-id="<?php echo $mainComment['commentId']; ?>">
                <img class="author-image" src="<?php echo $mainComment['userImage'] ? 'data:image/jpeg;base64,' . $mainComment['userImage'] : '../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg'; ?>" 
                     alt="Perfil de <?php echo htmlspecialchars($mainComment['userName']); ?>">
                <div class="comment-body">
                    <strong class="comment-username"><?php echo htmlspecialchars($mainComment['displayName'] ?: $mainComment['userName']); ?></strong>
                    <p class="comment-content"><?php echo htmlspecialchars($mainComment['content']); ?></p>
                    <div class="comment-actions">
                        <button class="comment-like-btn" data-comment-id="<?php echo $mainComment['commentId']; ?>">
                            <i class="bi bi-heart<?php echo $mainComment['isLiked'] ? '-fill' : ''; ?>"></i>
                            <span class="like-count"><?php echo $mainComment['likeCount']; ?></span>
                        </button>
            
                        <span class="comment-count">
                            <i class="bi bi-chat"></i> <?php echo count($allReplies); ?> comentarios
                        </span>
                          <button class="report-btn btn btn-outline-danger btn-sm ms-2" data-target-type="comment" data-target-id="<?php echo $mainComment['commentId']; ?>" type="button" aria-label="Denunciar comentario"><i class="bi bi-flag"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario para comentar en el comentario principal -->
        <?php if ($isUserLogged): ?>
        <div class="comment-to-main-form mt-4">
            <h5>Comentar en este comentario</h5>
            <form id="replyToMainForm" enctype="multipart/form-data">
                <input type="hidden" name="postId" value="<?php echo $mainComment['postId']; ?>">
                <input type="hidden" name="parentId" value="<?php echo $mainComment['commentId']; ?>">
                <textarea name="content" placeholder="Escribe tu comentario..." class="form-control" rows="3" required></textarea>
                
                <!-- Campo para subir imágenes -->
                <div class="mt-3">
                    <label for="replyImages" class="form-label">
                        <i class="bi bi-image"></i> Agregar imágenes (máximo 3)
                    </label>
                    <input type="file" id="replyImages" name="commentImages[]" 
                           class="form-control" multiple accept="image/*" 
                           onchange="previewReplyImages(this)">
                    <div id="replyImagePreview" class="mt-2"></div>
                    <small class="form-text text-muted">
                        Formatos permitidos: JPG, PNG, GIF, WebP. Máximo 5MB por imagen.
                    </small>
                </div>
                
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary">Comentar</button>
                </div>
            </form>
            <div id="replyMessage" role="alert" aria-live="polite"></div>
        </div>
        <?php else: ?>
        <div class="mt-4">
            <p class="text-muted">Debes <a href="logIn.php">iniciar sesión</a> para comentar.</p>
        </div>
        <?php endif; ?>

        <!-- Comentarios -->
        <div class="comments-section mt-4">
            <h5>Comentarios (<?php echo count($allReplies); ?>)</h5>
            <div id="commentsContainer">
                <?php if (empty($repliesTree)): ?>
                    <p class="text-muted">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script type="module" src="../js/viewRecipes/commentThread.js" 
        data-default-image-url="../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg"
        data-comment-id="<?php echo $mainComment['commentId']; ?>"
        data-post-id="<?php echo $mainComment['postId']; ?>"></script>
    <?php include '../includes/reportModal.php'; ?>
    <script src="../js/report/report.js" defer></script>
    <script>
    // Función para vista previa de imágenes de comentarios
    function previewReplyImages(input) {
        const preview = document.getElementById('replyImagePreview');
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
</body>
</html>
