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
        case 'variantes':
            $id_producto = isset($_POST["id"]) ? (int)$_POST["id"] : null;
            $datos = $productoModel->getVariantesPorProducto($id_producto);
            $response = ["status" => "success", "data" => $datos];
            break;
        case 'obtener_uno':
            $id_producto = isset($_POST["id"]) ? (int)$_POST["id"] : null;
            $dato = $productoModel->getProductoPorId($id_producto);
            if ($dato) {
                $response = ["status" => "success", "data" => $dato];
            } else {
                $response = ["status" => "error", "message" => "Producto no encontrado"];
            }
            break;
        case 'editarProducto':
            $id_producto = isset($_POST["id_producto"]) ? (int)$_POST["id_producto"] : null;
            $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
            $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";
            $id_categoria = isset($_POST["id_categoria"]) ? (int)$_POST["id_categoria"] : null;
            $estado = isset($_POST["estado"]) ? (int)$_POST["estado"] : 1;

            if( $productoModel->isExisteProducto(strtolower($nombre), $id_producto)) {
                $response = ["status" => "error", "message" => "El nombre del producto ya existe"];
                break;
            }

            $resultado = $productoModel->actualizarProducto($id_producto, $nombre, $descripcion, $id_categoria, $estado);
            if ($resultado) {
                $response = ["status" => "success", "message" => "Producto actualizado exitosamente"];
            } else {
                $response = ["status" => "error", "message" => "Error al actualizar el producto"];
            }
            break;
        case 'cargarProductos':
            $datos = $productoModel->getProductosFull(); // Asegúrate de traer el nombre de la categoría con un JOIN
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = htmlspecialchars($row["nombre"]);
                $sub_array[] = '<span class="badge bg-light text-dark border">' . htmlspecialchars($row["categoria"] ?? "S/C") . '</span>';
                $sub_array[] = htmlspecialchars($row["descripcion"]);
                // Estado con colores
                $sub_array[] = ($row["estado"] == 1)
                    ? '<span class="badge bg-success rounded-pill">Activo</span>'
                    : '<span class="badge bg-secondary rounded-pill">Inactivo</span>';

                $id = $row["id"];
                // ... dentro de tu foreach
                $sub_array[] = '
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="dropdown">
                                    <button class="btn btn-kebab-luxury shadow-none border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical text-dark"></i>
                                    </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 animate__animated animate__fadeIn">
                                            <li>
                                                <a class="dropdown-item py-2" onclick="editarProducto(' . $id . ')">
                                                    <i class="bi bi-pencil-fill me-2 text-warning"></i> Editar Producto
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="variantes?id=' . $id . '">
                                                    <i class="bi bi-eye-fill me-2 text-info"></i> Ver Variantes
                                                </a>
                                            </li>
                                        </ul>
                                </div>
                            </div>';

                $data[] = $sub_array;
            }
            $response = ["status" => "success", "data" => $data];
            break;
        case 'crearAtributo':
            $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
            if ($productoModel->isExisteAtributo(strtolower($nombre))) {
                $response = ["status" => "error", "message" => "El atributo ya existe"];
                break;
            }

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

                // 1. CAPTURA DE DATOS BASE (Producto Padre)
                $nombreProducto = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
                $descripcion    = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : "";
                $id_categoria   = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : null;
                $estado         = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

                if ($productoModel->isExisteProducto(strtolower($nombreProducto), 0)) {
                    $response = ["status" => "error", "message" => "Nombre de producto ya existe."];
                    exit;
                }
                // 2. REGISTRO EN TABLA: producto (Llamada al modelo)
                $idProducto = $productoModel->registrarProducto($nombreProducto, $descripcion, $id_categoria, $estado);

                if (!$idProducto) {
                    $response = ["status" => "error", "message" => "Error en la insercion del producto."];
                    exit;
                }

                // 4. REGISTRO DE ATRIBUTOS (Limpiando duplicados)
                if (isset($_POST['atributo_id']) && is_array($_POST['atributo_id'])) {
                    // array_unique elimina IDs repetidos por si el JS falló
                    $atributosUnicos = array_unique($_POST['atributo_id']);
                    foreach ($atributosUnicos as $id_at) {
                        if (!empty($id_at)) {
                            $productoModel->registrarProductoAtributo($idProducto, (int)$id_at);
                        }
                    }
                }

                // 3. PROCESAMIENTO DE LA MATRIZ DE VARIANTES
                if (isset($_POST['v_sku'])) {
                    foreach ($_POST['v_sku'] as $i => $sku) {

                        // Captura de datos de la variante i
                        $nombreVariante = $_POST['v_nombre'][$i];
                        $precioCompra   = (float)$_POST['v_precio_compra'][$i];
                        $precioVenta    = (float)$_POST['v_precio_venta'][$i];
                        $stockActual    = (int)$_POST['v_stock_actual'][$i];
                        $stockMinimo    = (int)$_POST['v_stock_minimo'][$i];
                        $jsonAtributos  = $_POST['v_valores_json'][$i]; // El JSON que armamos en JS

                        // --- MANEJO DE IMAGEN ---
                        $nombreImagen = "default.png";
                        if (isset($_FILES['v_foto']['name'][$i]) && $_FILES['v_foto']['error'][$i] === UPLOAD_ERR_OK) {
                            $rutaDestino = __DIR__ . "/../views/assets/images/";
                            $ext = strtolower(pathinfo($_FILES['v_foto']['name'][$i], PATHINFO_EXTENSION));
                            $nuevoNombre = "var_" . $sku . "_" . time() . "." . $ext;

                            if (move_uploaded_file($_FILES['v_foto']['tmp_name'][$i], $rutaDestino . $nuevoNombre)) {
                                $nombreImagen = $nuevoNombre;
                            }
                        }

                        // 4. REGISTRO EN TABLA: variante (Asegúrate de que tu modelo reciba estos nuevos campos)
                        $idVariante = $productoModel->registrarVariante(
                            $idProducto,
                            $sku,
                            $nombreVariante,
                            $precioCompra,
                            $precioVenta,
                            $stockActual,
                            $stockMinimo,
                            $nombreImagen
                        );

                        if (!$idVariante) {
                            $response = ["status" => "error", "message" => "Error en la insercion de alguna variante."];
                            exit;
                        }

                        // 5. REGISTRO EN TABLA: variantevalor (Usando el JSON)
                        $atributosObj = json_decode($jsonAtributos, true); // Convierte {"1":"Rojo"} en array
                        foreach ($atributosObj as $idAtributo => $valor) {
                            $productoModel->registrarVarianteValor($idVariante, (int)$idAtributo, trim($valor));
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
