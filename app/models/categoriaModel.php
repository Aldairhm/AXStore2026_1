<?php

declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Categoria
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function registrar(string $nombre, string $descripcion): ?int
    {
        $sql = "INSERT INTO categoria (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);

        return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
    }

    public function getCategorias(): array
    {
        $sql = "SELECT * FROM categoria";
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

    public function actualizarCategoria(int $id,string $nombre, string $descripcion): ?int{
        $sql= "UPDATE categoria SET nombre=:nombre, descripcion=:descripcion  WHERE id=:id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount();
    }
}
