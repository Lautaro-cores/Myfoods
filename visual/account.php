<?php
session_start();
require_once "../includes/config.php";

if (!isset($_SESSION['userId'])) {
    header('Location: ../visual/logIn.php');
    exit();
}

$userId = intval($_SESSION['userId']);

$sql = "SELECT userName, userEmail, userImage FROM users WHERE userId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
$userName = $user['userName'];
$userEmail = $user['userEmail'];

$userImage = '';
if (empty($user['userImage'])) {
    $userImage = '../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
} else {
    $userImage = 'data:image/jpeg;base64,' . base64_encode($user['userImage']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
    <h2>Mi Perfil</h2>
    <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Imagen de perfil" style="width:150px; height:150px; border-radius:50%;">
    <p>Nombre de usuario: <?php echo htmlspecialchars($userName); ?></p>
    <p>Correo electrónico: <?php echo htmlspecialchars($userEmail); ?></p>
    <br>
    <form id="formImage" enctype="multipart/form-data">
        <input type="file" name="userImage" id="subirArchivo" class="input-oculto">

        <label for="subirArchivo" class="boton-personalizado">
            Seleccionar archivo
        </label>

        <button type="submit">Subir Imagen</button>
    </form>
    <a href="index.php">Volver a la página principal</a>
    <a href="../logout.php">Cerrar sesión</a>

    <script src="../js/account.js"></script>
</body>

</html>