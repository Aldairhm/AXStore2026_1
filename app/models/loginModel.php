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


    public function obtenerDatosPorUsername(string $username): ?array
    {
        try {
            $usernameEncriptado = Encriptar::openCypher('encrypt', strtolower($username));

            $sql = "SELECT id, nombre_real, username FROM usuario WHERE username = :username LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $usernameEncriptado, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $usuario['username'] = Encriptar::openCypher('decrypt', $usuario['username']);
                return $usuario;
            }
            return null;
        } catch (Throwable $e) {
            error_log("Error obtenerDatosPorUsername: " . $e->getMessage());
            return null;
        }
    }


    public function actualizarContrasenia(int $id, string $password): bool
    {
        try {
            $passwordEncriptado = Encriptar::openCypher('encrypt', $password);

            $sql = "UPDATE usuario 
                    SET password = :password
                    WHERE id = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':password', $passwordEncriptado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
        } catch (Throwable $e) {
            error_log("Error actualizarContrasenia: " . $e->getMessage());
            return false;
        }
    }



    //guardar token 
    public function guardarToken(int $id, string $token): bool
    {
        try {
            $sql = "UPDATE usuario SET token = :token WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Throwable $e) {
            error_log("Error guardarToken: " . $e->getMessage());
            return false;
        }
    }

    // Buscar usuario por token válido
    public function obtenerIdPorToken(string $token): ?int
    {
        try {
            $sql = "SELECT id FROM usuario WHERE token = :token LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ? (int)$res['id'] : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    // Actualizar clave y borrar el token (para que no se use dos veces)
    public function actualizarPasswordYLimpiarToken(int $id, string $password): bool
    {
        try {
            $this->conexion->beginTransaction();

            $passwordEncriptado = Encriptar::openCypher('encrypt', $password);

            // Actualizamos pass y ponemos token en NULL
            $sql = "UPDATE usuario SET password = :password, token = NULL WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':password', $passwordEncriptado);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            return false;
        }
    }
}
