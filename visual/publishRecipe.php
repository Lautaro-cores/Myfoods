<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Receta</title>
    <link rel="icon" type="image/x-icon" href="img/gorromostacho 3 (1).png"> <!--icono de la pagina  -->
    <link rel="stylesheet" href="../css/stylesV.css"> 
</head>
<body>
    <div id="botones">
            <button type="submit" class="publicar">Publicar</button>
            <button type="submit" class="eliminar">eliminar</button>
    <form id="formPublish">
      <fieldset id="divGrande">
        <div id="imagenRece">
           <input type="file" class="imageInput" accept="image/*"/> </div>

        <div id="campos">
            <input type="text" name="title" id="recipeTitle" placeholder="Título de la receta" required>
            <input type="text" name="description" id="recipeDescription" placeholder="Cuentanos mas acerca de este plato" required>
        </div>
    </div>

        </fieldset>
    </form>
    <div id="mensaje"></div>
    <br>
    <a href="index.php">Volver al inicio</a>
    <a href="../account.php">Ir a tu perfil</a>

    <script src="../js/publish.js"></script>

</body>
</html>