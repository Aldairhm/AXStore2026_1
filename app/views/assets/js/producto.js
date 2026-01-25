// Evita que Bootstrap 5 bloquee el enfoque del teclado en SweetAlert2
document.addEventListener("focusin", (e) => {
  if (e.target.closest(".swal2-container")) {
    e.stopImmediatePropagation();
  }
});
var tabla;

$(document).ready(function () {
  //cargamos los productos
  tabla = $("#tablaProductos").DataTable({
    ajax: {
      url: "app/controllers/productoController.php",
      type: "POST",
      data: { accion: "cargarProductos" },
    },
    responsive: true,
    autoWidth: false, // ¡IMPORTANTE! Para que respete tus anchos de columna
    // El DOM estaba casi perfecto, solo añadí "p-2" para que no pegue a los bordes
    dom: '<"d-flex justify-content-between align-items-center px-2 mb-3"lf>rt<"d-flex justify-content-between align-items-center px-2 mt-4"ip>',
    language: {
      url: "app/ajax/idioma.json",
    },
    columns: [
      { width: "20%", targets: 0 },
      { width: "20%", targets: 1 },
      { width: "40%", targets: 2 },
      { width: "20%", targets: 3 },
      { width: "20%", className: "text-center", targets: 4 },
    ],
    order: [[0, "asc"]],
    // Añadimos esto para que los botones de paginación se vean limpios
    drawCallback: function () {
      $(".dataTables_paginate > .paginate_button").addClass("btn btn-sm");
    },
  });

  $(document).on("keydown", "input[type='number']", function (e) {
    if (e.key === "-" || e.key === "e" || e.key === "E") {
      e.preventDefault();
    }
  });

  // Validación cruzada de precios
  $(document).on("change","input[name='v_precio_compra[]'], input[name='v_precio_venta[]']",function () {
      const fila = $(this).closest("tr");
      const pCompra = parseFloat(fila.find("input[name='v_precio_compra[]']").val()) || 0;
      const pVenta = parseFloat(fila.find("input[name='v_precio_venta[]']").val()) || 0;

      if (pVenta > 0 && pVenta < pCompra) {
        mostrarError("El precio de venta no puede ser menor al precio de compra. ¡Estarías perdiendo dinero!",);
        fila.find("input[name='v_precio_venta[]']").val(pCompra.toFixed(2)); // Lo igualamos por defecto
      }
    },
  );

  // Asegurar que el stock sea al menos 1
  $(document).on("change", "input[name='v_stock_actual[]']", function () {
    let valor = parseInt($(this).val());
    if (isNaN(valor) || valor < 1) {
      $(this).val(1);
      mostrarError("El stock inicial debe ser al menos 1 unidad.");
    }
  });

  // Si por algún motivo entra un negativo, lo convertimos a 0 inmediatamente
  $(document).on("input", "input[type='number']", function () {
    if (this.value < 0) {
      this.value = 0;
    }
  });

  // Botón para agregar nueva fila de atributo
  $("#btnAgregarFilaAtributo").click(function () {
    $("#contenedorAtributos p").remove();

    const htmlFila = `
        <div class="row g-2 mb-3 fila-atributo p-2 align-items-end border-bottom">
            <div class="col-5">
                <label class="form-label small fw-bold text-dark">Atributo</label>
                <select name="atributo_id[]" class="form-select form-select-sm select-atributo-base selectAtributo">
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
        $("#id_categoria_edit").html(options);
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
    const descripcion = $("#descripcion").val().trim();
    const categoria = $("#id_categoria").val();

    if (nombre === "" || descripcion === "" || categoria === "") {
      mostrarError(
        "Primero completa el nombre, la categoria y descripcion del producto.",
      );
      return;
    }

    generarMatriz();
  });

  //funcion para generar la matriz de variantes
  function generarMatriz() {
    console.log("--- Iniciando Generación de Matriz Luxury ---");
    const nombreProducto = $("#nombre").val().trim();
    const cuerpo = $("#cuerpoVariantes");

    // 1. Limpieza inicial
    cuerpo.empty();
    let atributosData = [];
    let idsSeleccionados = new Set();
    let errorFilaIncompleta = false;
    let errorDuplicadoAtributo = false;

    // 2. Procesamiento de Filas
    $(".fila-atributo").each(function (index) {
      const idAttr = $(this).find(".selectAtributo").val();
      const nombreAttr = $(this).find(".selectAtributo option:selected").text();
      const valorInput = $(this).find(".input-valor-variante").val().trim();

      // Si la fila está vacía, la ignoramos (por si el usuario agregó una de más)
      if (idAttr === "" && valorInput === "") return true;

      // Validación: Fila con datos incompletos
      if (idAttr === "" || valorInput === "") {
        errorFilaIncompleta = true;
        return false;
      }

      // Validación: Atributo repetido (ej: seleccionar "Color" dos veces)
      if (idsSeleccionados.has(idAttr)) {
        errorDuplicadoAtributo = true;
        return false;
      }
      idsSeleccionados.add(idAttr);

      // --- FILTRO DE VALORES DUPLICADOS (Case-Insensitive) ---
      let valoresBrutos = valorInput
        .split(",")
        .map((v) => v.trim())
        .filter((v) => v !== "");
      let valoresUnicos = [];
      let valoresCheck = new Set(); // Para comparar en minúsculas

      valoresBrutos.forEach((v) => {
        let minuscula = v.toLowerCase();
        if (!valoresCheck.has(minuscula)) {
          valoresCheck.add(minuscula);
          valoresUnicos.push(v); // Guardamos el original (ej: "Rojo")
        }
      });

      if (valoresUnicos.length > 0) {
        atributosData.push({
          id: idAttr,
          nombre: nombreAttr,
          valores: valoresUnicos,
        });
      }
    });

    // 3. Control de Errores de Validación
    if (errorFilaIncompleta) {
      mostrarError(
        "Hay filas de atributos incompletas. Completá o eliminá las filas vacías.",
      );
      return;
    }
    if (errorDuplicadoAtributo) {
      mostrarError(
        "No puedes repetir el mismo atributo (ej: Color) en diferentes filas.",
      );
      return;
    }
    if (atributosData.length === 0) {
      cuerpo.html(
        '<tr><td colspan="8" class="text-center text-muted py-4">Define atributos y valores para ver la matriz.</td></tr>',
      );
      return;
    }

    // 4. Generación de Combinaciones (Producto Cartesiano)
    try {
      const valoresParaCombinar = atributosData.map((a) => a.valores);

      // Verificamos si la función auxiliar existe
      if (typeof obtenerCombinaciones !== "function") {
        throw new Error("La función 'obtenerCombinaciones' no fue encontrada.");
      }

      let combinaciones =
        valoresParaCombinar.length > 1
          ? obtenerCombinaciones(valoresParaCombinar)
          : valoresParaCombinar[0].map((v) => [v]);

      // 5. Renderizado de la Tabla
      let htmlFilas = "";
      combinaciones.forEach((combo) => {
        const arrayCombo = Array.isArray(combo) ? combo : [combo];

        // LÓGICA DEL SKU AUTOMÁTICO
        let prefijoProd = generarPrefijo(nombreProducto);
        let sufijoAtributos = arrayCombo
          .map((v) => generarPrefijo(v))
          .join("-");
        let skuSugerido = `${prefijoProd}-${sufijoAtributos}`;

        // Etiqueta visual: "Color: Rojo / Talla: XL"
        const labelVariante = arrayCombo
          .map((val, idx) => `${atributosData[idx].nombre}: ${val}`)
          .join(" / ");

        // Mapeo para el Controlador PHP: {"1":"Rojo", "2":"XL"}
        let mapaValores = {};
        arrayCombo.forEach((val, idx) => {
          mapaValores[atributosData[idx].id] = val;
        });

        htmlFilas += `
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-3">
                        <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">${nombreProducto}</small>
                        <span class="fw-bold" style="font-size: 0.85rem;">${labelVariante}</span>
                        <input type="hidden" name="v_nombre[]" value="${labelVariante}">
                        <input type="hidden" name="v_valores_json[]" value='${JSON.stringify(mapaValores)}'>
                    </td>
                    <td><input type="text" name="v_sku[]" class="form-control form-control-sm" value="${skuSugerido}" readonly></td>
                    <td><input type="file" name="v_foto[]" class="form-control form-control-sm" accept="image/*" required></td>
                    <td><input type="number" name="v_precio_compra[]" class="form-control form-control-sm" placeholder="0.00" step="0.01" min="0" required></td>
                    <td><input type="number" name="v_precio_venta[]" class="form-control form-control-sm" placeholder="0.00" step="0.01" min="0" required></td>
                    <td><input type="number" name="v_stock_actual[]" class="form-control form-control-sm" value="1" min="1" required></td>
                    <td><input type="number" name="v_stock_minimo[]" class="form-control form-control-sm" value="0" min="1" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-link text-danger p-0 btn-quitar-variante">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </td>
                </tr>`;
      });

      cuerpo.append(htmlFilas);
      mostrarExito(
        `Se han generado ${combinaciones.length} variantes correctamente.`,
      );
    } catch (error) {
      console.error("Fallo en la generación:", error);
      mostrarError("Error técnico: " + error.message);
    }
  }

  //Manejo de la informacion para registrar los datos
  $("#formProducto").on("submit", function (e) {
    e.preventDefault();
    let errorValidacion= false;

    $("#cuerpoVariantes tr").each(function() {
        const pCompra = parseFloat($(this).find("input[name='v_precio_compra[]']").val());
        const pVenta = parseFloat($(this).find("input[name='v_precio_venta[]']").val());
        const stock = parseInt($(this).find("input[name='v_stock_actual[]']").val());

        if (pVenta < pCompra) {
            mostrarError("Revisá los precios. Hay variantes donde la venta es menor a la compra.");
            errorValidacion = true;
            return false;
        }

        if (stock < 1) {
            mostrarError("Todas las variantes deben tener al menos 1 unidad de stock.");
            errorValidacion = true;
            return false;
        }
    });

    if (errorValidacion) return;

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
          tabla.ajax.reload();
        } else {
          mostrarError(response.message);
        }
      },
      error: function () {
        mostrarError("Error crítico en el servidor");
      },
    });
  });

  //Manejo de la info para editar el producto
  $("#formProductoEdicion").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("accion", "editarProducto");
    $.ajax({
      url: "app/controllers/productoController.php",
      type: "POST",
      data: formData,
      dataType: "json",
      processData: false, // Vital para enviar archivos
      contentType: false, // Vital para enviar archivos
      success: function (response) {
        if (response.status === "success") {
          $("#modalProductoEdicion").modal("hide");
          $("#formProductoEdicion")[0].reset();
          tabla.ajax.reload();
          mostrarExito("Producto editado correctamente");
        } else {
          mostrarError(response.message);
        }
      },
    });
  });
});

// Función para obtener todas las combinaciones posibles de múltiples arreglos
function obtenerCombinaciones(arrays) {
  return arrays.reduce((a, b) => a.flatMap((d) => b.map((e) => [d, e].flat())));
}

function generarPrefijo(texto) {
  return texto
    .trim()
    .substring(0, 3) // Tomamos las primeras 3 letras
    .toUpperCase() // Todo a mayúsculas
    .replace(/\s+/g, ""); // Quitamos espacios por si las moscas
}

//funcion de editar producto
function editarProducto(id) {
  $.ajax({
    url: "app/controllers/productoController.php",
    type: "POST",
    data: { accion: "obtener_uno", id: id },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const producto = response.data;
        // Rellenar los campos del formulario con los datos del producto
        $("#id_producto_edit").val(producto.id);
        $("#nombre_edit").val(producto.nombre);
        $("#id_categoria_edit").val(producto.id_categoria);
        $("#estado_edit").val(producto.estado);
        $("#descripcion_edit").val(producto.descripcion);
        $("#modalProductoEdicion").modal("show");
      }
    },
  });
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
