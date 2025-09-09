<?php
session_start();

if (isset($_SESSION['userLogged'])) {
    header("Location:/myfoods/visual/publishRecipe.html");
    exit();
} else {
    header("Location:/myfoods/visual/register.html");
    exit();
}
?>