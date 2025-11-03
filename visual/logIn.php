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
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body class="body-login">

    <div><img src="../img/logo.png" alt="Myfoods Logo" class="logo"></div>

    <h2>Iniciar sesion en Myfoods</h2>
    <br>

        <form id="formLogin">
            <div class="login-form">
            <p>Nombre de usuario o email</p>
            <input type="text" name="userName" id="loginUserName" class="input" placeholder="Nombre de usuario" required
                autocomplete="username" maxlength="20">

            <br>

            <p>Contraseña</p>
            <input type="password" name="userPassword" id="loginUserPassword" class="input" placeholder="Contraseña"
                required autocomplete="current-password" maxlength="15">

            <br>

            <button type="submit" id="button" class="buttonw">Iniciar Sesión</button>
            </div>
        </form>
    

    <div id="mensaje"></div>
    <br>

    <div class="login-register">
        <p>¿Sos nuevo en MyFoods? <a href="register.php">Crea una cuenta</a></p>
    </div>
    <br>

    <p>Al usar MyFoods, aceptas las <a href="">Condiciones de servicio</a> y la<a href=""> Politica de privacidad</a> de
        MyFoods</p>

    <script src="../js/logIn.js"></script>
</body>

</html>