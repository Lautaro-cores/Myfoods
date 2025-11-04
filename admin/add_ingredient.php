<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    echo json_encode(['success' => false, 'msj' => 'No autorizado']);
    exit;
}
$name = trim($_POST['name'] ?? '');
if ($name === '') {
    echo json_encode(['success' => false, 'msj' => 'Nombre vacío']);
    exit;
}
$sql = "INSERT INTO ingredients (name) VALUES (?)";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 's', $name);
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msj' => 'Error al agregar']);
}
mysqli_stmt_close($stmt);
