<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . "/../models/loginModel.php";
require_once __DIR__ . "/../models/encriptarModel.php";

$loginModel = new Login();

$opcion = isset($_GET["opcion"]) ? trim($_GET["opcion"]) : null;

$response = ["status" => "error", "message" => "Opción inválida"];

try{
    switch ($opcion) {
        case "login":
            $username = trim($_POST["username"] ?? "");
            $password = trim($_POST["password"] ?? "");

            if (!$username || !$password) {
                $response = ["status" => "error", "message" => "Faltan datos obligatorios entrada sistema"];
                break;
            }

            $usernameEncriptado = Encriptar::openCypher('encrypt', $username);
            $passwordEncriptado = Encriptar::openCypher('encrypt', $password);

            $usuario = $loginModel->getLogin($usernameEncriptado, $passwordEncriptado);

            if($usuario){
                session_start();
                $_SESSION['usuario'] = $usuario;
                $response = ["status"=>"success",
                             "message"=>"Inicio de sesión exitoso",
                             "usuario"=>$usuario];
            }else{
                $response = ["status"=>"error",
                             "message"=>"Usuario o contraseña incorrectos"];
            }
            break;

            case "cerrar":
                session_start();
            session_destroy();
            $response = [
                "status"  => "success",
                "message" => "Sesión cerrada exitosamente"
            ];
            break;

        default:
            $response = [
                "status"  => "error",
                "message" => "Opción no válida"
            ];
        break;
    }
} catch (Throwable $e) {
    error_log("Error en loginController: " . $e->getMessage());
    $response = ["status" => "error", "message" => "Error del servidor"];
} finally {
    echo json_encode($response);
}
