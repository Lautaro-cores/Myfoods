<?php
session_start();
$busqueda = isset($_GET['search']) ? trim($_GET['search']) : '';
$activateTag = isset($_GET['activateTag']) ? intval($_GET['activateTag']) : 0;
require_once '../includes/config.php';
$topTags = [];
$allTags = [];
$sqlTags = "SELECT t.tagId, t.tagName, tc.categoryName, COUNT(pt.postId) AS used 
           FROM tags t 
           LEFT JOIN postTags pt ON t.tagId = pt.tagId 
           LEFT JOIN tagCategories tc ON t.categoryId = tc.categoryId 
           GROUP BY t.tagId, tc.categoryName 
           ORDER BY tc.categoryId, used DESC, t.tagId ASC";
$resTags = mysqli_query($con, $sqlTags);

$tagsByCategory = [];
if ($resTags) {
    while ($r = mysqli_fetch_assoc($resTags)) {
        $category = $r['categoryName'] ?? 'Otros';
        if (!isset($tagsByCategory[$category])) {
            $tagsByCategory[$category] = [];
        }
        $tagsByCategory[$category][] = $r;
    }
    mysqli_free_result($resTags);
}
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
            <input type="text" placeholder="buscar recetas" id="searchInput" class="input">
            <button id="searchButton" class="buttono" type="button">Buscar</button>
        </div>
    </div>

        <div class="container my-3">
            <div class="d-flex flex-row flex-wrap gap-2 align-items-center" id="topTagsRow">
                <!-- Solo dejar el botón 'Más etiquetas' aquí para simplicidad visual -->
                <button type="button" class="input btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#allTagsModal">
                    Más etiquetas
                </button>
            </div>
        </div>

        <!-- Sección: mostrar únicamente las etiquetas más usadas (top N). Modal sigue mostrando todas. -->
        <div class="container my-3">
            <div class="row">
                <div class="col-12">
                    <h5>Etiquetas más usadas</h5>
                    <div id="popularTagsList" class="d-flex flex-wrap gap-2">
                        <?php
                        // Construir lista plana de tags con uso > 0, ordenada por 'used' desc y limitada a top 20
                        $popularTags = [];
                        foreach ($tagsByCategory as $category => $tags) {
                            foreach ($tags as $tag) {
                                if (isset($tag['used']) && intval($tag['used']) > 0) {
                                    $tag['categoryName'] = $category;
                                    $popularTags[] = $tag;
                                }
                            }
                        }
                        usort($popularTags, function($a, $b) {
                            return intval($b['used']) - intval($a['used']);
                        });
                        // Mostrar como máximo 10 etiquetas para evitar que se formen bloques grandes
                        $popularTags = array_slice($popularTags, 0, 10);

                        foreach ($popularTags as $tag): ?>
                            <button type="button" class="tag-filter btn btn-sm btn-outline-primary" data-tag="<?php echo intval($tag['tagId']); ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <span><?php echo htmlspecialchars($tag['tagName']); ?></span>
                                    <small class="text-muted">(<?php echo intval($tag['used']); ?>)</small>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal con etiquetas categorizadas -->
        <div class="modal fade" id="allTagsModal" tabindex="-1" aria-labelledby="allTagsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allTagsModalLabel">Todas las etiquetas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($tagsByCategory as $category => $tags): ?>
                            <div class="mb-3">
                                <h5 class="mb-2"><?php echo htmlspecialchars($category); ?></h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($tags as $tag): ?>
                                        <button type="button" class="input btn btn-sm btn-outline-primary tag-filter" 
                                                data-tag="<?php echo intval($tag['tagId']); ?>"
                                                data-bs-toggle="button">
                                            <?php echo htmlspecialchars($tag['tagName']); ?>
                                            <small class="text-muted">(<?php echo intval($tag['used']); ?>)</small>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Sección para filtrar por ingredientes -->
                        <div class="mb-3">
                            <h5 class="mb-2">Filtrar por ingredientes</h5>
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <input id="ingredientInput" type="text" class="input form-control" placeholder="Escribe un ingrediente" autocomplete="off">
                                <button id="addIngredientBtn" class="btn btn-outline-primary">Añadir</button>
                            </div>
                            <div id="ingredientSuggestions" class="list-group" style="position:relative; z-index:1050; max-height:200px; overflow:auto; display:none;"></div>
                            <div id="selectedIngredients" class="d-flex flex-wrap gap-2 mt-2"></div>
                            <small class="text-muted">Puedes seleccionar varios ingredientes; la búsqueda mostrará recetas que contengan todos ellos.</small>
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
    <script src="../js/searchRecipes/searchIngredients.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var search = <?php echo json_encode($busqueda); ?>;
        var activateTag = <?php echo json_encode($activateTag); ?>;

        if (activateTag) {
            const tagButton = document.querySelector(`.tag-filter[data-tag="${activateTag}"]`);
            if (tagButton) tagButton.click();
        } else if (search) {
            // If a global performSearch exists (exposed by searchPost.js), use it, otherwise try legacy button
            if (typeof window.performSearch === 'function') {
                window.performSearch(search);
            } else {
                const inputEl = document.getElementById('searchInput');
                if (inputEl) inputEl.value = search;
                const btn = document.getElementById('searchButton');
                if (btn) btn.click();
            }
        }
    });
    </script>
</body>
</html>
