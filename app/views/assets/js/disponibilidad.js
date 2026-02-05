const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
let allProducts = [];
let filteredProducts = [];

$(document).ready(function () {
    cargarTodosLosProductos();
    setupEvents();
});

// Cargar TODOS los productos con sus variantes
function cargarTodosLosProductos() {
    const $productGrid = $("#product-grid");

    $.ajax({
        url: "app/controllers/productoController.php",
        method: "POST",
        dataType: "json",
        data: { accion: "obtenerTodosLosProductosConVariantes" },
        success: function (response) {
            if (response.status === "success") {
                allProducts = response.data;
                filteredProducts = allProducts;
                cargarCategorias();
                renderProducts(filteredProducts);
                updateResultCount();
            } else {
                console.error("Error al cargar productos");
                showNoResults();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la carga:", error);
            $productGrid.html(
                '<div class="col-12 text-center text-danger">Error al conectar con la base de datos.</div>'
            );
        },
    });
}

// Cargar categorías únicas en el select
function cargarCategorias() {
    const categorias = [...new Set(allProducts.map(p => p.nombre_categoria))];
    const $categoryFilter = $("#categoryFilter");
    
    categorias.sort().forEach(categoria => {
        $categoryFilter.append(`<option value="${categoria}">${categoria}</option>`);
    });
}

// Aplicar filtros combinados (búsqueda + categoría)
function aplicarFiltros() {
    const searchTerm = $("#searchInput").val().toLowerCase().trim();
    const selectedCategory = $("#categoryFilter").val();

    filteredProducts = allProducts.filter(product => {
        // Filtro por búsqueda (nombre, SKU o producto padre)
        const matchesSearch = searchTerm === "" || 
            product.nombre.toLowerCase().includes(searchTerm) ||
            product.sku.toLowerCase().includes(searchTerm) ||
            product.nombre_producto_padre.toLowerCase().includes(searchTerm);

        // Filtro por categoría
        const matchesCategory = selectedCategory === "all" || 
            product.nombre_categoria === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    renderProducts(filteredProducts);
    updateResultCount();
}

// Renderizar productos
function renderProducts(productsList) {
    const $productGrid = $("#product-grid");
    const $noResults = $("#noResults");
    
    $productGrid.empty();

    if (productsList.length === 0) {
        showNoResults();
        return;
    }

    $noResults.addClass("d-none");
    $productGrid.removeClass("d-none");

    $.each(productsList, function (i, product) {
        let precioVenta = Number(product.precio_venta);
        let precioFormateado = precioVenta.toFixed(2);
        let stockClass = product.stock > 5 ? "bg-success" : "bg-danger";
        let stockText = product.stock > 0 ? `${product.stock} disponibles` : "Agotado";

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
                        <span class="badge bg-light text-dark border mb-2">${product.nombre_categoria}</span>
                        <p class="text-muted small mb-1">${product.nombre_producto_padre}</p>
                        <h4 class="card-title fw-bold text-dark mb-3">${product.nombre}</h4>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 mb-0 fw-bold text-primary">$${precioFormateado}</span>
                                    <br>
                                    <small class="text-muted">SKU: ${product.sku}</small>
                                    <br>
                                    <small class="text-muted">Reserva: ${product.reserva} un.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $productGrid.append(card);
    });
}


function showNoResults() {
    $("#product-grid").addClass("d-none");
    $("#noResults").removeClass("d-none");
}


function updateResultCount() {
    $("#resultCount").text(filteredProducts.length);
}

// Configurar eventos
function setupEvents() {
    
    $("#searchInput").on("keyup", function() {
        aplicarFiltros();
    });

 
    $("#categoryFilter").on("change", function() {
        aplicarFiltros();
    });

 
    $("#clearSearch").on("click", function() {
        $("#searchInput").val("");
        aplicarFiltros();
    });
}