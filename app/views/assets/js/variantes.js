const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
$(document).ready(function () {
  // 1. CONFIGURACIÓN
  const idProducto = $("#variantes-container").data("id");
  console.log("ID del producto:", idProducto);

  init();

  function init() {
    cargarVariantesGrid(idProducto);
    setupEvents();
  }

  //manejo del botón editar variante
  $(document).on("click", ".btnEditarVariante", function () {
    const idVariante = $(this).data("id");
    $("#modalEditarVariante").modal("show");
    console.log("Editar variante ID:", idVariante);
    prepararEdicionVariante(idVariante);
  });

  $("#formEditarVariante").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("accion", "editarVariante");

    $.ajax({
      url: "app/controllers/productoController.php",
      method: "POST",
      dataType: "json", 
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if(response.status === "success") {
          mostrarExito(response.message);
          $("#modalEditarVariante").modal("hide");
          cargarVariantesGrid(idProducto);
        }else{
          mostrarError(response.message);
        }
      }
    });
  });

  // 5. EVENTOS (MENÚ Y FILTROS)
  function setupEvents() {
    // Menú móvil
    $(".mobile-menu-btn").on("click", function () {
      const $nav = $(".main-nav");
      $nav.toggleClass("active");
      $(this).html(
        $nav.hasClass("active")
          ? '<i class="fas fa-times"></i>'
          : '<i class="fas fa-bars"></i>',
      );
    });

    // Filtrado por categoría
    $(".category-btn").on("click", function () {
      $(".category-btn").removeClass("active");
      $(this).addClass("active");

      const category = $(this).data("category");
      const filtered =
        category === "all"
          ? allProducts
          : allProducts.filter((p) => p.categoria === category);

      renderProducts(filtered);
    });
  }
});

//cargaremos los datos de la variante
function prepararEdicionVariante(idVariante) {
  $.ajax({
    url: "app/controllers/productoController.php",
    method: "POST",
    dataType: "json",
    data: { accion: "obtenerVariantePorId", id: idVariante },
    success: function (response) {
      if (response.status === "success") {
        const variante = response.data.variante;
        $("#id_variante_edit").val(variante.id);
        $("#id_producto").val(variante.id_producto);
        $("#nombre_variante_edit").val(variante.nombre);
        $("#precio_venta_edit").val(variante.precio_venta);
        $("#stock_actual_edit").val(variante.stock);
        $("#sku_edit").val(variante.sku);
        $("#stock_minimo_edit").val(variante.reserva);
        $("#comision_edit").val(variante.comision);
        $("#imgPrevEdit").attr("src", ruta + variante.imagen);

        //pintar los atributos que trae
        const atributos = response.data.atributos;
        const contenedor = $("#contenedorAtributosEdit");
        contenedor.empty();

        if (atributos && atributos.length > 0) {
          $.each(atributos, function (i, attr) {
            const inputAttr = `
            <div class="mb-2">
                <label class="form-label small fw-bold text-muted mb-0">${attr.nombre_atributo}</label>
                <input type="text" 
                       name="atributo_valor[${attr.id_atributo}]" 
                       class="form-control form-control-sm border-secondary input-atributo-edit" 
                       value="${attr.valor}" 
                       data-nombre="${attr.nombre_atributo}"
                       placeholder="Valor para ${attr.nombre_atributo}">
            </div>`;
            contenedor.append(inputAttr);
          });
        } else {
          contenedor.append(
            '<div class="text-center text-muted small py-3">Sin atributos definidos</div>',
          );
        }
      }
    },
  });
}

// 3. PETICIÓN AJAX AL CONTROLADOR (MVC)
window.cargarVariantesGrid = function (id) {
  let allProducts = [];
  const $productGrid = $("#product-grid");

  if (!id || id == 0) {
    console.warn("ID no válido para cargar variantes.");
    return;
  }

  $.ajax({
    url: "app/controllers/productoController.php",
    method: "POST",
    dataType: "json",
    data: { accion: "variantes", id: id },
    success: function (response) {
      if (response.status === "success") {
        allProducts = response.data;
        renderProducts(allProducts);
      } else {
        console.error("El servidor respondió pero con error");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la carga:", error);
      $productGrid.html(
        '<div class="col-12 text-center text-danger">Error al conectar con la base de datos.</div>',
      );
    },
  });
};

// 4. RENDERIZADO CON JQUERY
function renderProducts(productsList) {
  const $productGrid = $("#product-grid");
  $productGrid.empty(); // Limpia el contenedor

  if (productsList.length === 0) {
    $productGrid.append(
      '<p class="text-center w-100">No hay productos disponibles.</p>',
    );
    return;
  }

  $.each(productsList, function (i, product) {
    // Lógica para el color del stock (Poniéndonos serios con la UX)
    let precioVenta = Number(product.precio_venta);
    let precioFormateado = precioVenta.toFixed(2);
    let stockClass = product.stock > 5 ? "bg-success" : "bg-danger";
    let stockText =
      product.stock > 0 ? `${product.stock} disponibles` : "Agotado";

    const card = `
        <div class="col">

            <div class="card h-100 border-0 shadow-sm transition-hover">
                <span class="badge ${stockClass} position-absolute top-0 start-0 m-3 shadow-sm">
                    ${stockText}
                </span>

                <div class="p-3" style="height: 200px;">
                    <img src="${ruta}${product.imagen}" 
                         class="card-img-top h-100 w-100" 
                         style="object-fit: contain;" 
                         alt="${product.nombre}">
                </div>

                <div class="card-body d-flex flex-column">
                    <h3 class="text-uppercase text-muted fw-bold small">${product.nombre_categoria}</h3>
                    <h5 class="text-uppercase text-muted fw-bold small">Producto: ${product.nombre_producto_padre}</h5>
                    <h4 class="card-title fw-bold text-dark mb-3">${product.nombre}</h4>
                    
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="h5 mb-0 fw-bold text-primary">$${precioFormateado}</span>
                                <br>
                                <small class="text-muted">Stock: ${product.reserva} un.</small>
                            </div>
                            
                            <button class="btn btn-dark btn-sm rounded-pill px-3 btnEditarVariante" data-id="${product.id}">Editar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $productGrid.append(card);
  });
}

function mostrarExito(m) {
  Swal.fire({
    title: "¡Éxito!",
    text: m,
    icon: "success",
    confirmButtonColor: "#333",
  });
}

function mostrarError(m) {
  Swal.fire({
    title: "Error",
    text: m,
    icon: "error",
    confirmButtonColor: "#333",
  });
}