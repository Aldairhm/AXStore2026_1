/* Archivo: app/ajax/login.js */

$(document).ready(function() {

    // -------------------------------------------------------------------------
    // CONFIGURACIÓN DE SWEETALERT (Igual que en usuario.js)
    // -------------------------------------------------------------------------
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // -------------------------------------------------------------------------
    // 1. LOGIN
    // -------------------------------------------------------------------------
    $("#formLogin").on("submit", function (e) {
        e.preventDefault();
        
        // Efecto visual de carga en el botón (opcional)
        var btn = $(this).find("button[type='submit']");
        var textoOriginal = btn.text();
        btn.text("Verificando...").prop("disabled", true);

        var formData = new FormData(this);

        $.ajax({
            url: "app/controllers/loginController.php?opcion=login",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    // Alerta tipo Toast (menos intrusiva para login)
                    Toast.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: response.message || 'Inicio de sesión exitoso'
                    });

                    // Pequeña pausa para que se vea la alerta antes de redirigir
                    setTimeout(function() {
                        if (response.url) {
                            location.href = response.url;
                        } else {
                            location.href = "home"; // Ruta por defecto
                        }
                    }, 1500);

                } else {
                    // Error: restaurar botón y mostrar alerta
                    btn.text(textoOriginal).prop("disabled", false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso denegado',
                        text: response.message || "Usuario o contraseña incorrectos"
                    });
                }
            },
            error: function (xhr) {
                btn.text(textoOriginal).prop("disabled", false);
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: 'Ocurrió un problema al intentar conectar. Intente más tarde.'
                });
            }
        });
    });

    // -------------------------------------------------------------------------
    // 2. RECUPERAR CONTRASEÑA
    // -------------------------------------------------------------------------
    $("#formRecuperar").on("submit", function (e) {
        e.preventDefault();
        
        var btn = $(this).find("button[type='submit']");
        var textoOriginal = btn.text();
        btn.text("Enviando...").prop("disabled", true);

        var formData = new FormData(this);

        $.ajax({
            url: "app/controllers/loginController.php?opcion=recuperar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                btn.text(textoOriginal).prop("disabled", false);
                
                if (response.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Correo Enviado!',
                        text: response.message,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed || result.isDismissed) {
                            window.location.href = "login"; // Redirigir al login
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || "No se pudo procesar la solicitud."
                    });
                }
            },
            error: function (xhr) {
                btn.text(textoOriginal).prop("disabled", false);
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.'
                });
            }
        });
    });

    // -------------------------------------------------------------------------
    // 3. CAMBIAR CONTRASEÑA (NUEVA CLAVE)
    // -------------------------------------------------------------------------
    $("#formNuevaClave").on("submit", function (e) {
        e.preventDefault();
        
        var btn = $(this).find("button[type='submit']");
        var textoOriginal = btn.text();
        btn.text("Guardando...").prop("disabled", true);

        var formData = new FormData(this);

        $.ajax({
            url: "app/controllers/loginController.php?opcion=cambiar_clave",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                btn.text(textoOriginal).prop("disabled", false);

                if (response.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña Actualizada!',
                        text: response.message,
                        confirmButtonText: 'Iniciar Sesión',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = "login";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Las contraseñas no coinciden o el enlace expiró.'
                    });
                }
            },
            error: function () {
                btn.text(textoOriginal).prop("disabled", false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: 'No se pudo actualizar la contraseña en este momento.'
                });
            }
        });
    });

});