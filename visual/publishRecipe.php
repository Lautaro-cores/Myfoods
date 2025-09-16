<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Receta</title>
</head>
<body>
    <h1>Publicar Receta</h1>
    <form id="formPublish">
        <fieldset>
            <input type="text" name="title" id="recipeTitle" placeholder="Título de la receta" required>
            <br><br>
            <input type="text" name="description" id="recipeDescription" placeholder="Descripción breve" required>
            <br><br>
            <button type="submit">Publicar</button>
        </fieldset>
    </form>
    <div id="mensaje"></div>
    <br>
    <a href="index.php">Volver al inicio</a>
    <a href="../account.php">Ir a tu perfil</a>

    <script src="../js/publish.js"></script>

</body>
</html>