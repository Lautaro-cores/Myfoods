<?php
session_start();

if (isset($_SESSION['user_logged'])) {
    header("Location:/myfoods/visual/publish_recipe.html");
    exit();
} else {
    header("Location:/myfoods/visual/register.html");
    exit();
}
?>