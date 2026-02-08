const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
let allProducts = [];
let filteredProducts = [];

$(document).ready(function () {
    cargarTodosLosProductos();
    setupEvents();
    setFechaHoraActual();
});

// Establecer fecha y hora actual por defecto
function setFechaHoraActual() {
    const ahora = new Date();
    const fecha = ahora.toISOString().split('T')[0];
    const hora = ahora.toTimeString().split(' ')[0].substring(0, 5);
    
    $("#fecha_salida").val(fecha);
    $("#hora_salida").val(hora);
}

// Cargar TODOS los productos
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

// Cargar categorías únicas
function cargarCategorias() {
    const categorias = [...new Set(allProducts.map(p => p.nombre_categoria))];
    const $categoryFilter = $("#categoryFilter");
    
    categorias.sort().forEach(categoria => {
        $categoryFilter.append(`<option value="${categoria}">${categoria}</option>`);
    });
}

// Aplicar filtros
function aplicarFiltros() {
    const searchTerm = $("#searchInput").val().toLowerCase().trim();
    const selectedCategory = $("#categoryFilter").val();
    const selectedStatus = $("#statusFilter").val();

    filteredProducts = allProducts.filter(product => {
        const matchesSearch = searchTerm === "" || 
            product.nombre.toLowerCase().includes(searchTerm) ||
            product.sku.toLowerCase().includes(searchTerm) ||
            product.nombre_producto_padre.toLowerCase().includes(searchTerm);

        const matchesCategory = selectedCategory === "all" || 
            product.nombre_categoria === selectedCategory;

        const matchesStatus = selectedStatus === "all" || 
            product.estado == selectedStatus;

        return matchesSearch && matchesCategory && matchesStatus;
    });

    renderProducts(filteredProducts);
    updateResultCount();
}

// Renderizar productos con botón de salida
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
        let stockClass = product.stock > 5 ? "bg-success" : product.stock > 0 ? "bg-warning text-dark" : "bg-danger";
        let stockText = product.stock > 0 ? `${product.stock} disponibles` : "Agotado";
        
        // Deshabilitar botón si no hay stock
        let disabledBtn = product.stock <= 0 ? 'disabled' : '';

        const card = `
            <div class="col">
                <div class="card h-100 border-0 shadow-sm transition-hover">
                    
                    <span class="badge ${stockClass} position-absolute top-0 start-0 m-2 shadow-sm">
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
                            <div class="mb-2">
                                <span class="h5 mb-0 fw-bold text-primary">$${precioFormateado}</span>
                                <br>
                                <small class="text-muted">SKU: ${product.sku}</small>
                                <br>
                                <small class="text-muted">Reserva: ${product.reserva} un.</small>
                            </div>
                            
                            <button class="btn btn-danger btn-sm w-100 btnSalidaProducto" 
                                    data-id="${product.id}"
                                    data-nombre="${product.nombre}"
                                    data-sku="${product.sku}"
                                    data-precio="${product.precio_venta}"
                                    data-stock="${product.stock}"
                                    data-imagen="${product.imagen}"
                                    ${disabledBtn}>
                                <i class="fas fa-truck me-1"></i> Registrar Salida
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $productGrid.append(card);
    });
}

// Mostrar mensaje cuando no hay resultados
function showNoResults() {
    $("#product-grid").addClass("d-none");
    $("#noResults").removeClass("d-none");
}

// Actualizar contador
function updateResultCount() {
    $("#resultCount").text(filteredProducts.length);
}

// Configurar eventos
function setupEvents() {
    // Búsqueda
    $("#searchInput").on("keyup", aplicarFiltros);
    $("#categoryFilter").on("change", aplicarFiltros);
    $("#statusFilter").on("change", aplicarFiltros);
    $("#clearSearch").on("click", function() {
        $("#searchInput").val("");
        aplicarFiltros();
    });

    // Abrir modal de salida
    $(document).on("click", ".btnSalidaProducto", function() {
        const producto = {
            id: $(this).data("id"),
            nombre: $(this).data("nombre"),
            sku: $(this).data("sku"),
            precio: parseFloat($(this).data("precio")),
            stock: parseInt($(this).data("stock")),
            imagen: $(this).data("imagen")
        };
        
        abrirModalSalida(producto);
    });

    // Calcular totales en tiempo real
    $("#cantidad_salida, #precio_envio, #costo_extra").on("input", calcularTotales);

    // Enviar formulario de salida
    $("#formSalidaProducto").on("submit", function(e) {
        e.preventDefault();
        registrarSalida();
    });
}

// Abrir modal y cargar datos del producto
function abrirModalSalida(producto) {
    // Cargar información del producto
    $("#id_variante_salida").val(producto.id);
    $("#precio_unitario_salida").val(producto.precio);
    $("#nombreProductoSalida").text(producto.nombre);
    $("#skuProductoSalida").text(producto.sku);
    $("#precioProductoSalida").text("$" + producto.precio.toFixed(2));
    $("#stockProductoSalida").text(producto.stock + " unidades");
    $("#imgProductoSalida").attr("src", ruta + producto.imagen);
    
    // Establecer cantidad máxima
    $("#cantidad_salida").attr("max", producto.stock);
    $("#cantidad_salida").val(1);
    
    // Resetear campos
    $("#precio_envio").val("0.00");
    $("#costo_extra").val("0.00");
    $("#direccion").val("");
    $("#fecha_entrega").val("");
    $("#observaciones").val("");
    setFechaHoraActual();
    
    // Calcular totales iniciales
    calcularTotales();
    
    // Mostrar modal con Bootstrap 5
    const modalElement = document.getElementById('modalSalidaProducto');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Calcular totales
function calcularTotales() {
    const cantidad = parseFloat($("#cantidad_salida").val()) || 0;
    const precioUnitario = parseFloat($("#precio_unitario_salida").val()) || 0;
    const precioEnvio = parseFloat($("#precio_envio").val()) || 0;
    const costoExtra = parseFloat($("#costo_extra").val()) || 0;
    
    const subtotal = cantidad * precioUnitario;
    const total = subtotal + precioEnvio + costoExtra;
    
    $("#subtotalSalida").text("$" + subtotal.toFixed(2));
    $("#envioSalida").text("$" + precioEnvio.toFixed(2));
    $("#extraSalida").text("$" + costoExtra.toFixed(2));
    $("#totalSalida").text("$" + total.toFixed(2));
}

// Registrar salida
function registrarSalida() {
    const cantidad = parseInt($("#cantidad_salida").val());
    const stockDisponible = parseInt($("#stockProductoSalida").text());
    
    // Validar cantidad
    if (cantidad > stockDisponible) {
        Swal.fire({
            icon: 'error',
            title: 'Stock Insuficiente',
            text: `Solo hay ${stockDisponible} unidades disponibles`,
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    const formData = new FormData($("#formSalidaProducto")[0]);
    formData.append("accion", "registrarSalida");
    
    // Calcular totales para enviar
    const subtotal = cantidad * parseFloat($("#precio_unitario_salida").val());
    const total = subtotal + parseFloat($("#precio_envio").val() || 0) + parseFloat($("#costo_extra").val() || 0);
    
    formData.append("subtotal", subtotal.toFixed(2));
    formData.append("total", total.toFixed(2));
    
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Salida Registrada!',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Cerrar modal con Bootstrap 5
                    const modalElement = document.getElementById('modalSalidaProducto');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                    
                    // Recargar productos para actualizar stock
                    cargarTodosLosProductos();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}