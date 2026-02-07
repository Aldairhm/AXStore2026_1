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
        /* ============================================================
           1. CARGA Y VISUALIZACIÓN
        ============================================================ */
        case 'cargarProductos':
            $datos = $productoModel->getProductosFull();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = htmlspecialchars($row["nombre"]);
                $sub_array[] = '<span class="badge bg-light text-dark border">' . htmlspecialchars($row["categoria"] ?? "S/C") . '</span>';
                $sub_array[] = htmlspecialchars($row["descripcion"]);
                $sub_array[] = ($row["estado"] == 1)
                    ? '<span class="badge bg-success rounded-pill">Activo</span>'
                    : '<span class="badge bg-secondary rounded-pill">Inactivo</span>';

                $id = $row["id"];
                $sub_array[] = '
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-kebab-luxury shadow-none border-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical text-dark"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 animate__animated animate__fadeIn">
                                <li><a class="dropdown-item py-2" onclick="editarProducto(' . $id . ')"><i class="bi bi-pencil-fill me-2 text-warning"></i> Editar Producto</a></li>
                                <li><a class="dropdown-item py-2" href="variantes?id=' . $id . '"><i class="bi bi-eye-fill me-2 text-info"></i> Ver Variantes</a></li>
                            </ul>
                        </div>
                    </div>';
                $data[] = $sub_array;
            }
            $response = ["status" => "success", "data" => $data];
            break;

        case 'variantes':
            $id_producto = isset($_POST["id"]) ? (int)$_POST["id"] : null;
            $datos = $productoModel->getVariantesPorProducto($id_producto);
            $response = ["status" => "success", "data" => $datos];
            break;

        /* ============================================================
           2. GESTIÓN DE PRODUCTO PADRE
        ============================================================ */
        case 'obtener_uno':
            $id_producto = isset($_POST["id"]) ? (int)$_POST["id"] : null;
            $dato = $productoModel->obtenerProductoPorId($id_producto);
            $response = $dato ? ["status" => "success", "data" => $dato] : ["status" => "error", "message" => "No encontrado"];
            break;

        case 'editarProducto':
            $id_producto = (int)$_POST["id_producto"];
            $nombre = trim($_POST["nombre"]);
            if ($productoModel->isExisteProducto(strtolower($nombre), $id_producto)) {
                throw new Exception("El nombre del producto ya existe.");
            }
            $res = $productoModel->actualizarProducto($id_producto, $nombre, $_POST["descripcion"], (int)$_POST["id_categoria"], (int)$_POST["estado"]);
            $response = $res ? ["status" => "success", "message" => "Actualizado"] : ["status" => "error", "message" => "Sin cambios"];
            break;

        /* ============================================================
           3. REGISTRO MAESTRO (CON HASH Y SKU ALEATORIO)
        ============================================================ */
        case 'registrarProductoCompleto':
            $db = Conexion::conectar();
            $db->beginTransaction();

            $idProducto = $productoModel->registrarProducto($_POST['nombre'], $_POST['descripcion'], (int)$_POST['id_categoria'], (int)$_POST['estado']);

            if (isset($_POST['atributo_id'])) {
                foreach (array_unique($_POST['atributo_id']) as $id_at) {
                    if (!empty($id_at)) $productoModel->registrarProductoAtributo($idProducto, (int)$id_at);
                }
            }

            if (isset($_POST['v_valores_json'])) {
                $hashesSesion = [];
                foreach ($_POST['v_valores_json'] as $i => $jsonAtributos) {
                    $atributosObj = json_decode($jsonAtributos, true);
                    $hash = $productoModel->generarHashVariante($atributosObj);

                    if (in_array($hash, $hashesSesion) || $productoModel->existeHashEnProducto($idProducto, $hash)) {
                        throw new Exception("Combinación duplicada: " . $_POST['v_nombre'][$i]);
                    }
                    $hashesSesion[] = $hash;
                    $sku = $productoModel->generarSkuAleatorio();

                    // --- TU LÓGICA DE IMAGEN ORIGINAL ---
                    $nombreImagen = "default.webp";
                    if (isset($_FILES['v_foto']['name'][$i]) && $_FILES['v_foto']['error'][$i] === UPLOAD_ERR_OK) {
                        $rutaDestino = __DIR__ . "/../views/assets/images/";
                        $ext = strtolower(pathinfo($_FILES['v_foto']['name'][$i], PATHINFO_EXTENSION));
                        $nuevoNombre = "var_" . $sku . "_" . time() . "." . $ext;
                        if (move_uploaded_file($_FILES['v_foto']['tmp_name'][$i], $rutaDestino . $nuevoNombre)) $nombreImagen = $nuevoNombre;
                    }

                    // --- DENTRO DEL FOREACH DE 'registrarProductoCompleto' ---
                    $idVar = $productoModel->registrarVariante(
                        $idProducto,
                        $sku,
                        $hash,
                        $_POST['v_nombre'][$i],
                        (float)$_POST['v_precio_compra'][$i],
                        (float)$_POST['v_precio_venta'][$i],
                        (int)$_POST['v_stock_actual'][$i],
                        (int)$_POST['v_stock_minimo'][$i],
                        (float)$_POST['v_comision'][$i]
                    );

                    // ¡OJO ACÁ! Esto es lo que faltaba:
                    if ($idVar) {
                        // Registramos la imagen en la nueva tabla de galería como principal (1)
                        $productoModel->registrarImagenVariante($idVar, $nombreImagen, 1);
                    }

                    foreach ($atributosObj as $idA => $val) {
                        $productoModel->registrarVarianteValor($idVar, (int)$idA, trim($val));
                    }
                    $nuevoNombre = $productoModel->generarNombreVarianteDesdeAtributos($idVar);
                    $productoModel->actualizarNombreVariante($idVar, $nuevoNombre);
                }
            }
            $db->commit();
            $response = ["status" => "success", "message" => "Registrado con éxito"];
            break;

        /* ============================================================
           4. EDICIÓN DE VARIANTE INDIVIDUAL
        ============================================================ */
        case 'obtenerVariantePorId':
            $id = (int)$_POST["id"];
            $response = ["status" => "success", "data" => [
                "variante" => $productoModel->getVariantePorId($id),
                "atributos" => $productoModel->obtenerValorAtributosVariante($id)
            ]];
            break;

        case 'editarVariante':
            $db = Conexion::conectar();
            $db->beginTransaction();
            try {
                $idV = (int)$_POST["id_variante_edit"];
                // Asegurate que este nombre coincida con tu <input name="...">
                $idP = (int)$_POST["id_producto"];
                $sku = trim($_POST["sku_edit"]);

                $atributos = $_POST['atributo_valor'] ?? [];
                $hash = $productoModel->generarHashVariante($atributos);

                // 1. VALIDACIÓN LÓGICA: Mensaje amigable antes de tocar la base de datos
                if ($productoModel->existeHashEnOtro($idP, $hash, $idV)) {
                    // Aquí mandamos el mensaje "lógico"
                    throw new Exception("No se pudo guardar: Ya tenés otra variante registrada con exactamente la misma combinación de atributos.");
                }

                // 2. ACTUALIZACIÓN DE DATOS (Mantenemos tu orden de parámetros)
                $productoModel->actualizarDatosVariante(
                    $idV,
                    $sku,
                    $hash,
                    (int)$_POST["stock_actual_edit"],
                    (float)$_POST["precio_venta_edit"],
                    (int)$_POST["stock_minimo_edit"],
                    (float)$_POST["comision_edit"]
                );

                // 3. ACTUALIZACIÓN DE ATRIBUTOS EAV
                foreach ($atributos as $idA => $val) {
                    $productoModel->actualizarValorAtributo($idV, (int)$idA, $val);
                }

                // 4. REGENERACIÓN DE NOMBRE Y CIERRE
                $nuevoNombre = $productoModel->generarNombreVarianteDesdeAtributos($idV);
                $productoModel->actualizarNombreVariante($idV, $nuevoNombre);

                // Mantenemos tu lógica de imagen intacta
                if (isset($_FILES['foto_edit']) && $_FILES['foto_edit']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['foto_edit']['name'], PATHINFO_EXTENSION));
                    $imgNom = "var_" . $sku . "_" . time() . "." . $ext;

                    if (move_uploaded_file($_FILES['foto_edit']['tmp_name'], __DIR__ . "/../views/assets/images/" . $imgNom)) {
                        // 1. Insertamos la nueva imagen
                        // USAMOS EL MÉTODO Y OBTENEMOS EL ID
                        $idNuevaImg = $productoModel->registrarImagenVariante($idV, $imgNom, 1);

                        if ($idNuevaImg) {
                            // Ponemos a esta como jefa y a las demás como 0
                            $productoModel->setearPrincipal($idV, $idNuevaImg);
                        }
                    }
                }
                $db->commit();
                $response = ["status" => "success", "message" => "¡Excelente! La variante se actualizó correctamente."];
            } catch (PDOException $e) {
                $db->rollBack();
                // Si por alguna razón la validación manual falló y llegó a la base de datos
                if ($e->getCode() == 23000) {
                    $response = ["status" => "error", "message" => "Error de duplicidad: Esa combinación ya está en uso."];
                } else {
                    $response = ["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()];
                }
            } catch (Exception $e) {
                $db->rollBack();
                $response = ["status" => "error", "message" => $e->getMessage()];
            }
            break;
        /* ============================================================
           5. EXPANSIÓN Y ATRIBUTOS
        ============================================================ */
        case 'obtenerDetalleParaExpansion':
            $id = (int)$_POST['id'];
            $producto = $productoModel->obtenerProductoPorId($id);
            $response = ["status" => "success", "data" => [
                "producto" => $producto,
                "atributos" => $productoModel->obtenerAtributosProducto($id)
            ], "tieneVentas" => $productoModel->verificarVentasProducto($id)];
            break;

        case 'crearAtributo':
            $nombre = trim($_POST["nombre"]);
            if ($productoModel->isExisteAtributo(strtolower($nombre))) throw new Exception("Ya existe.");
            $id = $productoModel->registrarAtributo($nombre);
            $response = ["status" => "success", "id" => $id];
            break;

        case 'obtenerAtributos':
            $response = ["status" => "success", "data" => $productoModel->getAtributos()];
            break;

        case 'obtenerCategorias':
            $response = ["status" => "success", "data" => $categoriaModel->getCategorias()];
            break;

        case 'agregarVariantesIncremental':
            $db = Conexion::conectar();
            $db->beginTransaction();
            $idP = (int)$_POST['id_producto'];

            if (isset($_POST['atributo_id'])) {
                $enviados = array_map('intval', array_unique($_POST['atributo_id']));
                $actuales = $productoModel->obtenerIdsAtributosDeProducto($idP);
                foreach ($enviados as $idAt) {
                    if (!in_array($idAt, $actuales)) {
                        $productoModel->registrarProductoAtributo($idP, $idAt);
                        $productoModel->inyectarValorNA($idP, $idAt);
                    }
                }
                foreach ($actuales as $idAt) {
                    if (!in_array($idAt, $enviados)) $productoModel->eliminarAtributoDelContrato($idP, $idAt);
                }
            }

            if (isset($_POST['v_valores_json'])) {
                foreach ($_POST['v_valores_json'] as $i => $jsonAt) {
                    if ($_POST['v_id'][$i] == 0) {
                        $attrObj = json_decode($jsonAt, true);
                        $hash = $productoModel->generarHashVariante($attrObj);
                        if ($productoModel->existeHashEnProducto($idP, $hash)) throw new Exception("Duplicado detectado.");

                        $sku = $productoModel->generarSkuAleatorio();
                        $foto = "default.webp";
                        if (isset($_FILES['v_foto']['name'][$i]) && $_FILES['v_foto']['error'][$i] === UPLOAD_ERR_OK) {
                            $ext = strtolower(pathinfo($_FILES['v_foto']['name'][$i], PATHINFO_EXTENSION));
                            $foto = "var_" . $sku . "_" . time() . "." . $ext;
                            move_uploaded_file($_FILES['v_foto']['tmp_name'][$i], __DIR__ . "/../views/assets/images/" . $foto);
                        }

                        $idV = $productoModel->registrarVariante($idP, $sku, $hash, $_POST['v_nombre'][$i], (float)$_POST['v_precio_compra'][$i], (float)$_POST['v_precio_venta'][$i], (int)$_POST['v_stock_actual'][$i], (int)$_POST['v_stock_minimo'][$i], (float)$_POST['v_comision'][$i]);
                        $productoModel->registrarImagenVariante($idV, $foto, 1);
                        foreach ($attrObj as $idA => $val) $productoModel->registrarVarianteValor($idV, (int)$idA, trim($val));
                    }
                }
            }
            $db->commit();
            $response = ["status" => "success", "message" => "Expansión lista"];
            break;

        /* ============================================================
                6. NUEVA GESTIÓN DE GALERÍA (MODAL)
            ============================================================ */

        case 'obtenerGaleria':
            $idV = (int)$_POST["id"];
            $galeria = $productoModel->getGaleriaVariante($idV);
            $response = ["status" => "success", "data" => $galeria];
            break;

        case 'subirGaleriaVariante':
            $idV = (int)$_POST["id_variante_galeria"];
            if (isset($_FILES['imagenes_galeria'])) {
                foreach ($_FILES['imagenes_galeria']['tmp_name'] as $key => $tmp_name) {
                    $ext = strtolower(pathinfo($_FILES['imagenes_galeria']['name'][$key], PATHINFO_EXTENSION));
                    $nombre = "gal_" . uniqid() . "." . $ext;
                    if (move_uploaded_file($tmp_name, __DIR__ . "/../views/assets/images/" . $nombre)) {
                        $productoModel->registrarImagenVariante($idV, $nombre, 0); // 0 = No es principal por defecto
                    }
                }
                $response = ["status" => "success", "message" => "Galería actualizada", "idRefresh" => $idV];
            }
            break;

        case 'eliminarFoto':
            $idImg = (int)$_POST["id"];
            $response = $productoModel->eliminarImagenBD($idImg);

            if ($response["status"] === "success") {
                // Borramos el archivo físico para no llenar el server de basura
                $archivo = __DIR__ . "/../views/assets/images/" . $response["ruta"];
                if (file_exists($archivo) && $response["ruta"] != 'default.png') {
                    unlink($archivo);
                }
            }
            break;

        case 'cambiarPortada':
            $idV = (int)$_POST["id_variante"];
            $idImg = (int)$_POST["id"];
            $res = $productoModel->setearPrincipal($idV, $idImg);
            $response = $res ? ["status" => "success"] : ["status" => "error"];
            break;
    }
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    $response = ["status" => "error", "message" => $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
