<?php
// Verificamos si existe la sesión y el rol
$rol = isset($_SESSION['usuario']['rol']) ? $_SESSION['usuario']['rol'] : '';

if ($rol == "vendedor") {
    // Si es vendedor, cargamos el diseño nuevo "aparte"
    include __DIR__ . "/header_vendedor.php";
} else {
    // Si es admin (o cualquier otro), cargamos el diseño original completo
    // Asegúrate de haber renombrado tu header original a 'header_admin.php'
    include __DIR__ . "/header_admin.php";
}
?>