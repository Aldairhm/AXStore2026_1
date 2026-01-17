$(document).ready(function () {
  $("#formCategoria").on("submit", function (e) {
    e.preventDefault();

    let nombre = $("#nombre").val().trim();
    let descripcion = $("#descripcion").val().trim();
    let idActual = $("#id_categoria").val();

    //ponemos las validaciones basicas
    if (nombre.length < 5) {
      mostrarError("El nombre de la categoría es muy corto.");
      return;
    }

    if (nombre.length > 200) {
      mostrarError("El nombre de la categoría es muy largo.");
      return;
    }

    if (descripcion.length < 10) {
      mostrarError("La descripción de la categoría es muy corta.");
      return;
    }

    //tomamos los datos y mandamos al metodo
    let datos = $(this).serialize();
    editar_Y_registrarCategoria(datos, idActual);
  });
});

function editar_Y_registrarCategoria(formData, id) {
    // 1. Usamos una URL limpia (sin parámetros ?)
    let url = "app/controllers/categoriaController.php";
    
    // 2. Comparamos con == (o convertimos a número) para que "0" sea igual a 0
    let accionExtra = (id == 0) ? "registrarCategoria" : "editarCategoria";
    
    // 3. Concatenamos la acción a los datos serializados
    let datosCompletos = formData + "&accion=" + accionExtra;

    $.ajax({
        url: url,
        type: "POST", // Enviamos por POST
        data: datosCompletos,
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                mostrarExito(response.message).then(() => {
                    $("#modalCategoria").modal("hide");
                    location.reload(); // Recargamos para ver los cambios en la tabla
                });
            } else {
                mostrarError(response.message);
            }
        },
        error: function() {
            mostrarError("Error de comunicación con el controlador.");
        }
    });
}

//funciones para mostrar errores
// MÉTODO: Solo muestra el mensaje de éxito
function mostrarExito(mensaje) {
  return Swal.fire({
    title: "¡Operación Exitosa!",
    text: mensaje,
    icon: "success",
    confirmButtonColor: "#d4af37", // Color Luxury
    confirmButtonText: "Aceptar",
  });
}

// MÉTODO: Solo muestra el mensaje de error
function mostrarError(mensaje) {
  return Swal.fire({
    title: "Error detectado",
    text: mensaje,
    icon: "error",
    confirmButtonColor: "#333",
    confirmButtonText: "Aceptar",
  });
}
