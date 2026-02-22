<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recuperar Contraseña</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/login.css" />
</head>

<body>

    <!-- Fondo animado -->
    <div class="bg-canvas">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="grid-lines"></div>
    </div>

    <div class="wrapper wrapper--single">

        <div class="form-panel form-panel--full">
            <div class="form-card">

                <!-- Ícono superior -->
                <div class="recover-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>

                <div class="form-header">
                    <span class="form-badge">Recuperación de acceso</span>
                    <h2>Recuperar Contraseña</h2>
                    <p>Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
                </div>

                <form method="post" id="formRecuperar" class="login-form">

                    <div class="field-group">
                        <label for="username">
                            <i class="fas fa-envelope"></i>
                            Correo electrónico
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="email"
                                id="username"
                                name="username"
                                placeholder="nombre@empresa.com"
                                required
                                autocomplete="off">
                            <span class="input-line"></span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Enviar Correo</span>
                        <span class="btn-icon"><i class="fas fa-paper-plane"></i></span>
                    </button>

                </form>

                <p class="form-footer-note">
                    <a href="<?php echo APP_URL; ?>login" class="back-link">
                        <i class="fas fa-arrow-left"></i> Volver al Login
                    </a>
                </p>

            </div>
        </div>

    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <script src="<?php echo APP_URL; ?>app/ajax/login.js"></script>

</body>

</html>