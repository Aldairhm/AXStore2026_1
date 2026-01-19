<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . "/../models/productoModel.php";
require_once __DIR__ . "/../models/categoriaModel.php";

$productoModel = new Producto();
$categoriaModel = new Categoria();

$opcion = isset($_POST["accion"]) ? trim($_POST["accion"]) : null;

$response = ["status" => "error", "message" => "Opción inválida"];

try {
    switch ($opcion) {
        case 'crearAtributo':
            $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;

            $id = $productoModel->registrarAtributo($nombre);
            $response = $id ? ["status" => "success", "message" => "Atributo creado exitosamente", "id" => $id] : ["status" => "error", "message" => "Error al crear el atributo"];
            break;
        case 'obtenerAtributos':
            $atributos= $productoModel->getAtributos();
            $response = ["status" => "success", "data" => $atributos];
            break;
        case 'obtenerCategorias':
            $categorias = $categoriaModel->getCategorias();
            $response = ["status" => "success", "data" => $categorias];
            break;
        default:
            $response = ["status" => "error", "message" => "Opción inválida"];
            break;
    }
} catch (Throwable $e) {
    error_log("Error en productoController: " . $e->getMessage());
    $response = ["status" => "error", "message" => "Ocurrió un error en el sistema"];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
