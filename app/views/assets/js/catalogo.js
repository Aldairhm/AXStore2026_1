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

    // ============================================
    // VALIDACIONES EN TIEMPO REAL (ON BLUR)
    // ============================================
    
    // Validar cantidad cuando el usuario sale del campo
    $("#cantidad_salida").on("blur", function() {
        validarCantidadVsStock();
    });
    
    // Validar fecha de salida
    $("#fecha_salida").on("blur change", function() {
        validarFechaSalida();
        // También validar fecha de entrega si ya está llena
        if ($("#fecha_entrega").val()) {
            validarFechaEntrega();
        }
    });
    
    // Validar hora de salida
    $("#hora_salida").on("blur", function() {
        validarHoraSalida();
    });
    
    // Validar fecha de entrega (AHORA OBLIGATORIO)
    $("#fecha_entrega").on("blur change", function() {
        validarFechaEntrega();
    });
    
    // Validar dirección (AHORA OBLIGATORIO)
    $("#direccion").on("blur", function() {
        validarDireccion();
    });
    
    // Validar precio de envío (OBLIGATORIO y > 0)
    $("#precio_envio").on("blur", function() {
        validarPrecioEnvio();
    });
    
    // Validar costo extra (OPCIONAL)
    $("#costo_extra").on("blur", function() {
        validarNumeroDecimal("costo_extra", "Costo extra", false);
    });

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

// ============================================
// SISTEMA DE VALIDACIÓN CON FEEDBACK VISUAL
// ============================================

/**
 * Muestra un mensaje de error bajo un campo específico
 * @param {string} fieldId - ID del campo
 * @param {string} message - Mensaje de error
 */
function mostrarError(fieldId, message) {
    const $field = $("#" + fieldId);
    
    // Remover error anterior si existe
    $field.removeClass("is-valid").addClass("is-invalid");
    $field.siblings(".invalid-feedback").remove();
    
    // Agregar mensaje de error
    $field.after(`<div class="invalid-feedback d-block">${message}</div>`);
}

/**
 * Muestra que un campo es válido
 * @param {string} fieldId - ID del campo
 */
function mostrarValido(fieldId) {
    const $field = $("#" + fieldId);
    $field.removeClass("is-invalid").addClass("is-valid");
    $field.siblings(".invalid-feedback").remove();
}

/**
 * Limpia todos los estados de validación del formulario
 */
function limpiarValidaciones() {
    $("#formSalidaProducto").find(".is-invalid, .is-valid").removeClass("is-invalid is-valid");
    $("#formSalidaProducto").find(".invalid-feedback").remove();
}

/**
 * Valida que un campo no esté vacío
 * @param {string} fieldId - ID del campo
 * @param {string} fieldName - Nombre del campo para el mensaje
 * @returns {boolean} - true si es válido
 */
function validarCampoRequerido(fieldId, fieldName) {
    const valor = $("#" + fieldId).val().trim();
    
    if (valor === "") {
        mostrarError(fieldId, `El campo ${fieldName} es obligatorio`);
        return false;
    }
    
    mostrarValido(fieldId);
    return true;
}

/**
 * Valida que un número sea mayor a cero
 * @param {string} fieldId - ID del campo
 * @param {string} fieldName - Nombre del campo
 * @returns {boolean}
 */
function validarNumeroPositivo(fieldId, fieldName) {
    const valor = parseFloat($("#" + fieldId).val());
    
    if (isNaN(valor) || valor <= 0) {
        mostrarError(fieldId, `${fieldName} debe ser mayor a 0`);
        return false;
    }
    
    mostrarValido(fieldId);
    return true;
}

/**
 * Valida que la cantidad no exceda el stock disponible
 * @returns {boolean}
 */
function validarCantidadVsStock() {
    const cantidad = parseInt($("#cantidad_salida").val());
    const stockDisponible = parseInt($("#stockProductoSalida").text());
    
    if (isNaN(cantidad) || cantidad <= 0) {
        mostrarError("cantidad_salida", "Ingrese una cantidad válida");
        return false;
    }
    
    if (cantidad > stockDisponible) {
        mostrarError("cantidad_salida", `Solo hay ${stockDisponible} unidades disponibles en stock`);
        return false;
    }
    
    mostrarValido("cantidad_salida");
    return true;
}

/**
 * Valida que la fecha de salida no sea futura
 * @returns {boolean}
 */
function validarFechaSalida() {
    const fechaSalida = $("#fecha_salida").val();
    
    if (!fechaSalida) {
        mostrarError("fecha_salida", "La fecha de salida es obligatoria");
        return false;
    }
    
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const fechaSeleccionada = new Date(fechaSalida + "T00:00:00");
    
    if (fechaSeleccionada > hoy) {
        mostrarError("fecha_salida", "La fecha de salida no puede ser futura");
        return false;
    }
    
    mostrarValido("fecha_salida");
    return true;
}

/**
 * Valida que la fecha de entrega sea posterior o igual a la fecha de salida (CAMPO OBLIGATORIO)
 * @returns {boolean}
 */
function validarFechaEntrega() {
    const fechaEntrega = $("#fecha_entrega").val();
    
    // Ahora es OBLIGATORIO
    if (!fechaEntrega || fechaEntrega.trim() === "") {
        mostrarError("fecha_entrega", "La fecha de entrega es obligatoria");
        return false;
    }
    
    const fechaSalida = $("#fecha_salida").val();
    
    if (!fechaSalida) {
        mostrarError("fecha_entrega", "Primero debe seleccionar una fecha de salida");
        return false;
    }
    
    const salida = new Date(fechaSalida + "T00:00:00");
    const entrega = new Date(fechaEntrega + "T00:00:00");
    
    if (entrega < salida) {
        mostrarError("fecha_entrega", "La fecha de entrega no puede ser anterior a la fecha de salida");
        return false;
    }
    
    mostrarValido("fecha_entrega");
    return true;
}

/**
 * Valida que la hora de salida sea válida
 * @returns {boolean}
 */
function validarHoraSalida() {
    const hora = $("#hora_salida").val();
    
    if (!hora) {
        mostrarError("hora_salida", "La hora de salida es obligatoria");
        return false;
    }
    
    mostrarValido("hora_salida");
    return true;
}

/**
 * Valida números decimales (ACTUALIZADO: precio de envío es obligatorio)
 * @param {string} fieldId - ID del campo
 * @param {string} fieldName - Nombre del campo
 * @param {boolean} esObligatorio - Si el campo es obligatorio
 * @returns {boolean}
 */
function validarNumeroDecimal(fieldId, fieldName, esObligatorio = false) {
    const valor = parseFloat($("#" + fieldId).val());
    
    // Si es obligatorio y está vacío o es NaN
    if (esObligatorio && (isNaN(valor) || $("#" + fieldId).val().trim() === "")) {
        mostrarError(fieldId, `${fieldName} es obligatorio`);
        return false;
    }
    
    // Si no es obligatorio y está vacío, es válido
    if (!esObligatorio && (isNaN(valor) || $("#" + fieldId).val().trim() === "")) {
        mostrarValido(fieldId);
        return true;
    }
    
    if (isNaN(valor) || valor < 0) {
        mostrarError(fieldId, `${fieldName} debe ser un número válido mayor o igual a 0`);
        return false;
    }
    
    mostrarValido(fieldId);
    return true;
}

/**
 * Valida el precio de envío (CAMPO OBLIGATORIO y MAYOR A 0)
 * @returns {boolean}
 */
function validarPrecioEnvio() {
    const valor = parseFloat($("#precio_envio").val());
    
    // Verificar que no esté vacío
    if (isNaN(valor) || $("#precio_envio").val().trim() === "") {
        mostrarError("precio_envio", "El precio de envío es obligatorio");
        return false;
    }
    
    // Verificar que sea mayor a 0 (no acepta 0)
    if (valor <= 0) {
        mostrarError("precio_envio", "El precio de envío debe ser mayor a 0");
        return false;
    }
    
    mostrarValido("precio_envio");
    return true;
}

/**
 * Valida que la dirección de entrega no esté vacía (CAMPO OBLIGATORIO)
 * @returns {boolean}
 */
function validarDireccion() {
    const direccion = $("#direccion").val().trim();
    
    if (direccion === "") {
        mostrarError("direccion", "La dirección de entrega es obligatoria");
        return false;
    }
    
    // Validar longitud mínima
    if (direccion.length < 10) {
        mostrarError("direccion", "La dirección debe tener al menos 10 caracteres");
        return false;
    }
    
    mostrarValido("direccion");
    return true;
}

/**
 * FUNCIÓN PRINCIPAL DE VALIDACIÓN
 * Valida todos los campos del formulario antes de enviar
 * @returns {boolean} - true si todo es válido
 */
function validarFormularioCompleto() {
    console.log("🔍 Iniciando validación del formulario...");
    let esValido = true;
    
    // Limpiar validaciones previas
    limpiarValidaciones();
    
    // Validar campos obligatorios básicos
    console.log("Validando cantidad...");
    esValido = validarCantidadVsStock() && esValido;
    
    console.log("Validando fecha de salida...");
    esValido = validarFechaSalida() && esValido;
    
    console.log("Validando hora de salida...");
    esValido = validarHoraSalida() && esValido;
    
    // Validar campos obligatorios de entrega
    console.log("Validando fecha de entrega...");
    esValido = validarFechaEntrega() && esValido;
    
    console.log("Validando dirección...");
    esValido = validarDireccion() && esValido;
    
    console.log("Validando precio de envío (OBLIGATORIO y > 0)...");
    esValido = validarPrecioEnvio() && esValido;
    
    // Validar costo extra (opcional)
    console.log("Validando costo extra...");
    esValido = validarNumeroDecimal("costo_extra", "Costo extra", false) && esValido;
    
    console.log(`✅ Resultado de validación: ${esValido ? "VÁLIDO" : "INVÁLIDO"}`);
    
    // Si hay errores, hacer scroll al primer campo inválido
    if (!esValido) {
        const primerError = $("#formSalidaProducto").find(".is-invalid").first();
        if (primerError.length) {
            console.log("❌ Enfocando primer campo con error:", primerError.attr("id"));
            primerError.focus();
        }
    }
    
    return esValido;
}

// Registrar salida con validaciones completas
function registrarSalida() {
    // VALIDAR ANTES DE ENVIAR
    if (!validarFormularioCompleto()) {
        Swal.fire({
            icon: 'warning',
            title: 'Formulario Incompleto',
            text: 'Por favor, corrija los errores señalados en el formulario',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
    
    // Deshabilitar botón para evitar doble envío
    const $btnSubmit = $("#btnRegistrarSalida");
    $btnSubmit.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Procesando...');
    
    const cantidad = parseInt($("#cantidad_salida").val());
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
                    
                    // Limpiar validaciones
                    limpiarValidaciones();
                    
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
        },
        complete: function() {
            // Rehabilitar botón
            $btnSubmit.prop("disabled", false).html('<i class="fas fa-check me-1"></i> Registrar Salida');
        }
    });
}