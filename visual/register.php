<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
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
    <a href="index.php">Volver a la página principal</a>
    <script src="../js/register.js"></script>
</body>
</html>