<?php
// getIngredients.php
//este archivo obtiene la lista de ingredientes y filtra por el input para autocompletar

session_start();
//se conecta a la base de datos
require_once "includes/config.php";
header('Content-Type: application/json');

// obtiene el término de búsqueda del parámetro GET
$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($term)) {
    echo json_encode([]);
    exit();
}

// hace la consulta para obtener los ingredientes que coinciden con el término de búsqueda
$sql = "SELECT ingredientId, name FROM ingredients WHERE name LIKE ? LIMIT 10";
$stmt = mysqli_prepare($con, $sql);
$searchTerm = "%$term%";
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// crea un array para almacenar los ingredientes
$ingredients = [];

// por cada ingrediente obtenido de la consulta, se agrega al array
while ($row = mysqli_fetch_assoc($result)) {
    $ingredients[] = [
        'id' => $row['ingredientId'],
        'value' => $row['name']
    ];
}

echo json_encode($ingredients);