<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../../config/app.php";
require_once __DIR__ . "/../models/loginModel.php";
require_once __DIR__ . "/../models/encriptarModel.php";

$loginModel = new Login();

$opcion = isset($_GET["opcion"]) ? trim($_GET["opcion"]) : null;

$response = ["status" => "error", "message" => "Opción inválida"];

try {
    switch ($opcion) {
        case "login":
            $username = trim($_POST["username"] ?? "");
            $password = trim($_POST["password"] ?? "");

            if (!$username || !$password) {
                $response = ["status" => "error", "message" => "Faltan datos obligatorios"];
                break;
            }

            $usernameEncriptado = Encriptar::openCypher('encrypt', $username);
            $passwordEncriptado = Encriptar::openCypher('encrypt', $password);

            $usuario = $loginModel->getLogin($usernameEncriptado, $passwordEncriptado);

            if ($usuario) {
                session_start();
                $_SESSION['usuario'] = $usuario;
                $response = ["status" => "success", "message" => "Inicio de sesión exitoso", "usuario" => $usuario];
            } else {
                $response = ["status" => "error", "message" => "Usuario o contraseña incorrectos"];
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
                // 2. Generar TOKEN único en lugar de contraseña
                $token = bin2hex(random_bytes(32)); // Genera un token largo y seguro

                // 3. Guardar el token en la BD (asegúrate de que loginModel tenga el método guardarToken)
                if ($loginModel->guardarToken($datosUsuario['id'], $token)) {

                    // 4. Crear el enlace de recuperación
                    $urlRecuperacion = APP_URL . "nueva_clave?token=" . $token;

                    // 5. Enviar Correo con el LINK
                    $mail = new PHPMailer(true);
                    try {
                        // --- CONFIGURACIÓN SMTP (Coloca tus datos reales aquí) ---
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'storeax8@gmail.com';
                        $mail->Password   = 'aywl cuou exoi auzg';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('no-reply@axstore.com', 'Soporte AXStore');
                        $mail->addAddress($username, $datosUsuario['nombre_real']);

                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = 'Recuperar acceso - AXStore';
                        $mail->Body    = "
                            <h1>Restablecimiento de Contraseña</h1>
                            <p>Hola <b>{$datosUsuario['nombre_real']}</b>,</p>
                            <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva:</p>
                            <p>
                                <a href='{$urlRecuperacion}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Crear nueva contraseña</a>
                            </p>
                            <p>O copia y pega este enlace en tu navegador:</p>
                            <p>{$urlRecuperacion}</p>
                            <p><small>Si no solicitaste esto, ignora este correo.</small></p>
                        ";

                        $mail->send();
                        $response = ["status" => "success", "message" => "Hemos enviado un enlace a su correo."];
                    } catch (Exception $e) {
                        $response = ["status" => "error", "message" => "Error al enviar correo: {$mail->ErrorInfo}"];
                    }
                } else {
                    $response = ["status" => "error", "message" => "Error al generar el token de seguridad."];
                }
            } else {
                $response = ["status" => "error", "message" => "El correo no está registrado."];
            }
            break;

        case "cambiar_clave":
            $token = trim($_POST["token"] ?? "");
            $clave1 = trim($_POST["clave_nueva"] ?? "");
            $clave2 = trim($_POST["clave_confirmar"] ?? "");

            if (!$token || !$clave1 || !$clave2) {
                $response = ["status" => "error", "message" => "Faltan datos."];
                break;
            }

            if ($clave1 !== $clave2) {
                $response = ["status" => "error", "message" => "Las contraseñas no coinciden."];
                break;
            }

            // 1. Verificar si el token es válido y obtener el ID del usuario
            $idUsuario = $loginModel->obtenerIdPorToken($token);

            if ($idUsuario) {
                // 2. Actualizar la contraseña y borrar el token
                if ($loginModel->actualizarPasswordYLimpiarToken($idUsuario, $clave1)) {
                    $response = ["status" => "success", "message" => "Contraseña actualizada correctamente. Ahora puede iniciar sesión."];
                } else {
                    $response = ["status" => "error", "message" => "Error al actualizar la contraseña."];
                }
            } else {
                $response = ["status" => "error", "message" => "El enlace es inválido o ya fue utilizado."];
            }
            break;

        case "cerrar":
            session_start();
            session_destroy();
            $response = ["status" => "success", "message" => "Sesión cerrada"];
            break;

        default:
            $response = ["status" => "error", "message" => "Opción no válida"];
            break;
    }
} catch (Throwable $e) {
    error_log("Error en loginController: " . $e->getMessage());
    $response = ["status" => "error", "message" => "Error interno del servidor"];
} finally {
    echo json_encode($response);
}
