<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";
require_once __DIR__ . "/encriptarModel.php";

class Usuario{

    private PDO $conexion;
    public function __construct(){
        $this->conexion = Conexion::conectar();
    }

    public function agregar(
    string $nombre_real, 
    string $username,
    string $password,
    string $rol,
    int    $estado): bool{

        try{
            $this->conexion->beginTransaction();

            $usernameEncriptado = Encriptar::openCypher('encrypt',$username);
            $passwordEncriptado = Encriptar::openCypher('encrypt',$password);

            $sql = "INSERT INTO usuario(nombre_real,username,password,rol,estado) 
                    VALUES (:nombre_real, :username, :password, :rol, :estado)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre_real', $nombre_real, PDO::PARAM_STR);
            $stmt->bindParam(':username', $usernameEncriptado, PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordEncriptado, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        }catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error registrar usuario: " . $e->getMessage());
            
            throw $e;
        }
}


    public function getUsuarios(): array{
        try{
            $sql = "SELECT id,nombre_real,username,password,rol,estado
                    FROM usuario";

            $stmt = $this->conexion->query($sql);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($usuarios as &$usuario){
                $usuario['username'] = Encriptar::openCypher('decrypt',$usuario['username']);
                $usuario['password'] = Encriptar::openCypher('decrypt',$usuario['password']);
            }
            return $usuarios;
        } catch (Throwable $e) {
            error_log("Error getUsuarios: " . $e->getMessage());
            return [];
        }
    }


    public function getUsuariosSelect(): array{
        try{
            $sql = "SELECT id,nombre_real FROM usuario WHERE estado = 1";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (Throwable $e) {
        error_log("Error getUsuariosSelect: " . $e->getMessage());
        return [];
        }
    }


    public function getUsuarioById(int $id): ? array{
        try{
            $sql = "SELECT id,nombre_real,username,password,rol,estado
                    FROM usuario
                    WHERE id = :id
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                $usuario['username'] = Encriptar::openCypher('decrypt',$usuario['username']);
                $usuario['password'] = Encriptar::openCypher('decrypt',$usuario['password']);
                return $usuario;
            } else {
                return null;
            }
        } catch (Throwable $e) {
            error_log("Error getUsuarioById: " . $e->getMessage());
            return null;
        }
    }

    public function actualizar(
        int $id,
        string $nombre_real,
        string $username,
        string $password,
        string $rol,
        int   $estado): bool{

            try{
                $this->conexion->beginTransaction();

                $usernameEncriptado = Encriptar::openCypher('encrypt',$username);
                $passwordEncriptado = Encriptar::openCypher('encrypt',$password);

                $passwordEncriptado = null;
                if($password !== '' && $password !== null){
                    $passwordEncriptado = Encriptar::openCypher('encrypt',$password);
                }

                $sql = "UPDATE usuario 
                        SET nombre_real = :nombre_real,
                            username = :username,
                            password = COALESCE(:password, password),
                            rol = :rol,
                            estado = :estado
                        WHERE id = :id";

                        $stmt = $this->conexion->prepare($sql);
                        $stmt->bindParam(':nombre_real', $nombre_real, PDO::PARAM_STR); 
                        $stmt->bindParam(':username', $usernameEncriptado, PDO::PARAM_STR);
                        $stmt->bindParam(':password', $passwordEncriptado, PDO::PARAM_STR);
                        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);     
                        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $this->conexion->commit();
                        return true;
            }catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar usuario: " . $e->getMessage());
            
            throw $e;
        }

        }

    
    public function actualizarContrasenia(int $id, string $password): bool{
        try{
            $passwordEncriptado = Encriptar::openCypher('encrypt',$password);

            $sql = "UPDATE usuario 
                    SET password = :password
                    WHERE id = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':password', $passwordEncriptado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            error_log("Error actualizarContrasenia: " . $e->getMessage());
            return false;
        }
    }



    public function eliminar(int $id): bool{
        try{
            $this->conexion->beginTransaction();
            $sql = "DELETE FROM usuario WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar usuario: " . $e->getMessage());
         
            throw $e;
        }
    }


    public function validarCredenciales(string $username, string $password): ? array{
        try{
            $usernameEncriptado = Encriptar::openCypher('encrypt',$username);
            $passwordEncriptado = Encriptar::openCypher('encrypt',$password);

            $sql = "SELECT id,nombre_real,username,rol,estado
                    FROM usuario
                    WHERE username = :username
                    AND password = :password
                    AND estado = 1
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $usernameEncriptado, PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordEncriptado, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if($usuario){
                $usuario['username'] = Encriptar::openCypher('decrypt',$usuario['username']);
                $usuario['password'] = Encriptar::openCypher('decrypt',$usuario['password']);
                return $usuario;
            }
            return null;
        } catch (Throwable $e) {
            error_log("Error validarCredenciales: " . $e->getMessage());
            return null;
        }
    }



    public function existeCorreo(string $username):bool{
        try{
            $usernameEncriptado = Encriptar::openCypher('encrypt',$username);

            $sql = "SELECT COUNT(*) as count
                    FROM usuario
                    WHERE username = :username";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $usernameEncriptado, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['count'] > 0;
        } catch (Throwable $e) {
            error_log("Error existeCorreo: " . $e->getMessage());
            return false;
        }
    }



    public function cambiarEstado(int $id, int $estado): bool{
        try{
            $sql = "UPDATE usuario 
                    SET estado = :estado
                    WHERE id = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (Throwable $e) {
            error_log("Error cambiarEstado: " . $e->getMessage());
            return false;
        }
    }
    
}