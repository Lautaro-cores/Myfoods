<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: logIn.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Receta</title>
    <!--icono de la pagina  -->
    <link rel="icon" type="image/x-icon" href="img/gorromostacho 3 (1).png"> 
    <link rel="stylesheet" href="../css/stylesV.css">
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
<form id="formPublish" enctype="multipart/form-data" method="post" action="../publishRecipe.php">
    <fieldset id="divGrande">
        <div id="recipe-form-grid">

            <div id="image-area">
                <div id="imagenRece">
                    <div class="file-upload-">
                        <input type="file" name="image" id="imageInput" class="imageInput, imageInput2" accept="image/*" />
                    </div>
                </div>
            </div>

            <div id="header-area">
                <div id="botones">
                    <button type="submit" class="publicar">Publicar</button>
                    <button type="button" class="eliminar" id="btnEliminar">Eliminar</button>
                </div>
                <div id="campos">
                    <input type="text" name="title" id="recipeTitle" placeholder="Título de la receta" required>
                    <input type="text" name="description" id="recipeDescription" placeholder="Cuentanos mas acerca de este plato" required>
                </div>
            </div>

            <div id="ingredients-area">
                <div id="ingredientesSection">
                    <label>Ingredientes:</label>
                    <div id="ingredientesList">
                        <input type="text" name="ingredientes[]" class="ingredienteInput" placeholder="Ingrediente 1" required>
                    </div>
                    <button type="button" id="addIngrediente">Agregar ingrediente</button>
                </div>
            </div>

            <div id="steps-area">
                <div id="pasosSection">
                    <label>Pasos de la receta:</label>
                    <div id="pasosList">
                        <input type="text" name="pasos[]" class="pasoInput" placeholder="Paso 1" required>
                    </div>
                    <button type="button" id="addPaso">Agregar paso</button>
                </div>
            </div>

        </div> </fieldset>
</form>
    <div id="mensaje"></div>
    <br>
    <a href="index.php">Volver al inicio</a>


    <script src="../js/publish.js"></script>

</body>

</html>