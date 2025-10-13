<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
</head>

<body>
    <h2>Registrarse</h2>
    <form id="formRegister">
        <input type="text" name="userName" id="regUserName" placeholder="Nombre de usuario" required autocomplete="username">
        <input type="email" name="userEmail" id="regUserEmail" placeholder="Correo electrónico" required autocomplete="email">
        <input type="password" name="userPassword" id="regUserPassword" placeholder="Contraseña" required minlength="5" autocomplete="new-password">
        <button type="submit" id="Registrar">Registrarse</button>
    </form>
    <div id="mensaje"></div>
    <br>
    <p>¿Ya tienes una cuenta? <a href="logIn.php">Inicia sesión aquí</a></p>
    <br>

    <script src="../js/register.js"></script>
</body>

</html>