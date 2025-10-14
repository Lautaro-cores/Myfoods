<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/styleA.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    
    <div><img src="../img/logo.png" alt="Myfoods Logo" id="logo"></div>
    
    <h2>Iniciar sesion en Myfoods</h2>
    <br>
    
    <div>
        <form id="formLogin"> 
            <p>Nombre de usuario o email</p>
            <input type="text" name="userName" id="loginUserName" class="name" placeholder="Nombre de usuario" required autocomplete="username">
            
            <br>
            
            <p>Contraseña</p>
            <input type="password" name="userPassword" id="loginUserPassword" class="name" placeholder="Contraseña" required autocomplete="current-password">
            
            <br>
            
            <input type="submit" id="button" value="Iniciar Sesión"> 
        </form>
    </div>
    
    <div id="mensaje"></div>
    <br>
    
    <div id="crearcuenta">
        <p>¿Sos nuevo en MyFoods? <a href="register.php">Crea una cuenta</a></p>
    </div>
    <br>
    
    <p>Al usar MyFoods, aceptas las <a href="">Condiciones de servicio</a> y la<a href=""> Politica de privacidad</a> de MyFoods</p>
    
    <script src="../js/logIn.js"></script> 
</body>
</html>