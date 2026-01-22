<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/login.css" />
    <style>
        /* Estilos inline rápidos para ajustar el formulario de recuperación */
        .login-form { height: auto; padding-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container" style="justify-content: center;">
        <div class="login-form active">
            <h2>Recuperar Cuenta</h2>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">
                Ingrese su correo electrónico y le enviaremos una nueva contraseña temporal.
            </p>
            <form method="post" id="formRecuperar">
                <div class="form-group">
                    <label for="username">Correo Electrónico</label>
                    <input type="email" id="username" name="username" placeholder="ejemplo@correo.com" required>
                </div>
                
                <button type="submit" class="login-btn">Enviar Nueva Contraseña</button>
                
                <div class="form-footer">
                    <a href="<?php echo APP_URL; ?>login" class="forgot-link">Volver al Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>app/ajax/login.js"></script>
</body>
</html>