<?php
session_start();
require_once "includes/config.php";
header('Content-Type: application/json');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($term)) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT ingredientId, name FROM ingredients WHERE name LIKE ? LIMIT 10";
$stmt = mysqli_prepare($con, $sql);
$searchTerm = "%$term%";
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$ingredients = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ingredients[] = [
        'id' => $row['ingredientId'],
        'value' => $row['name']
    ];
}

echo json_encode($ingredients);