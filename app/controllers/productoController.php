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
            $atributos = $productoModel->getAtributos();
            $response = ["status" => "success", "data" => $atributos];
            break;
        case 'obtenerCategorias':
            $categorias = $categoriaModel->getCategorias();
            $response = ["status" => "success", "data" => $categorias];
            break;
        case 'registrarProductoCompleto':
            try {
                // Obtenemos la instancia de la conexión para la transacción
                $db = Conexion::conectar();
                $db->beginTransaction();

                // 1. CAPTURA DE DATOS BASE (Sin el wrapper $data)
                $nombreProducto = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
                $descripcion    = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : "";
                $id_categoria   = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : null;
                $estado         = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

                // 2. REGISTRO EN TABLA: producto (Llamada al modelo)
                $idProducto = $productoModel->registrarProducto($nombreProducto, $descripcion, $id_categoria, $estado);

                if (!$idProducto) throw new Exception("Error al registrar el producto base.");

                // 3. REGISTRO EN TABLA: productoatributo
                // Estos son los atributos que el usuario eligió en los selectores de arriba
                if (isset($_POST['atributo_id'])) {
                    foreach ($_POST['atributo_id'] as $id_at) {
                        $productoModel->registrarProductoAtributo($idProducto, (int)$id_at);
                    }
                }

                // 4. REGISTRO DE VARIANTES (Matriz dinámica)
                if (isset($_POST['v_descripcion'])) {
                    foreach ($_POST['v_descripcion'] as $i => $descAtributos) {

                        // Generamos el nombre descriptivo completo
                        $nombreCompleto = $nombreProducto . " - " . $descAtributos;
                        $precio = (float)$_POST['v_precio'][$i];
                        $stock  = (int)$_POST['v_stock'][$i];

                        // --- MANEJO DE IMÁGENES LOCALES ---
                        $nombreImagen = "default.png";

                        if (isset($_FILES['v_foto']['name'][$i]) && $_FILES['v_foto']['error'][$i] === UPLOAD_ERR_OK) {
                            // El punto "." une los textos correctamente
                            // Solo un "../" para salir de controllers y quedar en app/
                            $rutaDestino = __DIR__ . "/../views/assets/images/";
                            $ext = strtolower(pathinfo($_FILES['v_foto']['name'][$i], PATHINFO_EXTENSION));

                            // Nombre único para evitar colisiones
                            $nuevoNombre = "prod_" . $idProducto . "_v" . $i . "_" . time() . "." . $ext;

                            if (move_uploaded_file($_FILES['v_foto']['tmp_name'][$i], $rutaDestino . $nuevoNombre)) {
                                $nombreImagen = $nuevoNombre;
                            }
                        }

                        // 5. REGISTRO EN TABLA: variante
                        $idVariante = $productoModel->registrarVariante($idProducto, $nombreCompleto, $precio, $stock, $nombreImagen);

                        // 6. REGISTRO EN TABLA: variantevalor (Desglose atómico)
                        // Aquí rompemos la cadena "Color: Rojo / Talla: L" para cumplir tu ERD
                        $pares = explode(" / ", $descAtributos);
                        foreach ($pares as $par) {
                            $partes = explode(": ", $par);
                            if (count($partes) == 2) {
                                $idAttrBase = $productoModel->obtenerIdAtributoPorNombre(trim($partes[0]));
                                if ($idAttrBase) {
                                    $productoModel->registrarVarianteValor($idVariante, $idAttrBase, trim($partes[1]));
                                }
                            }
                        }
                    }
                }

                $db->commit();
                $response = ["status" => "success", "message" => "Producto y variantes registrados exitosamente"];
            } catch (Exception $e) {
                if (isset($db)) $db->rollBack(); // Si algo falla, se borra todo lo anterior
                error_log("Error en ProductoController: " . $e->getMessage());
                $response = ["status" => "error", "message" => $e->getMessage()];
            }
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
