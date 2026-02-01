/* ============================================================================
   SISTEMA DE LOGIN - AUTENTICACIÓN Y RECUPERACIÓN DE CONTRASEÑA
   ============================================================================ */

const CTRL_LOGIN = 'app/controllers/loginController.php';

$(document).ready(function () {

    // -------------------------------------------------------------------------
    // 1. CONFIGURACIÓN DE NOTIFICACIONES TOAST
    // -------------------------------------------------------------------------

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // -------------------------------------------------------------------------
    // 2. FUNCIONES DE VALIDACIÓN
    // -------------------------------------------------------------------------

    function limpiarErroresFormulario($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();
        $form.find('.is-valid').removeClass('is-valid');
        $form.find('.valid-feedback').remove();
    }

    function mostrarErrorCampo($campo, mensaje) {
        $campo.addClass('is-invalid').removeClass('is-valid');
        if ($campo.next('.invalid-feedback').length === 0) {
            $campo.after(`<div class="invalid-feedback">${mensaje}</div>`);
        } else {
            $campo.next('.invalid-feedback').text(mensaje);
        }
    }

    function validarEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validarFormularioLogin() {
        limpiarErroresFormulario($('#formLogin'));

        const username = $('#username').val().trim();
        const password = $('#password').val().trim();
        let errores = false;

        // Validar Username
        if (username === '') {
            mostrarErrorCampo($('#username'), 'El usuario o correo es obligatorio');
            errores = true;
        } else if (username.includes('@') && !validarEmail(username)) {
            mostrarErrorCampo($('#username'), 'El formato del correo no es válido');
            errores = true;
        } else if (username.length < 3) {
            mostrarErrorCampo($('#username'), 'Mínimo 3 caracteres');
            errores = true;
        }

        // Validar Password
        if (password === '') {
            mostrarErrorCampo($('#password'), 'La contraseña es obligatoria');
            errores = true;
        } else if (password.length < 8) {
            mostrarErrorCampo($('#password'), 'Mínimo 8 caracteres');
            errores = true;
        }

        if (errores) {
            Toast.fire({
                icon: 'error',
                title: 'Por favor, corrija los errores del formulario'
            });
            return false;
        }

        return true;
    }

    function validarFormularioRecuperar() {
        limpiarErroresFormulario($('#formRecuperar'));

        const username = $('#username').val().trim();

        if (username === '') {
            mostrarErrorCampo($('#username'), 'El correo es obligatorio');
            Toast.fire({
                icon: 'error',
                title: 'Ingrese su correo electrónico'
            });
            return false;
        }

        if (!validarEmail(username)) {
            mostrarErrorCampo($('#username'), 'Formato de correo inválido');
            Swal.fire({
                icon: 'warning',
                title: 'Correo inválido',
                text: 'Por favor, ingrese un correo electrónico válido'
            });
            return false;
        }

        return true;
    }

    function validarFormularioNuevaClave() {
        limpiarErroresFormulario($('#formNuevaClave'));

        const clave1 = $('#clave_nueva').val();
        const clave2 = $('#clave_confirmar').val();
        let errores = false;

        // Validar Contraseña Nueva
        if (clave1 === '') {
            mostrarErrorCampo($('#clave_nueva'), 'La contraseña es obligatoria');
            errores = true;
        } else if (clave1.length < 8) {
            mostrarErrorCampo($('#clave_nueva'), 'Mínimo 8 caracteres');
            errores = true;
        } else if (clave1.trim().length === 0) {
            mostrarErrorCampo($('#clave_nueva'), 'La contraseña no puede contener solo espacios');
            errores = true;
        }

        // Validar Confirmación de Contraseña
        if (clave2 === '') {
            mostrarErrorCampo($('#clave_confirmar'), 'Confirme su contraseña');
            errores = true;
        } else if (clave1 !== clave2) {
            mostrarErrorCampo($('#clave_confirmar'), 'Las contraseñas no coinciden');
            errores = true;
        }

        if (errores) {
            Swal.fire({
                icon: 'warning',
                title: 'Datos incompletos',
                text: 'Por favor, corrija los errores del formulario'
            });
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // 3. FORMULARIO DE INICIO DE SESIÓN
    // -------------------------------------------------------------------------

    $('#formLogin').on('submit', function (e) {
        e.preventDefault();

        // Validar antes de enviar
        if (!validarFormularioLogin()) {
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        const textoOriginal = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Verificando...').prop('disabled', true);

        const formData = new FormData(this);

        $.ajax({
            url: `${CTRL_LOGIN}?opcion=login`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function () { window.location.href = 'home'; }, 1600);

                } else if (response.status === 'inactive') {
                    // --- ESTA ES LA ALERTA DE WARNING ---
                    $btn.html(textoOriginal).prop('disabled', false);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Usuario Inactivo',
                        text: response.message,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#f8bb86' // Color naranja warning
                    });

                } else {
                    // Error normal (credenciales incorrectas)
                    $btn.html(textoOriginal).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso Denegado',
                        text: response.message,
                        confirmButtonText: 'Intentar de nuevo'
                    });
                }
            },
            error: function (xhr) {
                $btn.html(textoOriginal).prop('disabled', false);
                console.error('Error de conexión:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: 'No se pudo conectar. Intente más tarde.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Limpiar errores al escribir en los campos
    $('#username, #password').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback, .valid-feedback').remove();
    });

    // -------------------------------------------------------------------------
    // 4. FORMULARIO DE RECUPERAR CONTRASEÑA
    // -------------------------------------------------------------------------

    $('#formRecuperar').on('submit', function (e) {
        e.preventDefault();

        // Validar antes de enviar
        if (!validarFormularioRecuperar()) {
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        const textoOriginal = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Enviando...').prop('disabled', true);

        const formData = new FormData(this);

        $.ajax({
            url: `${CTRL_LOGIN}?opcion=recuperar`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $btn.html(textoOriginal).prop('disabled', false);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Correo Enviado!',
                        html: `
                            <p class="mb-2">${response.message}</p>
                            <p class="text-muted mb-0">
                                <small><i class="fas fa-info-circle"></i> Revisa tu bandeja de entrada y carpeta de spam</small>
                            </p>
                        `,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'login';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo procesar la solicitud',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function (xhr) {
                $btn.html(textoOriginal).prop('disabled', false);
                console.error('Error de conexión:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Limpiar errores al escribir
    $('#username_recuperar').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback, .valid-feedback').remove();
    });

    // -------------------------------------------------------------------------
    // 5. FORMULARIO DE CAMBIAR CONTRASEÑA
    // -------------------------------------------------------------------------

    $('#formNuevaClave').on('submit', function (e) {
        e.preventDefault();

        // Validar antes de enviar
        if (!validarFormularioNuevaClave()) {
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        const textoOriginal = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

        const formData = new FormData(this);

        $.ajax({
            url: `${CTRL_LOGIN}?opcion=cambiar_clave`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $btn.html(textoOriginal).prop('disabled', false);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña Actualizada!',
                        text: response.message || 'Tu contraseña ha sido cambiada correctamente',
                        confirmButtonText: 'Iniciar Sesión',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'login';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'El enlace expiró o es inválido',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function (xhr) {
                $btn.html(textoOriginal).prop('disabled', false);
                console.error('Error de conexión:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: 'No se pudo actualizar la contraseña',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Limpiar errores al escribir
    $('#clave_nueva, #clave_confirmar').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback, .valid-feedback').remove();
    });

    // Validación en tiempo real de coincidencia de contraseñas
    $('#clave_confirmar').on('input', function () {
        const clave1 = $('#clave_nueva').val();
        const clave2 = $(this).val();

        if (clave2.length > 0) {
            if (clave1 === clave2) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
                if ($(this).next('.valid-feedback').length === 0) {
                    $(this).after('<div class="valid-feedback"><i class="fas fa-check-circle"></i> Las contraseñas coinciden</div>');
                }
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                $(this).next('.valid-feedback').remove();
                if ($(this).next('.invalid-feedback').length === 0) {
                    $(this).after('<div class="invalid-feedback"><i class="fas fa-times-circle"></i> Las contraseñas no coinciden</div>');
                }
            }
        } else {
            $(this).removeClass('is-invalid is-valid');
            $(this).next('.invalid-feedback, .valid-feedback').remove();
        }
    });

    // -------------------------------------------------------------------------
    // 6. FUNCIONALIDADES ADICIONALES
    // -------------------------------------------------------------------------

    // Toggle para mostrar/ocultar contraseñas
    $('.toggle-password').on('click', function () {
        const targetId = $(this).attr('data-target');
        const $input = $(targetId);
        const $icon = $(this).find('i');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).attr('title', 'Ocultar contraseña');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).attr('title', 'Mostrar contraseña');
        }
    });

    // Prevenir espacios en blanco al inicio y final
    $('input[type="text"], input[type="email"], input[type="password"]').on('blur', function () {
        const valorLimpio = $(this).val().trim();
        $(this).val(valorLimpio);
    });

    // Detectar Enter en campos para enviar formulario
    $('input').on('keypress', function (e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Indicador de fortaleza de contraseña (opcional)
    $('#clave_nueva').on('input', function () {
        const password = $(this).val();
        let fuerza = 0;
        let mensaje = '';
        let colorClass = '';

        if (password.length >= 6) fuerza++;
        if (password.length >= 8) fuerza++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) fuerza++;
        if (/\d/.test(password)) fuerza++;
        if (/[^a-zA-Z\d]/.test(password)) fuerza++;

        switch (fuerza) {
            case 0:
            case 1:
                mensaje = 'Débil';
                colorClass = 'text-danger';
                break;
            case 2:
            case 3:
                mensaje = 'Media';
                colorClass = 'text-warning';
                break;
            case 4:
            case 5:
                mensaje = 'Fuerte';
                colorClass = 'text-success';
                break;
        }

        // Mostrar indicador si existe el contenedor
        if ($('#password-strength').length > 0 && password.length > 0) {
            $('#password-strength')
                .removeClass('text-danger text-warning text-success')
                .addClass(colorClass)
                .html(`<small><i class="fas fa-shield-alt"></i> Fortaleza: <strong>${mensaje}</strong></small>`);
        } else if (password.length === 0) {
            $('#password-strength').html('');
        }
    });

    // -------------------------------------------------------------------------
    // 7. EFECTOS VISUALES Y ANIMACIONES
    // -------------------------------------------------------------------------

    // Efecto de focus en inputs
    $('input').on('focus', function () {
        $(this).parent().addClass('input-focused');
    }).on('blur', function () {
        $(this).parent().removeClass('input-focused');
    });

    // Animación suave al cargar la página
    $('.login-form, .recover-form, .new-password-form').hide().fadeIn(800);

});