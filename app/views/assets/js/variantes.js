$(document).ready(function () {
  // 1. CONFIGURACIÓN
  const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
  const $productGrid = $("#product-grid");
  let allProducts = [];
  const idProducto = $("#variantes-container").data("id");
  console.log("ID del producto:", idProducto);

  // 2. INICIALIZACIÓN
  init();

  function init() {
    fetchProducts();
    setupEvents();
    cargarDatosProducto();
  }

  function cargarDatosProducto() {
    $.ajax({
      url: "app/controllers/productoController.php",
      type: "POST",
      data: { accion: "obtener_uno", id: idProducto },
      success: function (response) {
        if (response.status === "success") {
          const p = response.data;
          // Rellenamos los campos
          $("#id_producto").val(p.id);
          $("#nombre").val(p.nombre).prop("readonly", true); // Bloqueado
          $("#id_categoria").val(p.id_categoria).prop("disabled", true);
          $("#descripcion").val(p.descripcion).prop("readonly", true); // Bloqueado
          $("#estado").val(p.estado).prop("disabled", true);
        }
      },
    });
  }

  // 3. PETICIÓN AJAX AL CONTROLADOR (MVC)
  function fetchProducts() {
    $.ajax({
      url: "app/controllers/productoController.php",
      method: "POST",
      dataType: "json",
      data: { accion: "variantes", id: idProducto },
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
  }

  // 4. RENDERIZADO CON JQUERY
  function renderProducts(productsList) {
    $productGrid.empty(); // Limpia el contenedor

    if (productsList.length === 0) {
      $productGrid.append(
        '<p class="text-center w-100">No hay productos disponibles.</p>',
      );
      return;
    }

    $.each(productsList, function (i, product) {
      // Lógica para el color del stock (Poniéndonos serios con la UX)
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
                    <h6 class="text-uppercase text-muted fw-bold small">${product.categoria}</h6>
                    <h5 class="card-title fw-bold text-dark mb-3">${product.nombre}</h5>
                    
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="h5 mb-0 fw-bold text-primary">$${parseFloat(product.precio).toFixed(2)}</span>
                                <br>
                                <small class="text-muted">Stock: ${product.stock} un.</small>
                            </div>
                            
                            <button class="btn ${product.stock > 0 ? "btn-dark" : "btn-secondary disabled"} btn-sm rounded-pill px-3">
                                ${product.stock > 0 ? "Agregar" : "Sin stock"}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
      $productGrid.append(card);
    });
  }

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
