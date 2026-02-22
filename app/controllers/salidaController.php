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
            $idVariante = (int)$_POST["id_variante"];
            $cantidad   = (int)$_POST["cantidad"];

            // ============================================================
            // PROTECCIÓN ANTI-DUPLICACIÓN:
            // Verificar si ya existe una salida para esta variante
            // en los últimos 10 segundos con la misma cantidad
            // ============================================================
            if ($salidaModel->existeSalidaReciente($idVariante, $cantidad)) {
                throw new Exception("Registro duplicado detectado. La salida ya fue registrada.");
            }

            $stockActual = $salidaModel->obtenerStockVariante($idVariante);
            if ($stockActual < $cantidad) {
                throw new Exception("Stock insuficiente. Disponible: " . $stockActual);
            }

            $precioUnitario = (float)$_POST["precio_unitario"];
            $subtotal       = $cantidad * $precioUnitario;
            $descuento      = isset($_POST["descuento"]) ? (float)$_POST["descuento"] : 0.0;
            $total          = max(0.0, $subtotal - $descuento);

            $datos = [
                'id_variante'     => $idVariante,
                'id_usuario'      => 2,
                'cantidad'        => $cantidad,
                'fecha_salida'    => $_POST["fecha_salida"],
                'hora_salida'     => $_POST["hora_salida"],
                'precio_unitario' => $precioUnitario,
                'subtotal'        => $subtotal,
                'descuento'       => $descuento,
                'total'           => $total,
            ];

            $idSalida = $salidaModel->registrarSalida($datos);

            if (!$idSalida) {
                throw new Exception("Error al registrar la salida en la base de datos");
            }

            if (!$salidaModel->actualizarStock($idVariante, -$cantidad)) {
                throw new Exception("Error al actualizar el stock");
            }

            $response = [
                "status"    => "success",
                "message"   => "Salida registrada correctamente. Stock actualizado.",
                "id_salida" => $idSalida
            ];
            break;

        case 'listarSalidas':
            $datos = $salidaModel->obtenerSalidas();
            $response = ["status" => "success", "data" => $datos];
            break;

        case 'obtenerTodasLasSalidas':
            $datos = $salidaModel->obtenerTodasLasSalidas();
            $response = [
                "status" => "success",
                "data"   => $datos,
                "total"  => count($datos)
            ];
            break;

        case 'obtenerDetalleSalida':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) throw new Exception("ID de salida inválido");

            $detalle = $salidaModel->obtenerDetalleSalida($id);
            if (!$detalle) throw new Exception("Salida no encontrada");

            $response = ["status" => "success", "data" => $detalle];
            break;

        case 'obtenerEstadisticas':
            $fechaInicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
            $fechaFin    = isset($_POST['fecha_fin'])    ? trim($_POST['fecha_fin'])    : null;

            $estadisticas = $salidaModel->obtenerEstadisticas($fechaInicio, $fechaFin);
            $salidasHoy   = $salidaModel->obtenerSalidasHoy();

            $response = [
                "status" => "success",
                "data"   => array_merge($estadisticas, ['salidas_hoy' => $salidasHoy])
            ];
            break;

        case 'obtenerProductosMasVendidos':
            $limite   = isset($_POST['limite']) ? (int)$_POST['limite'] : 10;
            $productos = $salidaModel->obtenerProductosMasVendidos($limite);
            $response = ["status" => "success", "data" => $productos];
            break;

        case 'obtenerSalidasPorFecha':
            $fechaInicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
            $fechaFin    = isset($_POST['fecha_fin'])    ? trim($_POST['fecha_fin'])    : null;

            if (!$fechaInicio || !$fechaFin) throw new Exception("Debe proporcionar fecha de inicio y fin");

            $salidas  = $salidaModel->obtenerSalidasPorFecha($fechaInicio, $fechaFin);
            $response = ["status" => "success", "data" => $salidas, "total" => count($salidas)];
            break;

        case 'buscarSalidas':
            $termino = isset($_POST['termino']) ? trim($_POST['termino']) : '';
            if (empty($termino)) throw new Exception("Debe proporcionar un término de búsqueda");

            $salidas  = $salidaModel->buscarSalidas($termino);
            $response = ["status" => "success", "data" => $salidas, "total" => count($salidas)];
            break;

        case 'obtenerVentasPorCategoria':
            $ventas   = $salidaModel->obtenerVentasPorCategoria();
            $response = ["status" => "success", "data" => $ventas];
            break;

        case 'obtenerVentasMesActual':
            $ventas   = $salidaModel->obtenerVentasMesActual();
            $response = ["status" => "success", "data" => $ventas];
            break;

        case 'cambiarEstado':
            $id          = isset($_POST['id'])           ? (int)$_POST['id']                : 0;
            $nuevoEstado = isset($_POST['nuevo_estado']) ? trim($_POST['nuevo_estado'])      : '';

            if ($id <= 0)          throw new Exception("ID de salida inválido");
            if (empty($nuevoEstado)) throw new Exception("Debe especificar el nuevo estado");

            $estadosValidos = ['Pendiente', 'En camino', 'Entregado', 'Cancelado'];
            if (!in_array($nuevoEstado, $estadosValidos)) throw new Exception("Estado no válido");

            if ($salidaModel->cambiarEstadoSalida($id, $nuevoEstado)) {
                $response = ["status" => "success", "message" => "Estado actualizado correctamente a: " . $nuevoEstado];
            } else {
                throw new Exception("Error al actualizar el estado");
            }
            break;

        case 'obtenerSalidasPorUsuario':
            $idUsuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
            if ($idUsuario <= 0) throw new Exception("ID de usuario inválido");

            $salidas  = $salidaModel->obtenerSalidasPorUsuario($idUsuario);
            $response = ["status" => "success", "data" => $salidas, "total" => count($salidas)];
            break;

        case 'verificarSalida':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) throw new Exception("ID inválido");

            $response = ["status" => "success", "existe" => $salidaModel->existeSalida($id)];
            break;

        case 'verificarPuedeDevolver':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) throw new Exception("ID inválido");

            $validacion = $salidaModel->puedeDevolver($id);
            $response   = [
                "status"          => "success",
                "puede_devolver"  => $validacion['puede'],
                "motivo"          => $validacion['motivo'],
                "dias_restantes"  => $validacion['dias_restantes'] ?? null
            ];
            break;

        case 'devolverSalida':
            $id       = isset($_POST['id'])       ? (int)$_POST['id']      : 0;
            $motivo   = isset($_POST['motivo'])   ? trim($_POST['motivo'])  : '';
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : null;

            if ($id <= 0) throw new Exception("ID de salida inválido");

            $resultado = $salidaModel->procesarDevolucion($id, $motivo, $cantidad);

            if ($resultado['exito']) {
                $response = [
                    "status"             => "success",
                    "message"            => $resultado['mensaje'],
                    "cantidad_devuelta"  => $resultado['cantidad_devuelta'],
                    "stock_actualizado"  => $resultado['stock_actualizado']
                ];
            } else {
                throw new Exception($resultado['mensaje']);
            }
            break;

        case 'obtenerEstadisticasDevoluciones':
            $estadisticas = $salidaModel->obtenerEstadisticasDevoluciones();
            $response     = ["status" => "success", "data" => $estadisticas];
            break;

        default:
            $response = ["status" => "error", "message" => "Acción no válida"];
            break;
    }

} catch (Exception $e) {
    $response = ["status" => "error", "message" => $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);