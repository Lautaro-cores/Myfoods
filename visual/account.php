<?php
session_start();
require_once "../includes/config.php";

if (!isset($_SESSION['userId'])) {
    header('Location: ../visual/logIn.php');
    exit();
}

$userId = intval($_SESSION['userId']);

$sql = "SELECT userName, userEmail, userImage FROM users WHERE userId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
$userName = $user['userName'];
$userEmail = $user['userEmail'];

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
    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

      <body class="accountP">
<?php include '../nawbar.php'; ?>
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

    <h2>Mi Perfil</h2>
    <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Imagen de perfil" style="width:150px; height:150px; border-radius:50%;">
    <p>Nombre de usuario: <?php echo htmlspecialchars($userName); ?></p>
    <p>Correo electrónico: <?php echo htmlspecialchars($userEmail); ?></p>
    <br>
  <form id="formImage" enctype="multipart/form-data" method="post" action="../uploadImage.php">
    <input type="file" name="userImage" id="subirArchivo" class="input-oculto">

    <label for="subirArchivo" class="boton-personalizado">
      Seleccionar archivo
    </label>

    <button type="submit">Subir Imagen</button>
  </form>
  <div id="uploadMessage" role="alert" style="margin-top:8px"></div>
    <a href="index.php">Volver a la página principal</a>
    <a href="../logout.php">Cerrar sesión</a>

    <script>
      // Intercept form submit and send via fetch to uploadImage.php
      document.addEventListener('DOMContentLoaded', function(){
        const form = document.getElementById('formImage');
        const fileInput = document.getElementById('subirArchivo');
        const msg = document.getElementById('uploadMessage');
        const profileImg = document.querySelector('img[alt="Imagen de perfil"]');

        form.addEventListener('submit', function(e){
          e.preventDefault();
          msg.textContent = '';
          if (!fileInput.files || fileInput.files.length === 0) {
            msg.style.color = 'red'; msg.textContent = 'Selecciona un archivo primero.'; return;
          }
          const fd = new FormData();
          fd.append('userImage', fileInput.files[0]);

          fetch('../uploadImage.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                msg.style.color = 'green';
                msg.textContent = data.msj || 'Imagen actualizada';
                if (data.imageUrl) {
                  profileImg.src = data.imageUrl;
                } else {
                  // fallback: reload page
                  location.reload();
                }
              } else {
                msg.style.color = 'red';
                msg.textContent = data.msj || 'Error al subir imagen';
              }
            })
            .catch(err => {
              console.error('Error al subir imagen:', err);
              msg.style.color = 'red';
              msg.textContent = 'Error de red al subir imagen';
            });
        });
      });
    </script>
</body>

</html>