<?php
// logout.php
// este archivo cierra la sesión del usuario

session_start();

// destruye la sesión
session_destroy();
header("Location: visual/index.php");
exit;
