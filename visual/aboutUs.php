<?php
$team = [
    [
        'img' => '../img/yea.jpg',
        'name' => 'Mateo',
        'desc' => 'Programador PHP, trabaja viernes sabados y domingos, fue el encargado de hacer las primeras versiones del buscador, hacer la seccion de comentarios, realizar la logica del perfil, conectar la base de datos, el sistema de inicio de sesion y registro, y esta misma pagina'
    ],
    [
        'img' => '../img/tlabaja.jpg',
        'name' => 'Cores',
        'desc' => 'Programador PHP, scrum master, se encarga de organizar a todos los trabajadores, se encarga de perfeccionar los trabajos a su gusto, segun como lo quiera, se encargo de hacer la version actual de lo que hizo mateo, ademas de agregarle el boton de retroceder, ver las recetas de cada uno y la cuenta de otro.'
    ],
    [
        'img' => '../img/agustina.jpg',
        'name' => 'Agustina',
        'desc' => 'Administradora de base de datos, que tambien hace css si es necesario, se encargo de hacer la base de datos y de mantenerla, traduciendola cuando era necesario, agregando cada parte, y tambien colaborando con el equipo haciendo css cuando ya la base de datos no necesitaba cambios.'
    ],
    [
        'img' => '../img/Thomas.webp',
        'name' => 'Thomas',
        'desc' => 'Encargado de visuales (CSS), trabaja bien, escucha musica mientras trabaja , le gustan los beatles, se encarga en su mayoria de arreglos en js e hizo visuales cuando se necesitaba.'
    ],
    [
        'img' => '../img/chopper.webp',
        'name' => 'Victoria',
        'desc' => 'Encargada de visuales (CSS), le gusta el anime, en especial one piece, le gusta mucho hacer los visuales por el toque artistico, fue la que controlo mayormente del diseño visual, eligiendo el estilo, la imagen del equipo, los colores, y toda la organizacion de la pagina.'
    ],
    [
        'img' => '../img/perroShh.png',
        'name' => 'Pietro',
        'desc' => 'Programador HTML, se encargo de hacer la estructura basica de la pagina, entre estos siendo el perfil, la pagina principal y el resto de cosas basicas, luego, hizo el css de las mismas, segun la organizacion de los visuales ya establecidos.'
    ],
    [
        'img' => '../img/martin.png',
        'name' => 'Martin',
        'desc' => 'Programador de html que tambien hace css para complementar su trabajo, hizo parte de la estructura basica, y actualmente va de trabajo en trabajo viendo en que puede ayudar, ya sea de html, css o php'
    ],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS principal -->
  <link rel="stylesheet" href="../css/main.css">
  <link rel="stylesheet" href="../css/styleM.css">

  <!-- Bootstrap JS y Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <link rel="icon" href="../img/favicon.ico" type="image/x-icon">

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/backButton.php'; ?>
    <header class="about-header">
        <h1>¡Sobre nosotros!</h1>
    </header>
    <main>
        <section class="team-section">
            <div class="team-cards">
                <?php foreach ($team as $member): ?>
                    <div class="card">
                        <img src="<?= htmlspecialchars($member['img']) ?>"
                            alt="Foto de <?= htmlspecialchars($member['name']) ?>">
                        <div class="card-body">
                            <h3><?= htmlspecialchars($member['name']) ?></h3>
                            <p><?= htmlspecialchars($member['desc']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="about-section">
            <div class="about-card">
                <h2>¿Por qué hacemos esto?</h2>
                <p>Porque la comida une. Porque detrás de cada receta hay una historia, una tradición o simplemente el deseo de compartir algo rico con los demás. Creamos esta página para abrir una mesa virtual donde todos puedan traer su plato, su experiencia, sus secretos de cocina y su forma única de preparar lo que ama.

                    No se trata solo de recetas, sino de comunidad. De aprender unos de otros, de inspirarnos, de equivocarnos y volver a intentar. Acá no importa si sos chef profesional o si estás haciendo tus primeros intentos en la cocina: lo que importa es tener ganas de compartir.

                    Hacemos esto porque creemos que cocinar no tiene que ser solitario, y que los mejores sabores surgen cuando se comparten. </p>
            </div>
            <div class="about-card">
                <h2>¿Quíenes somos?</h2>
                <p>Somos un grupo de entusiastas de la cocina que creemos en el poder de compartir recetas y experiencias culinarias. Cada uno de nosotros aporta su propia perspectiva y habilidades, y juntos formamos una comunidad diversa y creativa. Nos apasiona la comida y queremos inspirar a otros a explorar su amor por la cocina, por eso, en nuestra pagina nuestro principal objetivo es el buen trato entre los usuarios y la capacidad de mostrar tus recetas al resto, esperando por que la prueben y la critiquen, para ir mejorando constantemente.</p>
            </div>
        </section>
    </main>
</body>

</html>
</body>

</html>