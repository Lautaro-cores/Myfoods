<?php
session_start();

if (isset($_SESSION['usuario_logueado'])) {
    header("Location:/myfoods/visual/publicar_receta.html");
    exit();
} else {
    header("Location:/myfoods/visual/registrarse.html");
    exit();
}
?>