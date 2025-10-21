<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {

    header('Location: ../visual/index.php'); 
    
    exit(); 
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['userName'] ?? '';
    $email = $_POST['userEmail'] ?? '';
    $type = $_POST['userType'] ?? 'user';
    $sql = "UPDATE users SET userName=?, userEmail=?, userType=? WHERE userId=?";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $type, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: index.php'); exit;
}

$user = null;
$sql = "SELECT userId, userName, userEmail, userType FROM users WHERE userId=? LIMIT 1";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid, $uname, $uemail, $utype);
    if (mysqli_stmt_fetch($stmt)) {
        $user = ['userId' => $uid, 'userName' => $uname, 'userEmail' => $uemail, 'userType' => $utype];
    }
    mysqli_stmt_close($stmt);
}
if (!$user) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Editar Usuario</title></head><body>
<h1>Editar Usuario #<?= htmlspecialchars($user['userId']) ?></h1>
<form method="post">
    <label>Nombre<br><input name="userName" value="<?= htmlspecialchars($user['userName']) ?>"></label><br>
    <label>Email<br><input name="userEmail" value="<?= htmlspecialchars($user['userEmail']) ?>"></label><br>
    <label>Tipo<br>
        <select name="userType">
            <option value="user" <?= $user['userType'] === 'user' ? 'selected' : '' ?>>Usuario</option>
            <option value="admin" <?= $user['userType'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </label><br>
    <button type="submit">Guardar</button>
    <a href="index.php">Cancelar</a>
</form>
</body></html>
