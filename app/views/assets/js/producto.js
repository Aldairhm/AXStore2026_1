// Evita que Bootstrap 5 bloquee el enfoque del teclado en SweetAlert2
document.addEventListener("focusin", (e) => {
  if (e.target.closest(".swal2-container")) {
    e.stopImmediatePropagation();
  }
});

$(document).ready(function () {
  // Botón para agregar nueva fila de atributo
  $("#btnAgregarFilaAtributo").click(function () {
    $("#contenedorAtributos p").remove();

    const htmlFila = `
        <div class="row g-2 mb-3 fila-atributo p-2 align-items-end border-bottom">
            <div class="col-5">
                <label class="form-label small fw-bold text-dark">Atributo</label>
                <select name="atributo_id[]" class="form-select form-select-sm select-atributo-base">
                    <option value="">Cargando...</option>
                </select>
            </div>
            <div class="col-5">
                <label class="form-label small fw-bold text-dark">Valor</label>
                <input type="text" name="atributo_valor[]" class="form-control form-control-sm input-valor-variante" placeholder="Ej: L, M, XL">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-quitar-atributo btn-sm btn-outline-danger w-100 p-0 d-flex justify-content-center align-items-center" 
                        style="height: 31px;">
                    <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                </button>
            </div>
        </div>`;

    const $nuevaFila = $(htmlFila);
    $("#contenedorAtributos").append($nuevaFila);
    cargarAtributos($nuevaFila.find(".select-atributo-base"));
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

  //consulta para cargar las categorias
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

  //boton que sirve para registrar un nuevo atributo en la base
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

  //logica: desabilitar el boton de los atributos mientras los datos del producto estan vacios

  // Generar matriz de variantes cuando cambie algún valor
 $("#btnGenerarMatriz").on("click", function () {
        // Validamos primero que haya datos base
        const nombre = $("#nombre").val().trim();
        const categoria = $("#id_categoria").val();

        if (nombre === "" || categoria === "") {
            mostrarError("Primero completa el nombre y la categoría del producto.");
            return;
        }

        generarMatriz();
        mostrarExito("Matriz generada según los atributos actuales.");
    });

  //funcion para generar la matriz de variantes
  function generarMatriz() {
    const nombre = $("#nombre").val().trim();
    const cuerpo = $("#cuerpoVariantes");
    cuerpo.empty();

    let atributosData = [];

    $(".fila-atributo").each(function () {
        const nombreAttr = $(this).find(".select-atributo-base option:selected").text();
        const valorInput = $(this).find(".input-valor-variante").val().trim();

        if (valorInput !== "" && nombreAttr !== "Seleccione...") {
            // "Explota" los valores por coma: "Rojo, Azul" -> ["Rojo", "Azul"]
            const valoresArray = valorInput.split(",").map((v) => v.trim()).filter((v) => v !== "");

            if (valoresArray.length > 0) {
                atributosData.push({
                    nombre: nombreAttr,
                    valores: valoresArray,
                });
            }
        }
    });

    if (atributosData.length === 0) {
        cuerpo.html('<tr><td colspan="5" class="text-center text-muted py-3">Agregue atributos válidos.</td></tr>');
        return;
    }

    const valoresParaCombinar = atributosData.map((attr) => attr.valores);

    // Calculamos el producto cartesiano
    let combinaciones = valoresParaCombinar.length > 1
        ? obtenerCombinaciones(valoresParaCombinar)
        : valoresParaCombinar[0].map((v) => [v]);

    // Renderizamos la tabla
    combinaciones.forEach((combo) => {
        const arrayCombo = Array.isArray(combo) ? combo : [combo];
        const labelVariante = arrayCombo.map((val, idx) => `${atributosData[idx].nombre}: ${val}`).join(" / ");

        const nuevaFila = `
            <tr class="animate__animated animate__fadeIn">
                <td class="ps-3">
                    <small class="text-muted d-block" style="font-size: 0.7rem;">${nombre}</small>
                    <span class="fw-bold">${labelVariante}</span>
                    <input type="hidden" name="v_descripcion[]" value="${labelVariante}">
                </td>
                <td><input type="file" name="v_foto[]" class="form-control form-control-sm" accept="image/*"></td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number" name="v_precio[]" class="form-control" placeholder="0.00" step="0.01" required>
                    </div>
                </td>
                <td><input type="number" name="v_stock[]" class="form-control form-control-sm" placeholder="0" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger p-0 btn-quitar-variante">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </td>
            </tr>`;
        cuerpo.append(nuevaFila);
    });
}

  //Manejo de la informacion para registrar los datos
  $("#formProducto").on("submit",function(e){
    e.preventDefault();

    Swal.fire({
        title: 'Procesando...',
        text: 'Guardando producto y variantes',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    const formData = new FormData(this);
    formData.append("accion", "registrarProductoCompleto");

    $.ajax({
        url: "app/controllers/productoController.php",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false, // Vital para enviar archivos
        contentType: false, // Vital para enviar archivos
        success: function (response) {
            if (response.status === "success") {
                mostrarExito("Producto registrado correctamente");
                $("#modalProducto").modal("hide");
                $("#formProducto")[0].reset();
                $("#cuerpoVariantes").empty();
                // Aquí recargarías tu DataTable de productos
            } else {
                mostrarError(response.message);
            }
        },
        error: function () {
            mostrarError("Error crítico en el servidor");
        }
    });
  });
});

// Función para obtener todas las combinaciones posibles de múltiples arreglos
function obtenerCombinaciones(arrays) {
  return arrays.reduce((a, b) => a.flatMap((d) => b.map((e) => [d, e].flat())));
}

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