<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nueva Contraseña</title>
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
                    <i class="fas fa-lock"></i>
                </div>

                <div class="form-header">
                    <span class="form-badge">Nueva contraseña</span>
                    <h2>Crear Nueva Contraseña</h2>
                    <p>Elige una contraseña segura de mínimo 8 caracteres.</p>
                </div>

                <form method="post" id="formNuevaClave" class="login-form">

                    <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">

                    <div class="field-group">
                        <label for="clave_nueva">
                            <i class="fas fa-lock"></i>
                            Nueva Contraseña
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="clave_nueva"
                                name="clave_nueva"
                                placeholder="Mínimo 8 caracteres"
                                required
                                autocomplete="off">
                            <span class="input-line"></span>
                            <button type="button" class="toggle-password" data-target="#clave_nueva" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Indicador de fortaleza -->
                        <div class="strength-bar">
                            <div class="strength-bar__fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>

                    <div class="field-group">
                        <label for="clave_confirmar">
                            <i class="fas fa-shield-halved"></i>
                            Confirmar Contraseña
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="clave_confirmar"
                                name="clave_confirmar"
                                placeholder="Repita la contraseña"
                                required
                                autocomplete="off">
                            <span class="input-line"></span>
                            <button type="button" class="toggle-password" data-target="#clave_confirmar" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="match-label" id="matchLabel"></span>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Guardar Contraseña</span>
                        <span class="btn-icon"><i class="fas fa-check"></i></span>
                    </button>

                </form>

            </div>
        </div>

    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <script src="<?php echo APP_URL; ?>app/ajax/login.js"></script>

</body>
</html>