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
            $username = strtolower(trim($_POST["username"] ?? ""));
            $password = trim($_POST["password"] ?? "");

            if (!$username || !$password) {
                $response = ["status" => "error", "message" => "Faltan datos obligatorios"];
                break;
            }

            $usernameEncriptado = Encriptar::openCypher('encrypt', $username);
            $passwordEncriptado = Encriptar::openCypher('encrypt', $password);

            // 1. Intentar login normal (Credenciales correctas + Estado activo)
            $usuario = $loginModel->getLogin($usernameEncriptado, $passwordEncriptado);

            if ($usuario) {

            $usuario['username'] = $username;
                session_start();
                $_SESSION['usuario'] = $usuario;
                $response = ["status" => "success", "message" => "Inicio de sesión exitoso", "usuario" => $usuario];
            } else {
                // 2. Si falló, buscamos los datos del usuario para diagnosticar el motivo
                $datosUsuario = $loginModel->obtenerDatosPorUsername($username);

                if ($datosUsuario) {
                    // Caso A: La contraseña es incorrecta
                    if ($datosUsuario['password'] !== $passwordEncriptado) {
                        $response = ["status" => "error", "message" => "Usuario o contraseña incorrectos"];
                    }
                    // Caso B: Contraseña correcta pero cuenta inactiva
                    elseif ((int)$datosUsuario['estado'] !== 1) {
                        $response = [
                            "status" => "inactive",
                            "message" => "Su cuenta se encuentra inactiva. Por favor, contacte a soporte para reactivarla."
                        ];
                    }
                    // Caso C: Cualquier otro error de coincidencia
                    else {
                        $response = ["status" => "error", "message" => "Usuario o contraseña incorrectos"];
                    }
                } else {
                    // El usuario ni siquiera existe
                    $response = ["status" => "error", "message" => "Usuario o contraseña incorrectos"];
                }
            }
            break;

        case "recuperar":
            $username = strtolower($_POST["username"] ?? "");

            if (!$username) {
                $response = ["status" => "error", "message" => "Ingrese su correo electrónico"];
                break;
            }

            // 1. Verificar si el usuario existe
            $datosUsuario = $loginModel->obtenerDatosPorUsername($username);

            if ($datosUsuario) {
                // 2. Generar TOKEN con expiración (Formato: randomHex.timestamp)
                // Usamos 24 bytes (48 hex chars) + separador + 10 digitos tiempo = ~60 chars (seguro para varchar 64/100)
                $caducidad = time() + (5 * 60); // 5 minutos a partir de ahora
                $tokenAleatorio = bin2hex(random_bytes(24));
                $token = $tokenAleatorio . '.' . $caducidad;

                // 3. Guardar el token en la BD
                if ($loginModel->guardarToken($datosUsuario['id'], $token)) {

                    // 4. Crear el enlace de recuperación y el código visual
                    $urlRecuperacion = APP_URL . "nueva_clave?token=" . $token;
                    // Usamos los primeros 6 caracteres del token aleatorio como "código visual" para el correo
                    $codigoVisual = strtoupper(substr($tokenAleatorio, 0, 6));

                    // 5. Enviar Correo con el diseño solicitado
                    $mail = new PHPMailer(true);
                    try {
                        // --- CONFIGURACIÓN SMTP ---
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

                        // Usamos la nueva función para generar el cuerpo del correo
                        $mail->Body = crearCuerpoCorreoConCodigo($datosUsuario['nombre_real'], $urlRecuperacion, $codigoVisual);

                        $mail->send();
                        $response = ["status" => "success", "message" => "Hemos enviado un código de recuperación a su correo."];
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

            // --- VALIDACIÓN DE EXPIRACIÓN ---
            // Separamos el token para buscar la marca de tiempo
            $partesToken = explode('.', $token);
            if (count($partesToken) === 2) {
                $expiracion = (int)$partesToken[1];
                if (time() > $expiracion) {
                    $response = ["status" => "error", "message" => "El enlace de recuperación ha expirado. Solicite uno nuevo."];
                    break;
                }
            } else {
                // Si el token no tiene el formato correcto (ej. tokens antiguos)
                // Puedes decidir si permitirlo o rechazarlo. Aquí lo rechazamos por seguridad.
                $response = ["status" => "error", "message" => "Token inválido o corrupto."];
                break;
            }

            if ($clave1 !== $clave2) {
                $response = ["status" => "error", "message" => "Las contraseñas no coinciden."];
                break;
            }

            // 1. Verificar si el token es válido en la BD y obtener el ID del usuario
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

function crearCuerpoCorreoConCodigo($nombre, $link, $codigo)
{
    $expiracion = date('d/m/Y H:i', time() + (15 * 60));

    return "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                line-height: 1.6; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 40px 20px;
            }
            .email-wrapper {
                max-width: 600px;
                margin: 0 auto;
                background: #ffffff;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 50px 30px;
                text-align: center;
                position: relative;
            }
            .header::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            }
            .logo {
                font-size: 36px;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 10px;
                letter-spacing: 2px;
                text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }
            .header-subtitle {
                color: rgba(255,255,255,0.9);
                font-size: 16px;
                font-weight: 300;
                letter-spacing: 1px;
            }
            .content {
                padding: 50px 40px;
            }
            .greeting {
                font-size: 24px;
                color: #333;
                margin-bottom: 20px;
                font-weight: 600;
            }
            .message {
                color: #666;
                font-size: 16px;
                margin-bottom: 35px;
                line-height: 1.8;
            }
            .code-section {
                background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
                border: 2px dashed #667eea;
                border-radius: 12px;
                padding: 30px;
                margin: 30px 0;
                text-align: center;
            }
            .code-label {
                font-size: 14px;
                color: #666;
                margin-bottom: 15px;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 600;
            }
            .code-display {
                font-size: 32px;
                font-weight: 700;
                color: #667eea;
                letter-spacing: 8px;
                font-family: 'Courier New', monospace;
                padding: 10px;
                background: #ffffff;
                border-radius: 8px;
                display: inline-block;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
            }
            .btn-container {
                text-align: center;
                margin: 40px 0;
            }
            .btn {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #ffffff !important;
                padding: 18px 50px;
                text-decoration: none;
                border-radius: 50px;
                font-size: 16px;
                font-weight: 600;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                letter-spacing: 0.5px;
            }
            .btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
            }
            .divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
                margin: 35px 0;
            }
            .link-section {
                background: #f9f9f9;
                border-radius: 8px;
                padding: 20px;
                margin: 25px 0;
            }
            .link-label {
                font-size: 13px;
                color: #666;
                margin-bottom: 10px;
                font-weight: 600;
            }
            .link-text {
                font-size: 12px;
                color: #667eea;
                word-break: break-all;
                font-family: 'Courier New', monospace;
                line-height: 1.8;
            }
            .warning-box {
                background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
                border-left: 4px solid #ffc107;
                border-radius: 8px;
                padding: 20px;
                margin: 30px 0;
            }
            .warning-title {
                color: #856404;
                font-weight: 700;
                font-size: 16px;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
            }
            .warning-title::before {
                content: '⚠';
                font-size: 20px;
                margin-right: 8px;
            }
            .warning-list {
                list-style: none;
                padding: 0;
            }
            .warning-list li {
                color: #856404;
                font-size: 14px;
                padding: 6px 0;
                padding-left: 25px;
                position: relative;
            }
            .warning-list li::before {
                content: '•';
                position: absolute;
                left: 10px;
                font-weight: bold;
            }
            .expiration-badge {
                display: inline-block;
                background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
                color: #ffffff;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                margin-top: 10px;
                box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            }
            .footer {
                background: linear-gradient(135deg, #2d3436 0%, #000000 100%);
                color: rgba(255,255,255,0.7);
                padding: 40px 30px;
                text-align: center;
            }
            .footer-text {
                font-size: 13px;
                margin: 8px 0;
                line-height: 1.6;
            }
            .footer-brand {
                color: #ffffff;
                font-weight: 600;
                margin-top: 20px;
                font-size: 14px;
            }
            .social-icons {
                margin: 20px 0;
            }
            .signature {
                margin-top: 35px;
                padding-top: 25px;
                border-top: 1px solid #e0e0e0;
                color: #666;
                font-size: 15px;
            }
            .signature strong {
                color: #667eea;
                font-size: 16px;
            }
            @media only screen and (max-width: 600px) {
                .content { padding: 30px 20px; }
                .code-display { font-size: 24px; letter-spacing: 4px; }
                .btn { padding: 15px 35px; font-size: 14px; }
            }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>
                <div class='logo'>🛍️ AXStore</div>
                <div class='header-subtitle'>Restablecimiento de Contraseña</div>
            </div>
            
            <div class='content'>
                <div class='greeting'>¡Hola, $nombre! 👋</div>
                
                <p class='message'>
                    Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en AXStore. 
                    Para continuar con el proceso de forma segura, utiliza el código de verificación que aparece a continuación:
                </p>
                
                <div class='btn-container'>
                    <a href='$link' class='btn'>🔓 Restablecer mi Contraseña</a>
                </div>
                
                <div class='divider'></div>
                
                <div class='link-section'>
                    <div class='link-label'>📎 ¿El botón no funciona?</div>
                    <div class='link-text'>$link</div>
                </div>
                
                <div class='warning-box'>
                    <div class='warning-title'>Información Importante</div>
                    <ul class='warning-list'>
                        <li>Este código expira el <strong>$expiracion</strong></li>
                        <li>Nunca compartas este código con nadie</li>
                        <li>Nuestro equipo jamás te pedirá este código por teléfono o email</li>
                        <li>Si no solicitaste este cambio, ignora este correo y tu cuenta permanecerá segura</li>
                    </ul>
                </div>
                
                <div class='signature'>
                    <p>Con los mejores deseos,</p>
                    <p><strong>El equipo de AXStore</strong> 💼</p>
                </div>
            </div>
            
            <div class='footer'>
                <p class='footer-text'>
                    Este es un correo electrónico automático generado por el sistema de seguridad de AXStore.
                </p>
                <p class='footer-text'>
                    Por favor, no respondas a este mensaje. Si necesitas ayuda, contacta con nuestro equipo de soporte.
                </p>
                <div class='divider' style='background: rgba(255,255,255,0.1); margin: 25px 40px;'></div>
                <p class='footer-brand'>© " . date('Y') . " AXStore. Todos los derechos reservados.</p>
                <p class='footer-text' style='margin-top: 10px; font-size: 11px;'>
                    🔒 Tu seguridad es nuestra prioridad
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}
