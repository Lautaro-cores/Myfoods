<?php
session_start();
require_once "../includes/config.php";

if (!isset($_SESSION['userId'])) {
  header('Location: ../visual/logIn.php');
  exit();
}

if (!isset($_GET['username']) || empty($_GET['username'])) {
  die("Error: no se especificó el nombre de usuario en la URL.");
}

$username = $_GET['username'];

$sql = "SELECT userName, userEmail, userImage, description FROM users WHERE userName = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);


$userName = $user['userName'];
$userEmail = $user['userEmail'];
$userDescription = $user['description'];

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
  <?php include '../backButton.php'; ?>

  <div class="profile-info">
    <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Imagen de perfil" class="profile-image">

    <div class="profile-details">
      <h3><?php echo htmlspecialchars($userName); ?></h3>
      <p class="user-description">
  <?php echo !empty($userDescription) 
    ? htmlspecialchars($userDescription) 
    : '<em>Sin descripción</em>'; ?>
</p>
    </div>
  </div>


  <?php if ($_SESSION['userName'] === $userName): ?>
  <div class="edit-profile">
    <button id="editProfile" class="buttonw" data-bs-toggle="modal" data-bs-target="#editProfileModal">Editar perfil</button>
  </div>
  <?php endif; ?>

  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formImage" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="description" class="form-label">Descripción</label>
              <textarea id="description" name="description" class="form-control" rows="3" placeholder="Escribe una breve descripción..."><?php echo htmlspecialchars($userDescription); ?></textarea>
            </div>
            <div class="form-group mb-3">
              <label for="subirArchivo" class="form-label">Seleccionar nueva foto de perfil (opcional):</label>
              <input type="file" id="subirArchivo" name="userImage" accept="image/*" class="form-control">
              <!-- Preview container -->
              <div id="imagePreview" class="m-3"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="buttono" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="buttono">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="user-recipes">
    <h2>Recetas de <?php echo htmlspecialchars($userName); ?></h2>
    <div id="userPosts"></div>
  </div>

  <script src="../js/account.js"></script>
</body>

</html>