<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exposuiza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/exposuiza.css">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
    <!-- title is an image in the page; don't include it as a stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/backButton.php'; ?>

    <div class="exposuiza-page">
        <main class="content-area container-fluid">
            <div class="hero text-center">
                <img src="../img/exposuiza(titulo).png" alt="EXPOSUIZA" class="expo-title-image">
                <p class="expo-desc">La exposuiza es un evento el cual se realiza cada año en la E.T.N 26° con el fin de que los estudiantes de cursos superiores muestren el proyecto. Aquí podrás jugar, dejar tu marca en el graffiti y conocer al equipo detrás del proyecto.</p>
            </div>

            <div class="cards-row d-flex justify-content-center">
                <a href="trivia.php" class="card-link me-3" aria-label="Ir a Trivia">
                  <div class="expo-card">
                      <div class="card-image" style="background-image:url('../img/istockphoto-1303554344-612x612.jpg')"></div>
                      <div class="card-body">
                          <h3>¡Juega ahora!</h3>
                          <p>Pongamos a prueba tu conocimiento CULinario</p>
                      </div>
                  </div>
                </a>

                <div class="expo-card me-3">
                    <div class="card-image" style="background-image:url('../img/istockphoto-1303554344-612x612.jpg')"></div>
                    <div class="card-body">
                        <h3>Crear mi graffiti</h3>
                        <p>Deja tu marca en nuestra pared digital</p>
                    </div>
                </div>

                <div class="expo-card">
                    <div class="card-image" style="background-image:url('../img/istockphoto-1303554344-612x612.jpg')"></div>
                    <div class="card-body">
                        <h3>Conoce al equipo</h3>
                        <p>Conoce al equipo detrás de este proyecto</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>