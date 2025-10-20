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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Receta</title>
    <!--icono de la pagina  -->
    <link rel="icon" type="image/x-icon" href="img/gorromostacho 3 (1).png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
    <?php include '../nawbar.php'; ?>
    <?php include '../backButton.php'; ?>
    <form id="formPublish" enctype="multipart/form-data" method="post">
        <button type="submit" class="buttono">Publicar</button>
        <button type="button" class="buttonw" id="btnEliminar">Eliminar</button>

        <div class="publish-form">
            <div class="publish-info">
                <input type="text" name="title" id="recipeTitle" class="input" placeholder="Título de la receta" required>
                <input type="text" name="description" id="recipeDescription" class="input" placeholder="Cuentanos mas acerca de este plato" required>
            </div>



            <div class="publish-image">
                <div class="image-upload">
                    <input type="file" name="recipeImages[]" id="imageInput" class="hide" accept="image/*" multiple />
                    <label for="imageInput">Subir imágenes (máximo 3)</label>
                </div>
                <div id="imagePreview" class="image-preview"></div>
            </div>


            <div class="publish-ingredients">
                <label>Ingredientes:</label>
                <div id="ingredients-list">
                    <div class="input-container">
                        <div class="input-wrapper">
                            <input type="text" name="ingredientes[]" class="input-ingredient input" placeholder="Ingrediente 1" required>
                        </div>
                        <div class="button-wrapper">
                        </div>
                    </div>
                </div>
                <button type="button" id="addIngrediente" class="buttonw">Agregar ingrediente</button>

            </div>

            <div class="publish-steps">

                <label>Pasos de la receta:</label>
                <div id="steps-list">
                    <div class="input-container">
                        <div class="input-wrapper">
                            <input type="text" name="pasos[]" class="input-step input" placeholder="Paso 1" required>
                        </div>
                        <div class="button-wrapper">
                        </div>
                    </div>
                </div>
                <button type="button" id="addPaso" class="buttonw">Agregar paso</button>


            </div>
    </form>
    <div id="mensaje"></div>



    <script src="../js/publish.js"></script>

</body>

</html>