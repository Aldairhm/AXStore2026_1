var tabla;

$(document).ready(function () {
  tabla = $("#tablaCategorias").DataTable({
    ajax: {
      url: "app/controllers/categoriaController.php",
      type: "POST",
      data: { accion: "listar" },
      dataType: "json",
      dataSrc: "data",
      error: function (e) {
        console.log(e.responseText);
      },
    },
    language: {
      url: "app/ajax/idioma.json",
    },
    aaSorting: [],
    lengthMenu: [
      [5, 12, 18, -1],
      [5, 12, 18, "Todos"],
    ],
    pageLength: 5,
    responsive: true,
    columnDefs: [
      { responsivePriority: 1, targets: 0 }, // Nombre
      { responsivePriority: 2, targets: 2 }, // Acciones (Cambiado de -1 a 2 para claridad)
      { orderable: false, targets: [2] }, // ELIMINADO EL 3, solo el índice 2 (Acciones)
      { searchable: false, targets: [2] }, // ELIMINADO EL 3
    ],
    buttons: [
      {
        extend: "pdfHtml5",
        title: "Categorias",
        messageTop: "Listado de autores",
        text: '<i class="fas fa-file-pdf"></i> ',
        download: "open",
        titleAttr: "Exportar a PDF",
        className: "btn btn-danger",
        exportOptions: {
          columns: ":not(.notexport)",
        },
      },
      {
        extend: "excelHtml5",
        title: "Categorias",
        messageTop: "Listado de autores",
        text: '<i class="fas fa-file-excel"></i> ',
        titleAttr: "Exportar a Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: ":not(.notexport)",
        },
      },
      {
        extend: "print",
        title: "Categorias",
        messageTop: "Listado de autores",
        text: '<i class="fa fa-print"></i> ',
        titleAttr: "Imprimir",
        className: "btn btn-info",
        exportOptions: {
          columns: ":not(.notexport)",
        },
      },
    ],
    dom:
      "<'row mb-4'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-4 text-center'l><'col-sm-12 col-md-4'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row mt-4'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
  });

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

function editar(id) {
  $.ajax({
    url: "app/controllers/categoriaController.php",
    type: "POST",
    data: { accion: "obtenerCategoria", id: id },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const categoria = response.data;
        console.log(categoria.nombre);
        $("#id_categoria").val(categoria.id);
        $("#nombre").val(categoria.nombre);
        $("#descripcion").val(categoria.descripcion);
        $("#modalCategoriaLabel").text("Editar Categoria");
        $("#modalCategoria").modal("show");
      } else {
        mostrarError(response.message);
      }
    },
    error: function () {
      mostrarError("Error de comunicación con el controlador.");
    },
  });
}

function editar_Y_registrarCategoria(formData, id) {
  // 1. Usamos una URL limpia (sin parámetros ?)
  let url = "app/controllers/categoriaController.php";

  // 2. Comparamos con == (o convertimos a número) para que "0" sea igual a 0
  let accionExtra = id == 0 ? "registrarCategoria" : "editarCategoria";

  // 3. Concatenamos la acción a los datos serializados
  let datosCompletos = formData + "&accion=" + accionExtra;

  $.ajax({
    url: url,
    type: "POST", // Enviamos por POST
    data: datosCompletos,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        mostrarExito(response.message).then(() => {
          $("#modalCategoria").modal("hide");
          $("#modalCategoriaLabel").text("Registrar Categoria");
          $("#formCategoria")[0].reset();
          tabla.ajax.reload(null, false);
        });
      } else {
        mostrarError(response.message);
      }
    },
    error: function () {
      mostrarError("Error de comunicación con el controlador.");
    },
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
