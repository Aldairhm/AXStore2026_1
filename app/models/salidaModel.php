<?php

declare(strict_types=1);

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
                    fecha_entrega, direccion, precio_envio, costo_extra, 
                    precio_unitario, subtotal, total, observaciones
                ) VALUES (
                    :id_variante, :id_usuario, :cantidad, :fecha_salida, :hora_salida,
                    :fecha_entrega, :direccion, :precio_envio, :costo_extra,
                    :precio_unitario, :subtotal, :total, :observaciones
                )";
        
        $stmt = $this->conexion->prepare($sql);
        
        return $stmt->execute([
            ':id_variante' => $datos['id_variante'],
            ':id_usuario' => $datos['id_usuario'],
            ':cantidad' => $datos['cantidad'],
            ':fecha_salida' => $datos['fecha_salida'],
            ':hora_salida' => $datos['hora_salida'],
            ':fecha_entrega' => $datos['fecha_entrega'],
            ':direccion' => $datos['direccion'],
            ':precio_envio' => $datos['precio_envio'],
            ':costo_extra' => $datos['costo_extra'],
            ':precio_unitario' => $datos['precio_unitario'],
            ':subtotal' => $datos['subtotal'],
            ':total' => $datos['total'],
            ':observaciones' => $datos['observaciones']
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
                    v.imagen,
                    u.nombre_real as usuario
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                ORDER BY s.created_at DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // NUEVOS MÉTODOS PARA EL HISTORIAL DE SALIDAS
    // ============================================================

    /**
     * Obtener todas las salidas con información completa del producto
     */
   
     public function obtenerTodasLasSalidas(): array
{
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
                s.created_at,
                v.sku,
                v.imagen,
                v.nombre_variante as nombre_producto,
                v.stock as stock_actual,
                u.nombre_real as usuario,
                c.nombre as nombre_categoria
            FROM salida s
            INNER JOIN variante v ON s.id_variante = v.id
            INNER JOIN usuario u ON s.id_usuario = u.id
            INNER JOIN producto p ON v.id_producto = p.id
            LEFT JOIN categoria c ON p.id_categoria = c.id
            ORDER BY s.fecha_salida DESC, s.hora_salida DESC";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
     * Obtener detalle completo de una salida específica
     */
    public function obtenerDetalleSalida(int $id): ?array
{
    $sql = "SELECT 
                s.*,
                v.sku,
                v.imagen,
                v.nombre_variante as nombre_producto,
                v.stock as stock_actual,
                u.nombre_real as usuario,
                c.nombre as nombre_categoria,
                p.nombre as nombre_producto_padre
            FROM salida s
            INNER JOIN variante v ON s.id_variante = v.id
            INNER JOIN usuario u ON s.id_usuario = u.id
            INNER JOIN producto p ON v.id_producto = p.id
            LEFT JOIN categoria c ON p.id_categoria = c.id
            WHERE s.id = :id";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([':id' => $id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $resultado ?: null;
}

    /**
     * Obtener estadísticas de salidas por rango de fechas
     */
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
        
        if ($fechaInicio && $fechaFin) {
            $sql .= " WHERE fecha_salida BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $fechaInicio;
            $params[':fecha_fin'] = $fechaFin;
        } elseif ($fechaInicio) {
            $sql .= " WHERE fecha_salida >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        } elseif ($fechaFin) {
            $sql .= " WHERE fecha_salida <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener productos más vendidos
     */
    public function obtenerProductosMasVendidos(int $limite = 10): array
    {
        $sql = "SELECT 
                    v.id as id_variante,
                    v.nombre_variante as nombre_producto,
                    v.sku,
                    v.imagen,
                    v.stock as stock_actual,
                    COUNT(s.id) as num_salidas,
                    SUM(s.cantidad) as total_vendido,
                    SUM(s.total) as ingresos_totales,
                    AVG(s.precio_unitario) as precio_promedio
                FROM variante v
                INNER JOIN salida s ON v.id = s.id_variante
                GROUP BY v.id, v.nombre_variante, v.sku, v.imagen, v.stock
                ORDER BY total_vendido DESC
                LIMIT :limite";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener salidas del día actual
     */
    public function obtenerSalidasHoy(): int
    {
        $sql = "SELECT COUNT(*) 
                FROM salida 
                WHERE DATE(fecha_salida) = CURDATE()";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener salidas por usuario
     */
    public function obtenerSalidasPorUsuario(int $idUsuario): array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    v.imagen,
                    v.nombre_variante as nombre_producto
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                WHERE s.id_usuario = :id_usuario
                ORDER BY s.fecha_salida DESC, s.hora_salida DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener salidas por rango de fechas
     */
    public function obtenerSalidasPorFecha(string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    v.imagen,
                    v.nombre_variante as nombre_producto,
                    u.nombre_real as usuario
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
                WHERE s.fecha_salida BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY s.fecha_salida DESC, s.hora_salida DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar salidas por término (SKU, nombre producto, observaciones)
     */
    public function buscarSalidas(string $termino): array
    {
        $sql = "SELECT 
                    s.*,
                    v.sku,
                    v.imagen,
                    v.nombre_variante as nombre_producto,
                    u.nombre_real as usuario
                FROM salida s
                INNER JOIN variante v ON s.id_variante = v.id
                INNER JOIN usuario u ON s.id_usuario = u.id
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

    /**
     * Obtener resumen de ventas por categoría
     */
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

    /**
     * Verificar si existe una salida
     */
    public function existeSalida(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM salida WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Obtener total de ventas del mes actual
     */
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
}