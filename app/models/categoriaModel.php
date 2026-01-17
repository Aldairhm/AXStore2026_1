<?php

    declare(strict_types=1);

    require_once __DIR__ . "/../../config/conexion.php";

    class Categoria{
        private PDO $conexion;

        public function __construct(){
            $this->conexion = Conexion::conectar();
        }

        public function registrar(string $nombre, string $descripcion): ?int {
            $sql = "INSERT INTO categoria (nombre, descripcion) VALUES (:nombre, :descripcion)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);

            return $stmt->execute() ? (int)$this->conexion->lastInsertId() : null;
        }

    }