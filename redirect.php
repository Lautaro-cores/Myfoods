<?php
session_start();

if (isset($_SESSION['userLogged'])) {
    header("Location: visual/publishRecipe.php");
    exit();
} else {
    header("Location: visual/register.php");
    exit();
}
?>