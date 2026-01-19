// Evita que Bootstrap 5 bloquee el enfoque del teclado en SweetAlert2
document.addEventListener("focusin", (e) => {
  if (e.target.closest(".swal2-container")) {
    e.stopImmediatePropagation();
  }
});

function cargarAtributos(selectElement) {
  $.ajax({
    url: "app/controllers/productoController.php",
    type: "POST",
    data: { accion: "obtenerAtributos" },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let options = '<option value="">Seleccione...</option>';
        response.data.forEach((item) => {
          options += `<option value="${item.id}">${item.nombre}</option>`;
        });
        selectElement.html(options);
      }
    },
  });
}

$(document).ready(function () {
  // Botón para agregar nueva fila de atributo
  // Botón para agregar nueva fila de atributo
  $("#btnAgregarFilaAtributo").click(function () {
    // 1. Quitamos el mensaje de "No hay atributos"
    $("#contenedorAtributos p").remove();

    // 2. Definimos el HTML (Como lo tenías)
    const htmlFila = `
        <div class="row g-2 mb-3 fila-atributo p-2 align-items-end">
            <div class="col-5">
                <label class="form-label small fw-bold">Atributo</label>
                <select name="atributo_id[]" class="form-select form-select-sm selectAtributo">
                    <option value="">Cargando...</option>
                </select>
            </div>
            <div class="col-5">
                <label class="form-label small fw-bold">Valor</label>
                <input type="text" name="atributo_valor[]" class="form-control form-control-sm" placeholder="Ej: Rojo">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-quitar-atributo btn-sm btn-outline-danger w-100" style="height: 31px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>`;

    // 3. CONVERTIMOS EL TEXTO EN OBJETO JQUERY
    const $nuevaFila = $(htmlFila);

    // 4. AGREGAMOS AL CONTENEDOR
    $("#contenedorAtributos").append($nuevaFila);

    // 5. AHORA SÍ PODEMOS USAR .find() EN EL OBJETO
    const $nuevoSelect = $nuevaFila.find(".selectAtributo");

    // 6. CARGAMOS LOS DATOS
    cargarAtributos($nuevoSelect);
  });

  // Delegación de eventos para quitar filas
  $(document).on("click", ".btn-quitar-atributo", function () {
    $(this).closest(".fila-atributo").remove();

    // Si ya no quedan filas, mostramos el mensaje de ayuda
    if ($("#contenedorAtributos").children().length === 0) {
      $("#contenedorAtributos").html(
        '<p class="text-muted small text-center pt-4">Asigne atributos para generar variantes.</p>',
      );
    }
  });

  $.ajax({
    url: "app/controllers/productoController.php",
    type: "POST",
    data: { accion: "obtenerCategorias" },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let options = '<option value="">Seleccione...</option>';
        response.data.forEach((item) => {
          options += `<option value="${item.id}">${item.nombre}</option>`;
        });
        $("#id_categoria").html(options);
      }
    },
  });

  $("#btnNuevoAtributo").on("click", function () {
    Swal.fire({
      title: "Nuevo Atributo",
      input: "text",
      inputLabel: "Nombre del atributo (ej: Talla, Color)",
      inputPlaceholder: "Escribe el nombre...",
      showCancelButton: true,
      confirmButtonText: "Guardar",
      cancelButtonText: "Cancelar",
      inputValidator: (value) => {
        if (!value) {
          return "¡Necesitas escribir algo!";
        }
      },
    }).then((result) => {
      if (result.isConfirmed) {
        // Si el usuario escribió algo y dio clic en Guardar
        const nombreAtributo = result.value;

        $.ajax({
          url: "app/controllers/productoController.php", // Tu archivo PHP
          type: "POST",
          data: { nombre: nombreAtributo, accion: "crearAtributo" },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              // 1. Mostrar mensaje de éxito
              mostrarExito("El atributo ha sido creado.");

              // 2. AGREGARLO AL SELECT DINÁMICAMENTE
              // Suponiendo que tu select de atributos tiene el id "selectAtributos"
              $(".selectAtributo").append(
                $("<option>", {
                  value: response.id,
                  text: nombreAtributo,
                }),
              );
            } else {
              mostrarError(response.message);
            }
          },
          error: function () {
            mostrarError("No se pudo conectar con el servidor");
          },
        });
      }
    });
  });
});

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
