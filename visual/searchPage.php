<?php
session_start();
$busqueda = isset($_GET['search']) ? trim($_GET['search']) : '';
$activateTag = isset($_GET['activateTag']) ? intval($_GET['activateTag']) : 0;
require_once '../includes/config.php';
$topTags = [];
$allTags = [];
$sqlTags = "SELECT t.tagId, t.tagName, COUNT(pt.postId) AS used FROM tags t LEFT JOIN postTags pt ON t.tagId = pt.tagId GROUP BY t.tagId ORDER BY used DESC, t.tagId ASC";
$resTags = mysqli_query($con, $sqlTags);
if ($resTags) {
    while ($r = mysqli_fetch_assoc($resTags)) {
        $allTags[] = $r;
    }
    mysqli_free_result($resTags);
}


$topTags = array_slice($allTags, 0, 10);
$otherTags = array_slice($allTags, 10);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar recetas - MyFoods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../css/main.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
        <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/backButton.php'; ?>
    <div class="header-container">
        <div class="search-container">
            <input type="text" id="searchInput" class="input" placeholder="Buscar recetas" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button id="searchButton" class="buttono">Buscar</button>
        </div>
    </div>

        <div class="container my-3">
                <div class="d-flex flex-row flex-wrap gap-2 align-items-center" id="topTagsRow">
                        <?php foreach ($topTags as $t): ?>
                                <button type="button" class="input btn btn-sm btn-outline-primary tag-filter" data-bs-toggle="button" data-tag="<?php echo intval($t['tagId']); ?>"><?php echo htmlspecialchars($t['tagName']); ?></button>
                        <?php endforeach; ?>
                        <?php if (!empty($otherTags)): ?>
                                <button type="button" class="input btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#allTagsModal">Más etiquetas</button>
                        <?php endif; ?>
                </div>
        </div>

        <!-- Modal con el resto de etiquetas -->
        <div class="modal fade" id="allTagsModal" tabindex="-1" aria-labelledby="allTagsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allTagsModalLabel">Todas las etiquetas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($otherTags as $ot): ?>
                                <button type="button" class="input btn btn-sm btn-outline-primary tag-filter" data-tag="<?php echo intval($ot['tagId']); ?>"><?php echo htmlspecialchars($ot['tagName']); ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <h2>Resultados de búsqueda</h2>
    <div id="posts">
    </div>
    <script src="../js/searchRecipes/searchPost.js"></script>
    <script src="../js/searchRecipes/searchTags.js"></script>
    <script>

    document.addEventListener('DOMContentLoaded', function() {
        var search = <?php echo json_encode($busqueda); ?>;
        var activateTag = <?php echo json_encode($activateTag); ?>;
        
        if(search) {
            document.getElementById('searchInput').value = search;
        }
        
        if(activateTag) {
            const tagButton = document.querySelector(`.tag-filter[data-tag="${activateTag}"]`);
            if(tagButton) {
                tagButton.click();
            }
        } else if(search) {
            document.getElementById('searchButton').click();
        }
    });
    </script>
</body>
</html>
