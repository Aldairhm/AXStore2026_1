<?php

    header('Content-Type: application/json; charset=utf-8');
    header('Pragma: no-cache');
    header('Cache-Control: no-store, no-cache, must-revalidate');

    require_once __DIR__ . "/../models/categoriaModel.php";

    $categoriaModel = new Categoria();

    $opcion = isset($_POST["accion"]) ? trim($_POST["accion"]) : null;

    $response = ["status" => "error", "message" => "Opción inválida"];

    try {
        switch ($opcion) {
            case "registrarCategoria":
                $nombre          = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
                $descripcion     = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : null;

                $id = $categoriaModel->registrar($nombre, $descripcion);

                if ($id) {
                    $response = ["status" => "success", "message" => "Categoria Registrada Exitosamente"];
                } else {
                    $response = ["status" => "error", "message" => "No se pudo crear la categoria"];
                }
                break;
            
            default:
                $response = ["status" => "error", "message" => "Opción inválida"];
                break;
        }
    } catch (Throwable $e) {
        error_log("Error en categoriaController: " . $e->getMessage());
        $response = ["status" => "error", "message" => "Ocurrió un error en el sistema"];
    }

    echo json_encode($response);
