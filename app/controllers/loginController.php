<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

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


            case "recuperar":
            $username = trim($_POST["username"] ?? "");

            if (!$username) {
                $response = ["status" => "error", "message" => "Ingrese su correo electrónico"];
                break;
            }

            // 1. Verificar si el usuario existe
            $datosUsuario = $loginModel->obtenerDatosPorUsername($username);

            if ($datosUsuario) {
                // 2. Generar nueva contraseña aleatoria (8 caracteres)
                $nuevaPass = substr(md5(uniqid(mt_rand(), true)), 0, 8);
                
                // 3. Actualizar contraseña en la BD
                if ($loginModel->actualizarContrasenia($datosUsuario['id'], $nuevaPass)) {
                    
                    // 4. Enviar Correo con PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // CONFIGURACIÓN SMTP (¡CAMBIA ESTO!)
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Ej: smtp.gmail.com
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'tu_correo@gmail.com'; 
                        $mail->Password   = 'tu_contraseña_de_aplicacion'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                        $mail->Port       = 587; 

                        // Destinatarios
                        $mail->setFrom('no-reply@axstore.com', 'Soporte AXStore');
                        $mail->addAddress($username, $datosUsuario['nombre_real']); 

                        // Contenido
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperacion de Contrasena - AXStore';
                        $mail->Body    = "
                            <h1>Restablecimiento de Contraseña</h1>
                            <p>Hola <b>{$datosUsuario['nombre_real']}</b>,</p>
                            <p>Has solicitado recuperar tu contraseña. Tu nueva contraseña temporal es:</p>
                            <h2 style='background: #eee; padding: 10px; display: inline-block;'>{$nuevaPass}</h2>
                            <p>Por favor, inicia sesión y cámbiala lo antes posible.</p>
                        ";

                        $mail->send();
                        $response = ["status" => "success", "message" => "Correo enviado. Verifique su bandeja de entrada (y spam)."];
                    } catch (Exception $e) {
                        $response = ["status" => "error", "message" => "Error al enviar correo: {$mail->ErrorInfo}"];
                    }
                } else {
                    $response = ["status" => "error", "message" => "Error al actualizar la contraseña en la base de datos."];
                }
            } else {
                $response = ["status" => "error", "message" => "El correo no está registrado en el sistema."];
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
