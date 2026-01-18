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
        case "listar":
            $datos = $categoriaModel->getCategorias();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = htmlspecialchars($row["nombre"]);
                $sub_array[] = htmlspecialchars($row["descripcion"] ?? "");
                //tomamos el id para las acciones
                $id = $row["id"];
                $sub_array[] = '
                    <div class="dropdown">
                        <button class="btn btn-sm shadow-none border-0 p-0 btn-kebab-luxury" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical" style="font-size: 1.3rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <a class="dropdown-item py-2" onclick="editar(' . $id . ')">
                                    <i class="bi bi-pencil-fill me-2 text-warning"></i> Editar Categoría
                                </a>
                            </li>
                        </ul>
                    </div>';

                $data[] = $sub_array;
            }
            $response = ["status" => "success", "data" => $data];
            break;

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

        case "obtenerCategoria":
            $id = isset($_POST["id"]) ? (int)($_POST["id"]) : 0;
            $data = $categoriaModel->buscarCategoria($id);

            if ($data) {
                $response = ["status" => "success", "data" => $data];
            } else {
                $response = ["status" => "error", "message" => "Error en servidor"];
            }
            break;

        case "editarCategoria":
            $id = isset($_POST["id_categoria"]) ? (int)$_POST["id_categoria"] : 0;
            $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
            $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : null;

            if ($id > 0 && !empty($nombre)) {
                $edit = $categoriaModel->actualizarCategoria($id, $nombre, $descripcion);

                // Si edit es >= 0, significa que la consulta se ejecutó bien.
                // (1 si cambió algo, 0 si los datos eran iguales)
                if ($edit >= 0) {
                    $response = [
                        "status" => "success",
                        "message" => "Categoría actualizada correctamente"
                    ];
                } else {
                    $response = [
                        "status" => "error",
                        "message" => "No se pudo realizar la actualización"
                    ];
                }
            } else {
                $response = ["status" => "error", "message" => "Datos incompletos"];
            }
            // Asegúrate de tener el echo json_encode($response) al final del switch
            break;

        default:
            $response = ["status" => "error", "message" => "Opción inválida"];
            break;
    }
} catch (Throwable $e) {
    error_log("Error en categoriaController: " . $e->getMessage());
    $response = ["status" => "error", "message" => "Ocurrió un error en el sistema"];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
