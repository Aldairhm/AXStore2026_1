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
