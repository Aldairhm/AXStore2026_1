$("#formLogin").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData($("#formLogin")[0]);

    $.ajax({
        url: "app/controllers/loginController.php?opcion=login",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                // Si PHP manda la URL:
                if (response.url) {
                    location.href = response.url;
                } else {
                    // Plan B: a "inicio" (ruta a tu menú principal)
                    location.href = "home";
                }
            } else {
                mostrarError(response.message || "Credenciales incorrectas");
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            mostrarError("Error en el servidor. Intente más tarde.");
        }
    });
});


$("#formRecuperar").on("submit", function (e) {
    e.preventDefault();
    
    // Feedback visual de carga
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
                alert(response.message);
                window.location.href = "login"; // Redirigir al login
            } else {
                alert(response.message || "Error al recuperar");
            }
        },
        error: function (xhr) {
            btn.text(textoOriginal).prop("disabled", false);
            console.error(xhr.responseText);
            alert("Error de conexión con el servidor.");
        }
    });
});


/* Añadir al final de app/ajax/login.js */

$("#formNuevaClave").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "app/controllers/loginController.php?opcion=cambiar_clave", // Nueva opción
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                alert(response.message);
                window.location.href = "login"; // Mandar al login al terminar
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert("Error del servidor");
        }
    });
});
