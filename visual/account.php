<?php
session_start();

// Si el usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
if (!isset($_SESSION['userLogged'])) {
    header("Location: logIn.html");
    exit();
}

// Incluir el archivo de conexión a la base de datos
require_once "../connection.php";

$userName = $_SESSION['userLogged'];

// Obtener el correo electrónico y la imagen del usuario desde la base de datos
$sql = "SELECT userEmail, userImage FROM users WHERE userName = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
$userEmail = $user['userEmail'];

// Manejar la imagen del usuario
$userImage = '';
// Si el campo userImage de la base de datos está vacío
if (empty($user['userImage'])) {
    // Usar la ruta a tu imagen predeterminada
    // Asegúrate de que la ruta sea correcta
    $userImage = '../icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg'; 
} else {
    // Si la base de datos tiene una imagen, la usamos
    $userImage = 'data:image/jpeg;base64,' . base64_encode($user['userImage']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h2>Mi Perfil</h2>
    <img src="getUserImage.php" alt="Imagen de perfil" style="width:150px; height:150px; border-radius:50%;">
    <p>Nombre de usuario: <?php echo htmlspecialchars($userName); ?></p>
    <p>Correo electrónico: <?php echo htmlspecialchars($userEmail); ?></p>
    <br>
    <form id="formImage" enctype="multipart/form-data">
        <input type="file" name="userImage" id="userImageInput">
        <button type="submit">Subir Imagen</button>
    </form>
    <a href="index.html">Volver a la página principal</a>
    <a href="logout.php">Cerrar sesión</a>

    <script src="account.js"></script>
</body>
</html>