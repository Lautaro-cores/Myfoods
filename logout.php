<?php
session_start();
session_destroy();
header("Location: visual/logIn.php");
exit;
?>