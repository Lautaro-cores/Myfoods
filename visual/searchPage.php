<?php
// searchpage.php - Página de búsqueda avanzada
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar recetas - MyFoods</title>
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../nawbar.php'; ?>
    <div class="header-container">
        <div class="search-container">
            <input type="text" id="searchInput" class="input" placeholder="Buscar recetas" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button id="searchButton" class="buttono">Buscar</button>
        </div>
    </div>
    <h2>Resultados de búsqueda</h2>
    <div id="posts"></div>
    <script src="../js/search.js"></script>
    <script>
    // Si hay búsqueda inicial, dispara búsqueda automáticamente
    document.addEventListener('DOMContentLoaded', function() {
        var q = <?php echo json_encode($busqueda); ?>;
        if(q) {
            document.getElementById('searchInput').value = q;
            document.getElementById('searchButton').click();
        }
    });
    </script>
</body>
</html>
