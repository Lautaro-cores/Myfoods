<?php
session_start();

if (isset($_SESSION['userLogged'])) {
    header("Location:/myfoods/publishRecipe.html");
    exit();
} else {
    header("Location:/myfoods/register.html");
    exit();
}
?>