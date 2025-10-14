<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/styleAG.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
</head>

<body>

    <div><img src="../img/logo.png" alt="Myfoods Logo" id="logo"></div>
    
    <h2>Registrate en Myfoods</h2>
    <br>
    
    <div>
        <form id="formRegister">
            
            <p>Email</p>
            <input type="email" name="userEmail" id="regUserEmail" class="name" placeholder="Correo electrónico" required autocomplete="email">
            <br>
            
            <p>Contraseña</p>
            <input type="password" name="userPassword" id="regUserPassword" class="name" placeholder="Contraseña" required minlength="5" autocomplete="new-password">
            <br>
            
            <p>Nombre de usuario</p>
            <input type="text" name="userName" id="regUserName" class="name" placeholder="Nombre de usuario" required autocomplete="username">
            <br>
            <input type="submit" id="Registrar" value="Crear cuenta" class="styled-button-register">
            </form>
    </div>

    

    <div id="mensaje"></div>
    <br>
    <p>¿Ya tienes una cuenta? <a href="logIn.php">Inicia sesión aquí</a></p>
    <br>

    <script src="../js/register.js"></script>
</body>

</html>