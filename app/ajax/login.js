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
    // 2. RECORDARME — Cargar email guardado al iniciar
    // -------------------------------------------------------------------------

    const emailGuardado = localStorage.getItem('recordarme_email');
    if (emailGuardado && $('#username').length) {
        $('#username').val(emailGuardado);
        // Marcar el checkbox visualmente
        $('#formLogin input[name="remember"]').prop('checked', true);
        const box = document.querySelector('.check-custom');
        if (box) {
            box.style.background = 'hsl(320,60%,52%)';
            box.style.borderColor = 'hsl(320,60%,52%)';
            box.innerHTML = '<svg width="8" height="10" viewBox="0 0 8 10"><polyline points="1,5 3,8 7,2" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>';
        }
    }

    // -------------------------------------------------------------------------
    // 3. FUNCIONES DE VALIDACIÓN
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

        if (password === '') {
            mostrarErrorCampo($('#password'), 'La contraseña es obligatoria');
            errores = true;
        } else if (password.length < 8) {
            mostrarErrorCampo($('#password'), 'Mínimo 8 caracteres');
            errores = true;
        }

        if (errores) {
            Toast.fire({ icon: 'error', title: 'Por favor, corrija los errores del formulario' });
            return false;
        }

        return true;
    }

    function validarFormularioRecuperar() {
        limpiarErroresFormulario($('#formRecuperar'));

        const username = $('#username').val().trim();

        if (username === '') {
            mostrarErrorCampo($('#username'), 'El correo es obligatorio');
            Toast.fire({ icon: 'error', title: 'Ingrese su correo electrónico' });
            return false;
        }

        if (!validarEmail(username)) {
            mostrarErrorCampo($('#username'), 'Formato de correo inválido');
            Swal.fire({ icon: 'warning', title: 'Correo inválido', text: 'Por favor, ingrese un correo electrónico válido' });
            return false;
        }

        return true;
    }

    function validarFormularioNuevaClave() {
        limpiarErroresFormulario($('#formNuevaClave'));

        const clave1 = $('#clave_nueva').val();
        const clave2 = $('#clave_confirmar').val();
        let errores = false;

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

        if (clave2 === '') {
            mostrarErrorCampo($('#clave_confirmar'), 'Confirme su contraseña');
            errores = true;
        } else if (clave1 !== clave2) {
            mostrarErrorCampo($('#clave_confirmar'), 'Las contraseñas no coinciden');
            errores = true;
        }

        if (errores) {
            Swal.fire({ icon: 'warning', title: 'Datos incompletos', text: 'Por favor, corrija los errores del formulario' });
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // 4. FORMULARIO DE INICIO DE SESIÓN
    // -------------------------------------------------------------------------

    $('#formLogin').on('submit', function (e) {
        e.preventDefault();

        if (!validarFormularioLogin()) return;

        const $btn = $(this).find('button[type="submit"]');
        const textoOriginal = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Verificando...').prop('disabled', true);

        const formData = new FormData(this);

        // ── RECORDARME: leer si el checkbox está marcado ──
        const recordarme = $('input[name="remember"]').is(':checked');
        const emailActual = $('#username').val().trim();

        $.ajax({
            url: `${CTRL_LOGIN}?opcion=login`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {

                    // ── RECORDARME: guardar o borrar email según elección ──
                    if (recordarme) {
                        localStorage.setItem('recordarme_email', emailActual);
                    } else {
                        localStorage.removeItem('recordarme_email');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function () { window.location.href = 'home'; }, 1600);

                } else if (response.status === 'inactive') {
                    $btn.html(textoOriginal).prop('disabled', false);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Usuario Inactivo',
                        text: response.message,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#f8bb86'
                    });

                } else {
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

    $('#username, #password').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback, .valid-feedback').remove();
    });

    // -------------------------------------------------------------------------
    // 5. FORMULARIO DE RECUPERAR CONTRASEÑA
    // -------------------------------------------------------------------------

    $('#formRecuperar').on('submit', function (e) {
        e.preventDefault();

        if (!validarFormularioRecuperar()) return;

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
                    }).then(() => { window.location.href = 'login'; });
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

    $('#username_recuperar').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback, .valid-feedback').remove();
    });

    // -------------------------------------------------------------------------
    // 6. FORMULARIO DE CAMBIAR CONTRASEÑA
    // -------------------------------------------------------------------------

    $('#formNuevaClave').on('submit', function (e) {
        e.preventDefault();

        if (!validarFormularioNuevaClave()) return;

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
                    }).then(() => { window.location.href = 'login'; });
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

    $('#clave_nueva, #clave_confirmar').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback, .valid-feedback').remove();
    });

    // -------------------------------------------------------------------------
    // 7. FUNCIONALIDADES ADICIONALES
    // -------------------------------------------------------------------------

    // Toggle mostrar/ocultar contraseña
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

    // Prevenir espacios al inicio/final
    $('input[type="text"], input[type="email"], input[type="password"]').on('blur', function () {
        $(this).val($(this).val().trim());
    });

    // Enter en campos para enviar formulario
    $('input').on('keypress', function (e) {
        if (e.which === 13) $(this).closest('form').submit();
    });

    // ── INDICADOR DE FORTALEZA (solo en página nueva_clave) ──
    // Guardado con null-check para evitar errores en otras páginas
    const inputClave    = document.getElementById('clave_nueva');
    const strengthFill  = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const inputConfirm  = document.getElementById('clave_confirmar');
    const matchLabel    = document.getElementById('matchLabel');

    if (inputClave && strengthFill && strengthLabel) {
        inputClave.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 8)          score++;
            if (/[A-Z]/.test(val))        score++;
            if (/[0-9]/.test(val))        score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { w: '0%',    color: 'transparent', text: '' },
                { w: '25%',   color: '#e53e3e',      text: 'Muy débil' },
                { w: '50%',   color: '#dd6b20',      text: 'Débil' },
                { w: '75%',   color: '#d69e2e',      text: 'Moderada' },
                { w: '100%',  color: '#38a169',      text: 'Fuerte' },
            ];

            strengthFill.style.width      = levels[score].w;
            strengthFill.style.background = levels[score].color;
            strengthLabel.textContent     = levels[score].text;
            strengthLabel.style.color     = levels[score].color;
        });
    }

    if (inputConfirm && matchLabel && inputClave) {
        inputConfirm.addEventListener('input', function () {
            if (this.value === inputClave.value) {
                matchLabel.textContent = '✓ Las contraseñas coinciden';
                matchLabel.style.color = '#38a169';
            } else {
                matchLabel.textContent = '✗ Las contraseñas no coinciden';
                matchLabel.style.color = '#e53e3e';
            }
        });
    }

    // -------------------------------------------------------------------------
    // 8. EFECTOS VISUALES
    // -------------------------------------------------------------------------

    $('input').on('focus', function () {
        $(this).parent().addClass('input-focused');
    }).on('blur', function () {
        $(this).parent().removeClass('input-focused');
    });

    $('.login-form, .recover-form, .new-password-form').hide().fadeIn(800);

});