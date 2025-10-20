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

<body class="body-login">

    <div><img src="../img/logo.png" alt="Myfoods Logo" class="logo"></div>

    <h2>Registrate en Myfoods</h2>
    <br>


    <form id="formRegister">
        <div class="login-form">
            <p>Nombre de usuario</p>
            <input type="text" name="userName" id="regUserName" class="input" placeholder="Nombre de usuario" required
                autocomplete="username">
            <br>

            <p>Email</p>
            <input type="email" name="userEmail" id="regUserEmail" class="input" placeholder="Correo electrónico"
                required autocomplete="email">
            <br>

            <p>Contraseña</p>
            <input type="password" name="userPassword" id="regUserPassword" class="input" placeholder="Contraseña"
                required minlength="5" autocomplete="new-password">
            <br>

            
            <button type="submit" id="Registrar" class="buttonw">Crear cuenta</button>
        </div>
    </form>

    <div id="mensaje"></div>
    <br>

    <div class="login-register">
        <p>¿Ya tienes una cuenta? <a href="logIn.php">Inicia sesión aquí</a></p>
    </div>
    <br>

    <p>Al usar MyFoods, aceptas las <a href="">Condiciones de servicio</a> y la<a href=""> Politica de privacidad</a> de
        MyFoods</p>

    <script src="../js/register.js"></script>
</body>

</html>