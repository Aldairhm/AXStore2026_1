<?php
session_start();
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/autoload.php";

use app\controllers\viewsController;

/* ===============================================
   LÓGICA DE CIERRE DE SESIÓN CON ALERTA (SweetAlert2)
   =============================================== */
if (isset($_GET['opcion']) && $_GET['opcion'] == 'cerrar') {
    
    // 1. Destruimos la sesión del servidor
    session_destroy();
    
    // 2. Mostramos una vista "intermedia" solo para la alerta
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cerrando sesión...</title>
        <script src="<?php echo APP_URL; ?>app/views/assets/js/sweetalert2.all.min.js"></script>
        <style> body { font-family: sans-serif; background-color: #f3f4f6; } </style>
    </head>
    <body>
        <script>
            // Ejecutamos la alerta
            Swal.fire({
                title: 'Cerrando sesión',
                text: 'Esperamos verte pronto...',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000, 
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                
                window.location.href = "<?php echo APP_URL; ?>login";
            });
        </script>
    </body>
    </html>
    <?php
    exit(); 
}

if (isset($_GET['views'])) {
    $url = explode("/", $_GET['views']);
} else {
    $url = ["login"];
}

// ... Middleware de seguridad ...
$vistas_publicas = ['login', '404', 'recuperar', 'nueva_clave'];

if (!isset($_SESSION['usuario'])) {
    if (!in_array($url[0], $vistas_publicas)) {
        $url[0] = "login";
    }
} else {
    if ($url[0] == "login") {
        $url[0] = "home";
    }
}

$viewsController = new viewsController();
$vista = $viewsController->obtenerVistasControlador($url[0]);
require_once $vista;