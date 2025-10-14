<?php
session_start();
session_destroy();
header("Location: visual/index.php");
exit;
