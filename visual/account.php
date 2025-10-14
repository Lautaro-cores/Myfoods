<?php
session_start();
require_once __DIR__ . "/../includes/config.php";

if (!isset($_SESSION['userId'])) {
    header('Location: logIn.php');
    exit();
}

$userId = intval($_SESSION['userId']);

$sql = "SELECT userName, userEmail, userImage FROM users WHERE userId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
$userName = $user['userName'] ?? '';
$userEmail = $user['userEmail'] ?? '';

$userImage = '';
if (empty($user['userImage'])) {
    $userImage = '../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
} else {
    $userImage = 'data:image/jpeg;base64,' . base64_encode($user['userImage']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    <style>
      /* Layout base */
      body { padding-left: 80px !important; background: #FDE7E4; }

      /* Sidebar (left narrow icons) */
      .sidebar-custom {
        position: fixed;
        left: 10px;
        top: 20px;
        bottom: 20px;
        width: 60px;
        background: #f9a89b;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        padding: 16px 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
      }
      .sidebar-custom .sb-item, .sidebar-custom .sb-logo { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; color:#000; text-decoration:none; }

      /* Main banner / profile area */
      main.container { max-width: 920px; margin-left: 120px; }
      .perfil-container img { width:120px; height:120px; object-fit:cover; border-radius:50%; background:#fff; display:block; }
      .perfil-container { align-items:center; }

      /* Big edit button full-width */
      form#formImage { display:flex; align-items:center; gap:12px; }
      form#formImage label.boton-personalizado {
        flex: 1 1 auto; text-align:center; background:#fff4f3; padding:14px 16px; border-radius:18px; border:1px solid rgba(0,0,0,0.25); cursor:pointer; font-size:20px;
      }
      </style>
    
      <!DOCTYPE html>
      <html lang="es">

      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Perfil de Usuario</title>
          <!-- Bootstrap CSS -->
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
          <!-- CSS principal -->
          <link rel="stylesheet" href="../css/main.css">
          <!-- Bootstrap JS y Popper -->
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
      </head>

      <body class="accountP">
          <nav id="myfoods-sidebar" class="d-flex flex-column p-3 bg-light border-end" style="width: 250px; height: 100vh; position: fixed; top:0; left:0; z-index: 1040; overflow-y:auto;">
        <a href="/visual/index.php" class="d-flex align-items-center mb-3 text-dark text-decoration-none">
          <span class="fs-4 fw-bold">MyFoods</span>
        </a>

        <hr>

        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item">
            <a href="/visual/index.php" class="nav-link">Inicio</a>
          </li>
          <li class="nav-item">
            <a href="/publishRecipe.php" class="nav-link">Publicar receta</a>
          </li>
          <li class="nav-item">
            <a href="/perfil.php" class="nav-link">Perfil</a>
          </li>
          <li class="nav-item">
            <a href="/favoritos.php" class="nav-link">Favoritos</a>
          </li>
        </ul>

        <hr>
        <div class="mt-auto">
          <a href="/logout.php" class="btn btn-outline-secondary w-100">Cerrar sesión</a>
        </div>
  
      </nav>
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-sign-turn-left" viewBox="0 0 30 30">
        <path d="M11 8.5A2.5 2.5 0 0 0 8.5 6H7V4.534a.25.25 0 0 0-.41-.192L4.23 6.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 7 8.466V7h1.5A1.5 1.5 0 0 1 10 8.5V11h1z"/>
        <path fill-rule="evenodd" d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.48 1.48 0 0 1 0-2.098zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134Z"/>
      </svg>
        <h2 class="h2P">Recetas(0)</h2>

      <div class="perfil-container">
        <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Imagen de perfil">
        <div class="info-container">
          <p class="pP"><?php echo htmlspecialchars($userName); ?></p>
          <p class="pP"><?php echo htmlspecialchars($userEmail); ?></p>
        </div>
      </div>

      <div class="botones-container">
        <form id="formImage" enctype="multipart/form-data">
          <input class="inputlP" type="file" name="userImage" id="subirArchivo" class="input-oculto">
          <label for="subirArchivo" class="boton-personalizado">Editar Perfil</label>
        </form>

        <button class="buttonP" type="submit">Seguir</button>
      </div>

          </form>
          <style>
        /* Important: usa padding-left en body o en un .content wrapper */
        body { padding-left: 250px !important; }

        /* Para pantallas pequeñas convertir el sidebar en un menú superior ocultable */
        @media (max-width: 768px) {
          #myfoods-sidebar { position: relative; width: 100%; height: auto; padding-bottom: 0; }
          body { padding-left: 0 !important; }
        }
      </style>


          <script src="../js/account.js"></script>
      </body>

      </html>