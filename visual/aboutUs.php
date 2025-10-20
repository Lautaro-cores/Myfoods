<?php
$team = [
    [
        'img' => '../img/yea.jpg',
        'name' => 'Mateo',
        'desc' => 'Programador PHP, trabaja viernes sabados y domingos, la semana la utiliza para tareas de teoria. juega muchos videojuegos.'
    ],
    [
        'img' => '../img/tlabaja.jpg',
        'name' => 'Cores',
        'desc' => 'Programador PHP, skrum master, se encarga de organizar a todos los trabajadores. duerme poco.'
    ],
    [
        'img' => '../img/agustina.jpg',
        'name' => 'Agustina',
        'desc' => 'Administradora de base de datos, que tambien hace css si es necesario, se complico mucho con la base de datos y la cambio varias veces.'
    ],
    [
        'img' => '../img/Thomas.webp',
        'name' => 'Thomas',
        'desc' => 'Encargado de visuales (CSS), trabaja bien, escucha musica mientras trabaja , tiene anteojos, es el mayor del equipo.'
    ],
    [
        'img' => '../img/chopper.webp',
        'name' => 'Victoria',
        'desc' => 'Encargada de visuales (CSS), le gusta el anime, en especial one piece, le gusta mucho hacer los visuales por el toque artistico.'
    ],
    [
        'img' => '../img/perroShh.png',
        'name' => 'Pietro',
        'desc' => 'Programador HTML, juega videojuegos, habla con mujeres, es carismatico y soltero, llamar al +54 911 2285-3094 para mas informacion.'
    ],
    [
        'img' => '../img/martin.png',
        'name' => 'Martin',
        'desc' => 'Programador de html que tambien hace css para complementar su trabajo, escucha musica chilena, le cuesta concentrarse'
    ],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleM.css">
    <title>Myfoods - Sobre Nosotros</title>
</head>

<body>
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
                <p>Queremos darle a las personas un medio para compartir sus recetas con el mundo y obtener otras de
                    diferentes personas, creando un ambiente saludable y colaborativo. En nuestra pagina, queremos darle
                    libertad a la gente de poder subir la receta que quieran, con los ingredientes que quieran, y que el
                    resto de usuarios opine y la critique, tanto para bien, como para mal. </p>
            </div>
            <div class="about-card">
                <h2>¿Cómo lo hacemos?</h2>
                <p>Esta página está programada mayormente en PHP, con funciones principales divididas entre otros
                    archivos PHP y JavaScript. Usamos Apache como servidor y la base de datos está creada en MySQL. Nos
                    organizamos mayormente en 3 divisiones, los 2 de php, que son Cores y Medina, los css, que son
                    Victoria y Thomas, y el html que son Pietro y Martin, de esta forma, nos organizamos para trabajar
                    en diferentes dias sobre diferentes cosas, para despues organizarnos y subir todo</p>
            </div>
        </section>
    </main>
</body>

</html>
</body>

</html>