<?php
// Verificar si no estamos en la página principal
$currentPage = basename($_SERVER['PHP_SELF']);
if ($currentPage !== 'index.php'): 
?>
<button onclick="window.history.back()" class="back-button" title="Volver atrás">
    <i class="bi bi-arrow-left"></i>
</button>
<?php endif; ?>