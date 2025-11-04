<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    echo json_encode(['success' => false, 'msj' => 'No autorizado']);
    exit;
}
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'msj' => 'ID inválido']);
    exit;
}
$sql = "DELETE FROM ingredients WHERE ingredientId=?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msj' => 'Error al eliminar']);
}
mysqli_stmt_close($stmt);
