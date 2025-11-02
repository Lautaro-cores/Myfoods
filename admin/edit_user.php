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
    $displayName = $_POST['displayName'] ?? '';
    $email = $_POST['userEmail'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['userType'] ?? 'user';

    $sql = "SELECT * FROM users WHERE userName = ? OR userEmail = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $name, $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        echo json_encode(["error" => true, "msj" => "El usuario o correo ya existe."]);
        exit();
    }

    $sql = "UPDATE users SET userName=?, displayName=?, userEmail=?, description=?, userType=? WHERE userId=?";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sssssi', $name, $displayName, $email, $description, $type, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: index.php'); exit;
}


$sql = "SELECT userId, userName, displayName, userEmail, description, userType FROM users WHERE userId=? LIMIT 1";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid, $uname, $udname, $uemail, $udesc, $utype);
    if (mysqli_stmt_fetch($stmt)) {
        $user = ['userId' => $uid, 'userName' => $uname, 'displayName' => $udname, 'userEmail' => $uemail, 'description' => $udesc, 'userType' => $utype];
    }
    mysqli_stmt_close($stmt);
}
if (!$user) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Editar Usuario</title></head><body>
<h1>Editar Usuario #<?= htmlspecialchars($user['userId']) ?></h1>
<form method="post">
    <label>Nombre de usuario<br><input name="userName" value="<?= htmlspecialchars($user['userName']) ?>"></label><br>
    <label>Nombre<br><input name="displayName" value="<?= htmlspecialchars($user['displayName']) ?: htmlspecialchars($user['userName']) ?>"></label><br>
    <label>Email<br><input name="userEmail" value="<?= htmlspecialchars($user['userEmail']) ?>"></label><br>
    <label>Descripción<br><textarea name="description"><?= htmlspecialchars($user['description']) ?></textarea></label><br>
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
