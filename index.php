<?php

// Rutas absolutas basadas en el directorio actual
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/autoload.php";

use app\controllers\viewsController;

// Obtener la vista desde la URL: index.php?views=algo
if (isset($_GET['views'])) {
    $url = explode("/", $_GET['views']);
} else {
    // Vista por defecto
    $url = ["login"];
}

$viewsController = new viewsController();
$vista = $viewsController->obtenerVistasControlador($url[0]);

// Cargar la vista resultante
require_once $vista;