<?php
$team = [
    [
        'img' => '../img/yea.jpg',
        'name' => 'Mateo',
        'desc' => 'Programador PHP de 17 años, vive en Almagro, Yapeyu 263.'
    ],
    [
        'img' => '../img/tlabaja.jpg',
        'name' => 'Cores',
        'desc' => 'Programador PHP, 16 años, vive en Lanús Oeste, Liniers 1940.'
    ],
    [
        'img' => '../img/agustina.jpg',
        'name' => 'Agustina',
        'desc' => 'Administradora de base de datos, 16 años, ubicación desconocida.'
    ],
    [
        'img' => '../img/Thomas.webp',
        'name' => 'Thomas',
        'desc' => 'Encargado de visuales (CSS), vive en Estados Unidos y Pichincha, 17 años.'
    ],
    [
        'img' => '../img/chopper.webp',
        'name' => 'Victoria',
        'desc' => 'Encargada de visuales (CSS), vive en Boedo e Yrigoyen, 17 años.'
    ],
    [
        'img' => '../img/perroShh.png',
        'name' => 'Pietro',
        'desc' => 'Programador HTML de 17 años que vive en su casa.'
    ],
    [
        'img' => '../img/martin.png',
        'name' => 'Martin',
        'desc' => 'Martin es Martin.'
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
                        <img src="<?= htmlspecialchars($member['img']) ?>" alt="Foto de <?= htmlspecialchars($member['name']) ?>">
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
                <p>Queremos darle a las personas un medio para compartir sus recetas con el mundo y obtener otras de diferentes personas, creando un ambiente saludable y colaborativo.</p>
            </div>
            <div class="about-card">
                <h2>¿Cómo lo hacemos?</h2>
                <p>Esta página está programada mayormente en PHP, con funciones principales divididas entre otros archivos PHP y JavaScript. Usamos Apache como servidor y la base de datos está creada en MySQL.</p>
            </div>
        </section>
    </main>
</body>
</html>
</body>
</html>