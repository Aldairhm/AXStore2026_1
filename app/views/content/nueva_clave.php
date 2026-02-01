<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/login.css" />
</head>
<body>
    <div class="container" style="justify-content: center;">
        <div class="login-form active">
            <h2>Crear Nueva Contraseña</h2>
            
            <form method="post" id="formNuevaClave">
                <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
                
                <div class="form-group" style="position: relative;">
                    <label for="clave_nueva">Nueva Contraseña</label>
                    <input type="password" id="clave_nueva" name="clave_nueva" placeholder="Mínimo 8 caracteres" required>
                    <span class="toggle-password" data-target="#clave_nueva" style="position: absolute; right: 10px; top: 38px; cursor: pointer; z-index: 10;">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div id="password-strength" style="margin-top: 5px; font-weight: bold;"></div>
                </div>

                <div class="form-group" style="position: relative;">
                    <label for="clave_confirmar">Confirmar Contraseña</label>
                    <input type="password" id="clave_confirmar" name="clave_confirmar" placeholder="Repita la contraseña" required>
                    <span class="toggle-password" data-target="#clave_confirmar" style="position: absolute; right: 10px; top: 38px; cursor: pointer; z-index: 10;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <button type="submit" class="login-btn">Guardar Contraseña</button>
            </form>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <script src="<?php echo APP_URL; ?>app/ajax/login.js"></script>
</body>
</html>