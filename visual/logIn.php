<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
</head>

<body>
    <h2>Iniciar Sesión</h2>
    <form id="formLogin">
        <input type="text" name="userName" id="loginUserName" placeholder="Nombre de usuario" required autocomplete="username">
        <input type="password" name="userPassword" id="loginUserPassword" placeholder="Contraseña" required autocomplete="current-password">
        <button type="submit" id="Login">Ingresar</button>
    </form>
    <div id="mensaje"></div>
    <br>
    <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    <br>
    <script src="../js/logIn.js"></script>
</body>

</html>