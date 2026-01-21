<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class Login{

    private PDO $conexion;
    public function __construct(){
        $this->conexion = Conexion::conectar();
    }

    public function getLogin(string $username, string $password): ? array{
        try{
            $sql= "SELECT nombre_real,
            username,
            password,
            estado
            FROM usuario
            WHERE username = :username
            AND password = :password
            AND estado = 1
            LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        } catch (PDOException $e) {
            error_log("Error en getLogin: " . $e->getMessage());
            return null;
        }
    }
}
