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

/* ===============================================
   CAPTURA DE VISTAS
   =============================================== */
if (isset($_GET['views'])) {
    $url = explode("/", $_GET['views']);
} else {
    $url = ["login"];
}

/* ===============================================
   MIDDLEWARE DE SEGURIDAD Y ROLES
   =============================================== */
$vistas_publicas = ['login', '404', 'recuperar', 'nueva_clave'];

// 1. Validar si el usuario NO ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    if (!in_array($url[0], $vistas_publicas)) {
        $url[0] = "login";
    }
} 
// 2. Validar si el usuario SÍ ha iniciado sesión
else {
    // A) Si intenta ir a login estando logueado, mandar a home
    if ($url[0] == "login") {
        $url[0] = "home";
    }

    // B) VALIDACIÓN ESPECÍFICA PARA VENDEDOR
    // Aquí verificamos el rol y restringimos el acceso
    if (isset($_SESSION['usuario']['rol']) && $_SESSION['usuario']['rol'] == 'vendedor') {
        
        // Definimos las ÚNICAS vistas que el vendedor puede ver.
        // Si intenta entrar a 'salidas', 'productos' o 'usuarios', será rechazado
        // porque no están en esta lista.
        $vistas_permitidas_vendedor = ['home', '404'];

        if (!in_array($url[0], $vistas_permitidas_vendedor)) {
            // Si la vista solicitada no está permitida, lo forzamos a ir al home
            $url[0] = "home";
        }
    }
}

/* ===============================================
   CARGA DEL CONTROLADOR
   =============================================== */
$viewsController = new viewsController();
$vista = $viewsController->obtenerVistasControlador($url[0]);
require_once $vista;