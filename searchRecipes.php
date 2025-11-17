<?php
// searchRecipes.php
// este archivo maneja la funcionalidad de búsqueda de recetas con filtros de contenido, etiquetas e ingredientes
session_start();
// se conecta a la base de datos
require_once 'includes/config.php';

// se crea un array para almacenar las recetas encontradas
$posts = [];

// obtiene el userId del usuario de la sesión
$userId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

// obtiene los parámetros de búsqueda desde la URL 
$search = isset($_GET['contenido']) ? trim($_GET['contenido']) : '';
$tagsParam = isset($_GET['tags']) ? trim($_GET['tags']) : '';

// procesa los tags en un array de enteros
$tags = [];
if ($tagsParam !== '') {
    $tags = array_filter(array_map('intval', explode(',', $tagsParam)));
}

// procesa los ingredientes en arrays de ids y nombres
$ingredientsParam = isset($_GET['ingredients']) ? trim($_GET['ingredients']) : '';
$ingredientNamesParam = isset($_GET['ingredientNames']) ? trim($_GET['ingredientNames']) : '';
$ingredients = [];
$ingredientNames = [];
if ($ingredientsParam !== '') {
    $ingredients = array_filter(array_map('intval', explode(',', $ingredientsParam)));
}
if ($ingredientNamesParam !== '') {
    $tmp = array_map('trim', explode(',', $ingredientNamesParam));
    $ingredientNames = array_values(array_filter($tmp, function ($v) {
        return $v !== '';
    }));
}

// crea un array para las cláusulas WHERE
$where = [];
// 1. hacer consulta base y filtros dinámicos según tags/ingredients/search
$sql = "SELECT p.postId, p.title, p.description, p.postDate, u.displayName, u.userImage,
             (SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId) AS likesCount,
             (SELECT COUNT(*) FROM likes l2 WHERE l2.postId = p.postId AND l2.userId = ?) AS userLikedCount
         FROM post p
         JOIN users u ON p.userId = u.userId";

// 2. si hay búsqueda de texto añade la condición
if ($search !== '') {
    $where[] = "(p.title LIKE ? OR p.description LIKE ? )";
}

// 3. si hay tags, restringimos a posts que contengan todas las tags 
if (!empty($tags)) {
    $safeTagIds = implode(',', array_map('intval', $tags));
    $tagsCount = count($tags);
    $where[] = "p.postId IN (SELECT postId FROM postTags WHERE tagId IN ($safeTagIds) GROUP BY postId HAVING COUNT(DISTINCT tagId) = $tagsCount)";
}

// 4. si hay ingredient ids, restringimos a posts que contengan todas las ingredient ids
if (!empty($ingredients)) {
    $safeIngIds = implode(',', array_map('intval', $ingredients));
    $ingCount = count($ingredients);
    $where[] = "p.postId IN (SELECT postId FROM ingredientrecipe WHERE ingredientId IN ($safeIngIds) GROUP BY postId HAVING COUNT(DISTINCT ingredientId) = $ingCount)";
}

// 5. si hay ingredient names, filtramos por nombre o customIngredient
if (!empty($ingredientNames)) {
    // escapar y preparar lista de nombres
    $escaped = array_map(function ($n) use ($con) {
        return "'" . mysqli_real_escape_string($con, $n) . "'";
    }, $ingredientNames);
    $namesList = implode(',', $escaped);
    $nameCount = count($ingredientNames);
    $where[] = "p.postId IN (
        SELECT ir.postId FROM ingredientrecipe ir
        LEFT JOIN ingredients i ON ir.ingredientId = i.ingredientId
        WHERE (i.name IN ($namesList) OR ir.customIngredient IN ($namesList))
        GROUP BY ir.postId HAVING COUNT(DISTINCT COALESCE(i.name, ir.customIngredient)) = $nameCount
    )";
}

//6. se anaden las condiciones puestas a la consulta 
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY p.postDate DESC";

$stmt = mysqli_prepare($con, $sql);
//si preparacion de la consulta falla
if ($stmt === false) {
    echo json_encode(['error' => 'db_prepare_failed', 'msj' => mysqli_error($con), 'sql' => $sql]);
    exit();
}

// 7. prepara los parámetros para vincular las variables en el bind_param
$bindTypes = 'i';
$bindValues = [$userId];
if ($search !== '') {
    $like = "%" . $search . "%";
    $bindTypes .= 'ss';
    $bindValues[] = $like;
    $bindValues[] = $like;
}

$bind_ok = true;

if (!empty($bindValues)) {
    // preparar array de referencias
    $refs = [];
    foreach ($bindValues as $k => $v) {
        $refs[$k] = &$bindValues[$k];
    }
    // insertar el string de tipos al inicio
    array_unshift($refs, $bindTypes);
    // construir args: stmt + tipos + refs...
    $args = array_merge([$stmt], $refs);
    $bind_ok = call_user_func_array('mysqli_stmt_bind_param', $args);
    if ($bind_ok === false) {
        error_log('bind_param failed: ' . mysqli_error($con));
    }
}
// 8. ejecuta la consulta
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
// por cada publicación se obtiene la información y sus imágenes
while ($row = mysqli_fetch_assoc($res)) {
    if (!empty($row['userImage'])) {
        $row['userImage'] = base64_encode($row['userImage']);
    }
    $imageData = '';
    $row['images'] = [];
    // hacer la consulta para obtener las imágenes de la publicación
    $sqlImg = "SELECT imageData FROM recipeImages WHERE postId = ? ORDER BY imageOrder ASC";
    $stmtImg = mysqli_prepare($con, $sqlImg);
    if ($stmtImg) {
        mysqli_stmt_bind_param($stmtImg, 'i', $row['postId']);
        mysqli_stmt_execute($stmtImg);
        mysqli_stmt_bind_result($stmtImg, $imageData);
        while (mysqli_stmt_fetch($stmtImg)) {
            $row['images'][] = base64_encode($imageData);
        }
        mysqli_stmt_close($stmtImg);
    }
    // se procesan los conteos y estados de "me gusta"
    $row['likesCount'] = isset($row['likesCount']) ? intval($row['likesCount']) : 0;
    $row['userLiked'] = (isset($row['userLikedCount']) && intval($row['userLikedCount']) > 0) ? true : false;
    unset($row['userLikedCount']);
    unset($row['recipeImage']);
    $posts[] = $row;
}

echo json_encode($posts);