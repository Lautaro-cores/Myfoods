<?php
session_start();

if (isset($_SESSION['usuario_logueado'])) {
    header("Location: publicar_receta.html");
    exit();
} else {
    header("Location: registrarse.html");
    exit();
}
?>