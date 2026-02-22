<?php

declare(strict_types = 1)
;

require_once __DIR__ . "/../../config/conexion.php";

class Salida
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function registrarSalida(array $datos): ?int
    {
        $sql = "INSERT INTO salida (
                id_variante, id_usuario, cantidad, fecha_salida, hora_salida,
                precio_unitario, subtotal, descuento, total, estado, fecha_entrega
            ) VALUES (
                :id_variante, :id_usuario, :cantidad, :fecha_salida, :hora_salida,
                :precio_unitario, :subtotal, :descuento, :total, 'Entregado', :fecha_entrega
            )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ':id_variante' => $datos['id_variante'],
            ':id_usuario' => $datos['id_usuario'],
            ':cantidad' => $datos['cantidad'],
            ':fecha_salida' => $datos['fecha_salida'],
            ':hora_salida' => $datos['hora_salida'],
            ':precio_unitario' => $datos['precio_unitario'],
            ':subtotal' => $datos['subtotal'],
            ':descuento' => $datos['descuento'],
            ':total' => $datos['total'],
            ':fecha_entrega' => $datos['fecha_salida'],
        ]) ? (int)$this->conexion->lastInsertId() : null;
    }

    public function obtenerStockVariante(int $idVariante): int
    {
        $sql = "SELECT stock FROM variante WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $idVariante]);
        return (int)$stmt->fetchColumn();
    }

    public function actualizarStock(int $idVariante, int $cantidad): bool
    {
        $sql = "UPDATE variante SET stock = stock + :cantidad WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':cantidad' => $cantidad, ':id' => $idVariante]);
    }

    public function obtenerSalidas(): array
    {
        $sql = "SELECT 
                    s.*, 
                    v.nombre_variante, 
                    v.sku,
                    vi.ruta_imagen as imagen,
                    u.nombre_real as usuario
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                ORDER BY s.created_at DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // MÉTODOS PARA EL HISTORIAL DE SALIDAS
    // ============================================================

    public function obtenerTodasLasSalidas(): array
    {
        $this->limpiarSalidasVencidas();

        $sql = "SELECT 
                    s.id,
                    s.id_variante,
                    s.id_usuario,
                    s.cantidad,
                    s.precio_unitario,
                    s.subtotal,
                    s.precio_envio,
                    s.costo_extra,
                    s.total,
                    s.fecha_salida,
                    s.hora_salida,
                    s.fecha_entrega,
                    s.direccion,
                    s.observaciones,
                    s.estado,
                    s.fecha_cancelacion,
                    s.created_at,
                    v.sku,
                    vi.ruta_imagen as imagen,
                    v.nombre_variante as nombre_producto,
                    v.stock as stock_actual,
                    u.nombre_real as usuario,
                    c.nombre as nombre_categoria,
                    1 as puede_devolver
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                INNER JOIN producto p ON v.id_producto = p.id
                LEFT JOIN categoria c ON p.id_categoria = c.id
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                ORDER BY s.fecha_salida DESC, s.hora_salida DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleSalida(int $id): ?array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    vi.ruta_imagen as imagen,
                    v.nombre_variante as nombre_producto,
                    v.stock as stock_actual,
                    u.nombre_real as usuario,
                    c.nombre as nombre_categoria,
                    p.nombre as nombre_producto_padre,
                    1 as puede_devolver,
                    NULL as dias_para_devolucion
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                INNER JOIN producto p ON v.id_producto = p.id
                LEFT JOIN categoria c ON p.id_categoria = c.id
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                WHERE s.id = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    public function obtenerEstadisticas(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_salidas,
                    COALESCE(SUM(cantidad), 0) as total_unidades,
                    COALESCE(SUM(total), 0) as monto_total,
                    COALESCE(AVG(total), 0) as promedio_venta,
                    COALESCE(SUM(precio_envio), 0) as total_envios,
                    COALESCE(SUM(costo_extra), 0) as total_extras
                FROM salida";

        $params = [];

        $sql .= " WHERE estado != 'Cancelado'";

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha_salida BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $fechaInicio;
            $params[':fecha_fin'] = $fechaFin;
        }
        elseif ($fechaInicio) {
            $sql .= " AND fecha_salida >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }
        elseif ($fechaFin) {
            $sql .= " AND fecha_salida <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerProductosMasVendidos(int $limite = 10): array
    {
        $sql = "SELECT 
                    v.id as id_variante,
                    v.nombre_variante as nombre_producto,
                    v.sku,
                    vi.ruta_imagen as imagen,
                    v.stock as stock_actual,
                    COUNT(s.id) as num_salidas,
                    SUM(s.cantidad) as total_vendido,
                    SUM(s.total) as ingresos_totales,
                    AVG(s.precio_unitario) as precio_promedio
                FROM variante v
                INNER JOIN salida s ON v.id = s.id_variante
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                GROUP BY v.id, v.nombre_variante, v.sku, vi.ruta_imagen, v.stock
                ORDER BY total_vendido DESC
                LIMIT :limite";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSalidasHoy(): int
    {
        $sql = "SELECT COUNT(*) 
                FROM salida 
                WHERE DATE(fecha_salida) = CURDATE()";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function obtenerSalidasPorUsuario(int $idUsuario): array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    vi.ruta_imagen as imagen,
                    v.nombre_variante as nombre_producto
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                WHERE s.id_usuario = :id_usuario
                ORDER BY s.fecha_salida DESC, s.hora_salida DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSalidasPorFecha(string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    vi.ruta_imagen as imagen,
                    v.nombre_variante as nombre_producto,
                    u.nombre_real as usuario
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                WHERE s.fecha_salida BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY s.fecha_salida DESC, s.hora_salida DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarSalidas(string $termino): array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    vi.ruta_imagen as imagen,
                    v.nombre_variante as nombre_producto,
                    u.nombre_real as usuario
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                LEFT JOIN variante_imagen vi ON v.id = vi.id_variante AND vi.es_principal = 1
                WHERE v.sku LIKE :termino
                   OR v.nombre_variante LIKE :termino
                   OR s.observaciones LIKE :termino
                   OR s.direccion LIKE :termino
                ORDER BY s.fecha_salida DESC, s.hora_salida DESC";

        $stmt = $this->conexion->prepare($sql);
        $terminoBusqueda = "%{$termino}%";
        $stmt->execute([':termino' => $terminoBusqueda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasPorCategoria(): array
    {
        $sql = "SELECT 
                    c.id,
                    c.nombre as categoria,
                    COUNT(s.id) as total_salidas,
                    SUM(s.cantidad) as unidades_vendidas,
                    SUM(s.total) as ingresos_totales
                FROM categoria c
                LEFT JOIN producto p ON c.id = p.id_categoria
                LEFT JOIN variante v ON p.id = v.id_producto
                LEFT JOIN salida s ON v.id = s.id_variante
                GROUP BY c.id, c.nombre
                HAVING total_salidas > 0
                ORDER BY ingresos_totales DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeSalida(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM salida WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function obtenerVentasMesActual(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_salidas,
                    SUM(cantidad) as unidades_vendidas,
                    SUM(total) as ingresos_totales
                FROM salida
                WHERE MONTH(fecha_salida) = MONTH(CURDATE())
                  AND YEAR(fecha_salida) = YEAR(CURDATE())";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // ============================================================
    // MÉTODOS MEJORADOS PARA DEVOLUCIONES CON VALIDACIÓN DE FECHA
    // ============================================================

    /**
     * Cambiar el estado de una salida
     */
    public function cambiarEstadoSalida(int $id, string $nuevoEstado, string $motivo = ''): bool
    {
        $estadosValidos = ['Pendiente', 'En camino', 'Entregado', 'Cancelado'];

        if (!in_array($nuevoEstado, $estadosValidos)) {
            return false;
        }

        // Definir SQL base
        $sql = "UPDATE salida SET estado = :estado";
        $params = [
            ':estado' => $nuevoEstado,
            ':id'     => $id
        ];

        // Si se proporciona un motivo, guardarlo en observaciones
        if (!empty($motivo)) {
            $sql .= ", observaciones = :motivo";
            $params[':motivo'] = $motivo;
        }

        // Manejar fecha de cancelación explícitamente
        if ($nuevoEstado === 'Cancelado') {
            $sql .= ", fecha_cancelacion = NOW()";
        } else {
            $sql .= ", fecha_cancelacion = NULL";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Verificar si una salida puede ser devuelta
     * Valida: estado, fecha de entrega y que no haya sido devuelta antes
     */
    public function puedeDevolver(int $id): array
    {
        $salida = $this->obtenerDetalleSalida($id);

        if (!$salida) {
            return [
                'puede' => false,
                'motivo' => 'Salida no encontrada'
            ];
        }

        // Verificar si ya está cancelada
        if ($salida['estado'] === 'Cancelado') {
            return [
                'puede' => false,
                'motivo' => 'Esta salida ya fue cancelada anteriormente'
            ];
        }

        // PERMITIR DEVOLUCIÓN EN CUALQUIER OTRO ESTADO (Incluso Entregado o Vencido)
        /*
         // Verificar si ya fue entregada
         if ($salida['estado'] === 'Entregado') {
         return [
         'puede' => false,
         'motivo' => 'No se puede devolver una salida ya entregada'
         ];
         }
         // Verificar fecha de entrega (solo si existe)
         if ($salida['fecha_entrega']) {
         $fechaEntrega = new DateTime($salida['fecha_entrega']);
         $fechaActual = new DateTime();
         
         if ($fechaActual > $fechaEntrega) {
         $diasPasados = $fechaActual->diff($fechaEntrega)->days;
         return [
         'puede' => false,
         'motivo' => "El plazo de devolución venció hace {$diasPasados} día(s). Fecha límite: " . $fechaEntrega->format('d/m/Y')
         ];
         }
         }
         */

        // Si pasa todas las validaciones
        $diasRestantes = null;
        if ($salida['fecha_entrega']) {
            $fechaEntrega = new DateTime($salida['fecha_entrega']);
            $fechaActual = new DateTime();
            $diasRestantes = $fechaActual->diff($fechaEntrega)->days;
        }

        return [
            'puede' => true,
            'motivo' => 'Devolución permitida',
            'dias_restantes' => $diasRestantes
        ];
    }

    /**
     * Registrar una devolución con todas las validaciones
     */
   public function procesarDevolucion(int $id, string $motivo = '', ?int $cantidadADevolver = null): array
{
    $salida = $this->obtenerDetalleSalida($id);

    if (!$salida) {
        return ['exito' => false, 'mensaje' => 'Salida no encontrada'];
    }

    if ($salida['estado'] === 'Cancelado') {
        return ['exito' => false, 'mensaje' => 'Esta salida ya fue cancelada'];
    }

    $cantidadOriginal = (int)$salida['cantidad'];

    // Si no se especifica cantidad, devolver todo
    if ($cantidadADevolver === null || $cantidadADevolver <= 0) {
        $cantidadADevolver = $cantidadOriginal;
    }

    // No puede devolver más de lo que hay
    if ($cantidadADevolver > $cantidadOriginal) {
        return ['exito' => false, 'mensaje' => 'No puede devolver más unidades de las registradas'];
    }

    $esTotal = ($cantidadADevolver >= $cantidadOriginal);

    try {
        $this->conexion->beginTransaction();

        if ($esTotal) {
            // Devolución total: cancelar la salida y guardar motivo
            if (!$this->cambiarEstadoSalida($id, 'Cancelado', $motivo)) {
                throw new Exception("Error al cancelar la salida");
            }
        } else {
            // Devolución parcial: reducir cantidad, mantener estado Entregado
            $nuevaCantidad = $cantidadOriginal - $cantidadADevolver;
            $nuevoSubtotal = $nuevaCantidad * (float)$salida['precio_unitario'];
            $nuevoTotal    = max(0.0, $nuevoSubtotal - (float)$salida['descuento']);

            $sql = "UPDATE salida SET 
                        cantidad = :cantidad, 
                        subtotal = :subtotal, 
                        total = :total,
                        observaciones = :motivo
                    WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            if (!$stmt->execute([
                ':cantidad' => $nuevaCantidad,
                ':subtotal' => $nuevoSubtotal,
                ':total'    => $nuevoTotal,
                ':motivo'   => $motivo ?: $salida['observaciones'], // Mantener anterior si no hay nuevo
                ':id'       => $id
            ])) {
                throw new Exception("Error al actualizar la cantidad");
            }
        }

        // Restaurar stock con la cantidad devuelta
        if (!$this->actualizarStock($salida['id_variante'], $cantidadADevolver)) {
            throw new Exception("Error al restaurar el stock");
        }

            // 3. Registrar en el historial de devoluciones (NUEVO)
            $sqlDev = "INSERT INTO devolucion (id_salida, cantidad, motivo) VALUES (:id_salida, :cantidad, :motivo)";
            $stmtDev = $this->conexion->prepare($sqlDev);
            if (!$stmtDev->execute([
                ':id_salida' => $id,
                ':cantidad'  => $cantidadADevolver,
                ':motivo'    => $motivo ?: 'Devolución procesada'
            ])) {
                throw new Exception("Error al registrar el historial de devoluciones");
            }

            $this->conexion->commit();

        $mensaje = $esTotal
            ? "Devolución total: se canceló la salida y se restauraron {$cantidadADevolver} unidades."
            : "Devolución parcial: se devolvieron {$cantidadADevolver} unidades al stock. La salida continúa con " . ($cantidadOriginal - $cantidadADevolver) . " unidades.";

        return [
            'exito'            => true,
            'mensaje'          => $mensaje,
            'cantidad_devuelta' => $cantidadADevolver,
            'stock_actualizado' => (int)$salida['stock_actual'] + $cantidadADevolver
        ];

    } catch (Exception $e) {
        $this->conexion->rollBack();
        return ['exito' => false, 'mensaje' => "Error: " . $e->getMessage()];
    }
}

    /**
     * Eliminar permanentemente salidas canceladas después de 3 días
     */
    public function limpiarSalidasVencidas(): void
    {
        try {
            $sql = "DELETE FROM salida 
                    WHERE estado = 'Cancelado' 
                    AND fecha_cancelacion <= DATE_SUB(NOW(), INTERVAL 3 DAY)";
            $this->conexion->exec($sql);
        } catch (Exception $e) {
            // Silencio, no queremos que un error de limpieza bloquee el listado
        }
    }

    /**
     * Obtener estadísticas de devoluciones
     */
    public function obtenerEstadisticasDevoluciones(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_devoluciones,
                    SUM(cantidad) as unidades_devueltas,
                    SUM(total) as monto_devuelto,
                    DATE(MAX(fecha_cancelacion)) as ultima_devolucion
                FROM salida
                WHERE estado = 'Cancelado'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    /**
     * Obtener el historial de devoluciones de una salida
     */
    public function obtenerHistorialDevoluciones(int $idSalida): array
    {
        $sql = "SELECT * FROM devolucion WHERE id_salida = :id ORDER BY fecha_registro DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $idSalida]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}