<?php

    require_once "./config/app.php";
    require_once "./autoload.php";
    use app\controllers\viewsController;

    // 1. Detectar la vista solicitada
    if(isset($_GET['views'])){
        $url = explode("/", $_GET['views']);
    } else {
        $url = ["home"];
    }
    
    // 2. Instanciar el controlador de vistas
    $viewsController = new viewsController();
    $vista = $viewsController->obtenerVistasControlador($url[0]);
    
    /* COMENTAMOS LA VALIDACIÓN POR AHORA
       Para que el sistema no te mande al login automáticamente
    */
    /*
    if(empty($_SESSION['usuario'])){
        if($vista != "app/views/content/404.php"){
            $vista = "app/views/content/login.php";
        }
    } else {
        if($vista == "app/views/content/login.php"){
            $vista = "app/views/content/cerrar.php";
        }
    }
    */

    // 3. Cargar la vista directamente
    require_once $vista;