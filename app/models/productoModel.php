<?php

declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Producto
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function getProductoPorId(int $id_producto): ?array
    {
        $sql = "SELECT p.id,p.nombre, p.descripcion,p.estado, c.nombre AS categoria, c.id AS id_categoria FROM producto p INNER JOIN categoria c  ON p.id_categoria=c.id WHERE p.id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado : null;
    }

    public function actualizarProducto(int $id, string $nombre, string $descripcion, int $categoria, int $estado): ?int
    {
        $sql = "UPDATE producto SET id_categoria=:categoria, nombre=:nombre, descripcion=:descripcion, estado=:estado WHERE id=:id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getVariantesPorProducto(int $id_producto): array
    {
        $sql = "SELECT p.descripcion, v.nombre_variante as nombre, v.precio, v.stock, v.imagen, c.nombre as categoria FROM producto p INNER JOIN variante v ON p.id=v.id_producto INNER JOIN categoria c ON p.id_categoria= c.id WHERE p.id= :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductosFull(): array
    {
        $sql = "SELECT p.id,p.nombre,p.estado, c.nombre as categoria FROM producto p INNER JOIN categoria c ON p.id_categoria = c.id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarAtributo(string $nombre): ?int
    {
        $sql = "INSERT INTO atributo (nombre) VALUES (:nombre)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);

        return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
    }

    // Corrección del typo en el BindParam
    public function registrarProducto(string $nombre, string $descripcion, int $categoria, int $estado): ?int
    {
        $sql = "INSERT INTO producto (id_categoria, nombre, descripcion, estado) VALUES (:categoria, :nombre, :descripcion, :estado)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT); // CORREGIDO: :estado en lugar de :estadp

        return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
    }

    //INSERT DE ProductoAtributo
    public function registrarProductoAtributo(int $id_producto, int $id_atributo): ?int
    {
        $sql = "INSERT INTO productoatributo (id_producto, id_atributo) VALUES (:id_producto, :id_atributo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':id_atributo', $id_atributo, PDO::PARAM_INT);

        return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
    }

    public function registrarVariante(int $id_producto, string $nombre, float $precio, int $stock, string $imagen): ?int
    {
        $sql = "INSERT INTO variante (id_producto, nombre_variante, precio, stock, imagen) VALUES (:id_producto, :nombre, :precio, :stock, :imagen)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $imagen, PDO::PARAM_STR);

        return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
    }

    public function obtenerIdAtributoPorNombre(string $nombre): ?int
    {
        $sql = "SELECT id FROM atributo WHERE nombre = :nombre";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (int)$resultado['id'] : null;
    }

    public function registrarVarianteValor(int $id_variante, int $id_atributo, string $valor): ?int
    {
        $sql = "INSERT INTO variantevalor (id_variante, id_atributo, valor) VALUES (:id_variante, :id_atributo, :valor)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_variante', $id_variante, PDO::PARAM_INT);
        $stmt->bindParam(':id_atributo', $id_atributo, PDO::PARAM_INT);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);

        return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
    }

    public function getAtributos(): array
    {
        $sql = "SELECT * FROM atributo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarCategoria(int $id): array
    {
        $sql = "SELECT * FROM categoria WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // fetch() devuelve solo la fila encontrada o false si no existe
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado : [];
    }

    public function actualizarCategoria(int $id, string $nombre, string $descripcion): ?int
    {
        $sql = "UPDATE categoria SET nombre=:nombre, descripcion=:descripcion  WHERE id=:id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount();
    }
}
