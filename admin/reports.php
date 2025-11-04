<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    header('Location: ../visual/index.php');
    exit();
}

$reports = [];
$sql = "SELECT r.*, u.userName as reporterName FROM reports r LEFT JOIN users u ON r.reporterId = u.userId ORDER BY r.created_at DESC";
if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $reports[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Admin - Reportes</title>
    <link href="../css/main.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-header">
        <h1>Reportes de usuarios</h1>
        <a href="index.php" class="admin-btn">Volver al panel</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Reportado por</th>
                <th>Tipo</th>
                <th>Target</th>
                <th>Propietario</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Creado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($reports as $r): ?>
            <tr>
                <td><?php echo intval($r['reportId']); ?></td>
                <td><?php echo htmlspecialchars($r['reporterName'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['targetType']); ?></td>
                <td>
                    <?php
                    $targetLink = '#';
                    $targetLabel = htmlspecialchars($r['targetId']);
                    if ($r['targetType'] === 'post') {
                        // fetch title
                        $t = null;
                        $sql2 = "SELECT title FROM post WHERE postId = ? LIMIT 1";
                        if ($st2 = mysqli_prepare($con, $sql2)) {
                            mysqli_stmt_bind_param($st2, 'i', $r['targetId']);
                            mysqli_stmt_execute($st2);
                            mysqli_stmt_bind_result($st2, $t);
                            mysqli_stmt_fetch($st2);
                            mysqli_stmt_close($st2);
                        }
                        $targetLabel = $t ? htmlspecialchars($t) : 'Publicación #' . intval($r['targetId']);
                        $targetLink = "../visual/viewRecipe.php?id=" . intval($r['targetId']);
                    } elseif ($r['targetType'] === 'user') {
                        $uName = null;
                        $sql2 = "SELECT userName FROM users WHERE userId = ? LIMIT 1";
                        if ($st2 = mysqli_prepare($con, $sql2)) {
                            mysqli_stmt_bind_param($st2, 'i', $r['targetId']);
                            mysqli_stmt_execute($st2);
                            mysqli_stmt_bind_result($st2, $uName);
                            mysqli_stmt_fetch($st2);
                            mysqli_stmt_close($st2);
                        }
                        $targetLabel = $uName ? '@' . htmlspecialchars($uName) : 'Usuario #' . intval($r['targetId']);
                        $targetLink = "../visual/account.php?username=" . urlencode($uName ?: '');
                    } elseif ($r['targetType'] === 'comment') {
                        $targetLabel = 'Comentario #' . intval($r['targetId']);
                        $targetLink = "../visual/commentThread.php?commentId=" . intval($r['targetId']);
                    }
                    ?>
                    <a href="<?php echo $targetLink; ?>" target="_blank"><?php echo $targetLabel; ?></a>
                </td>
                <td><?php echo htmlspecialchars($r['targetOwnerId']); ?></td>
                <td style="max-width:350px; white-space:pre-wrap;"><?php echo htmlspecialchars($r['reason']); ?></td>
                <td><?php echo htmlspecialchars($r['status']); ?></td>
                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                <td>
                    <a class="admin-btn" href="../visual/viewRecipe.php?id=<?php echo intval($r['targetId']); ?>" target="_blank">Ver</a>
                    <a class="admin-btn secondary" href="delete_report.php?id=<?php echo intval($r['reportId']); ?>" onclick="return confirm('Eliminar este reporte?')">Borrar reporte</a>
                    <?php if ($r['targetType'] === 'post'): ?>
                        <a class="admin-btn secondary" href="delete.php?type=post&id=<?php echo intval($r['targetId']); ?>" onclick="return confirm('Eliminar la publicación reportada?')">Eliminar post</a>
                    <?php elseif ($r['targetType'] === 'user'): ?>
                        <a class="admin-btn" href="edit_user.php?id=<?php echo intval($r['targetId']); ?>">Editar usuario</a>
                        <a class="admin-btn secondary" href="delete.php?type=user&id=<?php echo intval($r['targetId']); ?>" onclick="return confirm('Eliminar el usuario reportado?')">Eliminar usuario</a>
                    <?php elseif ($r['targetType'] === 'comment'): ?>
                        <a class="admin-btn secondary" href="delete_comment.php?id=<?php echo intval($r['targetId']); ?>" onclick="return confirm('Eliminar el comentario reportado?')">Eliminar comentario</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
