<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio de Sesión</title>
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

    <div class="wrapper">

        <!-- Panel izquierdo: Marca -->
        <div class="brand-panel">
            <div class="brand-content">

                <div class="wordmark">
                    <span class="wordmark-label">AX Store<span class="wordmark-dot"></span></span>
                </div>

                <p class="brand-tagline">Plataforma de gestión inteligente</p>

                <div class="brand-divider"></div>

                <ul class="brand-features">
                    <li><i class="fas fa-shield-halved"></i> Acceso seguro y cifrado</li>
                    <li><i class="fas fa-chart-line"></i> Panel de control en tiempo real</li>
                    <li><i class="fas fa-users"></i> Gestión de equipos centralizada</li>
                </ul>

            </div>

            <div class="panel-deco"></div>
        </div>

        <!-- Panel derecho: Formulario -->
        <div class="form-panel">
            <div class="form-card">

                <div class="form-header">
                    <span class="form-badge">Sistema de acceso</span>
                    <h2>Bienvenido de nuevo</h2>
                    <p>Ingresa tus credenciales para continuar</p>
                </div>

                <form method="post" id="formLogin" class="login-form" autocomplete="off">

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

                    <div class="field-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Contraseña
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="off">
                            <span class="input-line"></span>
                            <button type="button" class="toggle-password" data-target="#password" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-meta">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="check-custom"></span>
                            Recordarme
                        </label>
                        <a href="<?php echo APP_URL; ?>recuperar" class="forgot-link">¿Olvidó su contraseña?</a>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Iniciar Sesión</span>
                        <span class="btn-icon"><i class="fas fa-arrow-right"></i></span>
                    </button>

                </form>

                <p class="form-footer-note">
                    ¿Problemas para ingresar? <a href="mailto:soporte@axstore.com">Contactar soporte</a>
                </p>

            </div>
        </div>

    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <script src="<?php echo APP_URL; ?>app/views/assets/js/login.js"></script>
    <script src="<?php echo APP_URL; ?>app/ajax/login.js"></script>

</body>
</html>