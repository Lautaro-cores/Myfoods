<?php
require_once "includes/config.php";

if (!isset($_GET["id"])) {
    echo "Receta no encontrada.";
    exit;
}

$postId = intval($_GET["id"]);

$sql = "SELECT title, description FROM post WHERE postId = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $description);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($title) {
    echo "<h1>" . htmlspecialchars($title) . "</h1>";
    echo "<p>" . nl2br(htmlspecialchars($description)) . "</p>";

} else {
    echo "Receta no encontrada.";
}
?>
