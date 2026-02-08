<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . "/../models/salidaModel.php";

$salidaModel = new Salida();

$opcion = isset($_POST["accion"]) ? trim($_POST["accion"]) : null;
$response = ["status" => "error", "message" => "Opción inválida"];

try {
    switch ($opcion) {
        case 'registrarSalida':
            $db = Conexion::conectar();
            $db->beginTransaction();
            
            $idVariante = (int)$_POST["id_variante"];
            $cantidad = (int)$_POST["cantidad"];
            
            // Verificar stock disponible
            $stockActual = $salidaModel->obtenerStockVariante($idVariante);
            
            if ($stockActual < $cantidad) {
                throw new Exception("Stock insuficiente. Disponible: " . $stockActual);
            }
            
            // Registrar la salida
            $datos = [
                'id_variante' => $idVariante,
                'id_usuario' => 2, // Cambiar por sesión real: $_SESSION['usuario_id']
                'cantidad' => $cantidad,
                'fecha_salida' => $_POST["fecha_salida"],
                'hora_salida' => $_POST["hora_salida"],
                'fecha_entrega' => !empty($_POST["fecha_entrega"]) ? $_POST["fecha_entrega"] : null,
                'direccion' => trim($_POST["direccion"]),
                'precio_envio' => (float)$_POST["precio_envio"],
                'costo_extra' => (float)$_POST["costo_extra"],
                'precio_unitario' => (float)$_POST["precio_unitario"],
                'subtotal' => (float)$_POST["subtotal"],
                'total' => (float)$_POST["total"],
                'observaciones' => trim($_POST["observaciones"])
            ];
            
            $idSalida = $salidaModel->registrarSalida($datos);
            
            if (!$idSalida) {
                throw new Exception("Error al registrar la salida");
            }
            
            // Restar del stock
            if (!$salidaModel->actualizarStock($idVariante, -$cantidad)) {
                throw new Exception("Error al actualizar el stock");
            }
            
            $db->commit();
            $response = [
                "status" => "success", 
                "message" => "Salida registrada correctamente. Stock actualizado.",
                "id_salida" => $idSalida
            ];
            break;
            
        case 'listarSalidas':
            $datos = $salidaModel->obtenerSalidas();
            $response = ["status" => "success", "data" => $datos];
            break;

        // ============================================================
        // NUEVOS CASOS PARA EL HISTORIAL DE SALIDAS
        // ============================================================

        case 'obtenerTodasLasSalidas':
            $datos = $salidaModel->obtenerTodasLasSalidas();
            $response = [
                "status" => "success", 
                "data" => $datos,
                "total" => count($datos)
            ];
            break;

        case 'obtenerDetalleSalida':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID de salida inválido");
            }

            $detalle = $salidaModel->obtenerDetalleSalida($id);
            
            if (!$detalle) {
                throw new Exception("Salida no encontrada");
            }

            $response = [
                "status" => "success",
                "data" => $detalle
            ];
            break;

        case 'obtenerEstadisticas':
            $fechaInicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
            $fechaFin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
            
            $estadisticas = $salidaModel->obtenerEstadisticas($fechaInicio, $fechaFin);
            $salidasHoy = $salidaModel->obtenerSalidasHoy();
            
            $response = [
                "status" => "success",
                "data" => array_merge($estadisticas, ['salidas_hoy' => $salidasHoy])
            ];
            break;

        case 'obtenerProductosMasVendidos':
            $limite = isset($_POST['limite']) ? (int)$_POST['limite'] : 10;
            $productos = $salidaModel->obtenerProductosMasVendidos($limite);
            
            $response = [
                "status" => "success",
                "data" => $productos
            ];
            break;

        case 'obtenerSalidasPorFecha':
            $fechaInicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
            $fechaFin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
            
            if (!$fechaInicio || !$fechaFin) {
                throw new Exception("Debe proporcionar fecha de inicio y fin");
            }

            $salidas = $salidaModel->obtenerSalidasPorFecha($fechaInicio, $fechaFin);
            
            $response = [
                "status" => "success",
                "data" => $salidas,
                "total" => count($salidas)
            ];
            break;

        case 'buscarSalidas':
            $termino = isset($_POST['termino']) ? trim($_POST['termino']) : '';
            
            if (empty($termino)) {
                throw new Exception("Debe proporcionar un término de búsqueda");
            }

            $salidas = $salidaModel->buscarSalidas($termino);
            
            $response = [
                "status" => "success",
                "data" => $salidas,
                "total" => count($salidas)
            ];
            break;

        case 'obtenerVentasPorCategoria':
            $ventas = $salidaModel->obtenerVentasPorCategoria();
            
            $response = [
                "status" => "success",
                "data" => $ventas
            ];
            break;

        case 'obtenerVentasMesActual':
            $ventas = $salidaModel->obtenerVentasMesActual();
            
            $response = [
                "status" => "success",
                "data" => $ventas
            ];
            break;

        case 'obtenerSalidasPorUsuario':
            $idUsuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
            
            if ($idUsuario <= 0) {
                throw new Exception("ID de usuario inválido");
            }

            $salidas = $salidaModel->obtenerSalidasPorUsuario($idUsuario);
            
            $response = [
                "status" => "success",
                "data" => $salidas,
                "total" => count($salidas)
            ];
            break;

        case 'verificarSalida':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID inválido");
            }

            $existe = $salidaModel->existeSalida($id);
            
            $response = [
                "status" => "success",
                "existe" => $existe
            ];
            break;
            
        default:
            $response = ["status" => "error", "message" => "Acción no válida"];
            break;
    }
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $response = ["status" => "error", "message" => $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);