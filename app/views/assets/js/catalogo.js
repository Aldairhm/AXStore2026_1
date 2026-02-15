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
    
    // Obtener fecha local ajustando por el offset de la zona horaria
    const offset = ahora.getTimezoneOffset() * 60000; // offset en ms
    const localISOTime = new Date(ahora.getTime() - offset).toISOString();
    
    const fecha = localISOTime.split('T')[0];
    const hora = ahora.getHours().toString().padStart(2, '0') + ':' + 
                 ahora.getMinutes().toString().padStart(2, '0');
    
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

                // [NUEVO] Abrir modal de salida automáticamente si viene el ID en la URL
                const urlParams = new URLSearchParams(window.location.search);
                const abrirSalidaId = urlParams.get('abrirSalida');
                if (abrirSalidaId) {
                    const productToOpen = allProducts.find(p => p.id == abrirSalidaId);
                    if (productToOpen) {
                        setTimeout(() => abrirModalSalida(productToOpen), 500);
                        // Limpiar la URL para que no se reabra al recargar
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                }
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

// Cargar categorías únicas y renderizar Pills
function cargarCategorias() {
    const categorias = [...new Set(allProducts.map(p => p.nombre_categoria))];
    const $categorySelect = $("#categoryFilter");
    const $categoryNav = $("#catalogo-categories-nav");
    
    // Limpiar contenedores
    $categorySelect.html('<option value="all">Todas las Categorías</option>');
    $categoryNav.empty();

    // Agregar botón "TODOS" a los Pills
    $categoryNav.append('<div class="catalog-pill active" data-category="all">TODOS</div>');
    
    categorias.sort().forEach(categoria => {
        // Agregar al Select (se mantiene oculto pero sincronizado)
        $categorySelect.append(`<option value="${categoria}">${categoria}</option>`);
        
        // Agregar al Nav visual (Pills)
        $categoryNav.append(`<div class="catalog-pill" data-category="${categoria}">${categoria}</div>`);
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

// Renderizar productos con funciones Premium
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

    const maxId = Math.max(...productsList.map(p => p.id), 0);
    const umbralNuevo = maxId - 12; // Marcar como nuevos los últimos 12 IDs

    $.each(productsList, function (i, product) {
        let precioVenta = Number(product.precio_venta);
        let precioFormateado = precioVenta.toFixed(2);
        
        // --- LÓGICA DE ETIQUETAS (BADGES) ---
        let badgesHtml = '';
        
        // 1. Etiqueta NUEVO (Basado en ID si no hay fecha)
        if (product.id > umbralNuevo) {
            badgesHtml += '<span class="badge-premium badge-new">NUEVO</span>';
        }
        
        // 2. Etiqueta STOCK BAJO (Si stock es menor o igual a reserva)
        if (product.stock > 0 && product.stock <= product.reserva) {
            badgesHtml += '<span class="badge-premium badge-low-stock">STOCK BAJO</span>';
        }

        // 3. Etiqueta TOP VENTAS (Si tiene más de 5 unidades vendidas)
        if (parseInt(product.ventas_totales) >= 5) {
            badgesHtml += '<span class="badge-premium badge-top">TOP VENTAS</span>';
        }

        // --- LÓGICA DE IMAGENES (HOVER FX) ---
        const mainImg = `${ruta}${product.imagen}`;
        const hoverImg = product.imagen_hover ? `${ruta}${product.imagen_hover}` : mainImg;

        // Deshabilitar botón si no hay stock
        let disabledBtn = product.stock <= 0 ? 'disabled' : '';
        let stockColor = product.stock > 5 ? 'text-success' : product.stock > 0 ? 'text-warning' : 'text-danger';

        const card = `
            <div class="col">
                <div class="card h-100 border-0 shadow-sm transition-hover product-card">
                    
                    <!-- Contenedor de Badges -->
                    <div class="product-badge-container">
                        ${badgesHtml}
                    </div>

                    <!-- Acciones Rápidas Flotantes -->
                    <div class="product-quick-actions">
                        <button class="btn-action-premium btnQuickView" data-id="${product.id}" title="Vista Rápida">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action-premium btnPdfDownload" data-id="${product.id}" title="Descargar Ficha PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>

                    <!-- Imagen con Efecto Hover -->
                    <div class="product-image-container">
                        <img src="${mainImg}" class="product-img-main" alt="${product.nombre}">
                        <img src="${hoverImg}" class="product-img-hover" alt="${product.nombre} hover">
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-dark border">${product.nombre_categoria}</span>
                            <span class="small fw-bold ${stockColor}">${product.stock > 0 ? product.stock + ' un.' : 'AGOTADO'}</span>
                        </div>
                        
                        <p class="text-muted small mb-1">${product.nombre_producto_padre}</p>
                        <h5 class="card-title fw-bold text-dark mb-3">${product.nombre}</h5>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 mb-0 fw-bold text-primary">$${precioFormateado}</span>
                                <small class="text-muted fw-bold">SKU: ${product.sku}</small>
                            </div>
                            
                            <button class="btn btn-danger btn-sm w-100 btnSalidaProducto py-2" 
                                    data-id="${product.id}"
                                    data-nombre="${product.nombre}"
                                    data-sku="${product.sku}"
                                    data-precio="${product.precio_venta}"
                                    data-stock="${product.stock}"
                                    data-imagen="${product.imagen}"
                                    ${disabledBtn}>
                                <i class="fas fa-truck me-1"></i> REGISTRAR SALIDA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $productGrid.append(card);
    });
}

// Event listener para el botón PDF (Delegado para mayor robustez)
$(document).on("click", ".btnPdfDownload", function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const id = $(this).data("id");
    console.log("PDF Clicked for ID:", id);
    
    // Usamos la variable global filteredProducts o buscamos en products si estuviera disponible
    // En catalogo.js la variable global es 'allProducts'
    const product = allProducts.find(p => p.id == id);
    
    if (product) {
        console.log("Generating Ticket for:", product.nombre);
        descargarFichaProducto(product);
    } else {
        console.error("Product not found for ID:", id);
    }
});

// Función para descargar PDF tipo TICKET
async function descargarFichaProducto(product) {
    console.log("Iniciando descargarFichaProducto...");
    try {
        const { jsPDF } = window.jspdf;
        if (!jsPDF) throw new Error("jsPDF no está cargado correctamente.");

        // Formato Ticket (80mm x 150mm aprox)
        const doc = new jsPDF({
            unit: 'mm',
            format: [80, 160]
        });
    
    const pageWidth = 80;
    const margin = 5;
    const availableWidth = pageWidth - (margin * 2);
    
    // Header Ticket
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("AXStore", pageWidth / 2, 10, { align: "center" });
    
    doc.setFontSize(8);
    doc.setFont("helvetica", "normal");
    doc.text("******************************************", pageWidth / 2, 14, { align: "center" });
    doc.text("FICHA DE PRODUCTO", pageWidth / 2, 18, { align: "center" });
    doc.text("******************************************", pageWidth / 2, 22, { align: "center" });
    
    // Imagen del Producto (Centrada)
    let yPos = 25;
    try {
        const imgUrl = "app/views/assets/images/" + (product.imagen || 'default.png');
        const imgData = await getBase64ImageFromUrl(imgUrl);
        const imgSize = 50; 
        const xImg = (pageWidth - imgSize) / 2;
        doc.addImage(imgData, "JPEG", xImg, yPos, imgSize, imgSize);
        yPos += imgSize + 5;
    } catch (err) {
        console.error("Error cargando imagen para PDF:", err);
        yPos += 5;
    }
    
    // Datos del Producto
    doc.setFontSize(10);
    doc.setFont("helvetica", "bold");
    const splitTitle = doc.splitTextToSize(product.nombre.toUpperCase(), availableWidth);
    doc.text(splitTitle, pageWidth / 2, yPos, { align: "center" });
    yPos += (splitTitle.length * 5) + 2;
    
    doc.setFontSize(8);
    doc.setFont("helvetica", "normal");
    doc.text(`CAT: ${product.nombre_categoria}`, pageWidth / 2, yPos, { align: "center" });
    yPos += 4;
    doc.text(`SKU: ${product.sku}`, pageWidth / 2, yPos, { align: "center" });
    yPos += 8;
    
    // Precio (Grande)
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text(`PRECIO: $${parseFloat(product.precio_venta).toFixed(2)}`, pageWidth / 2, yPos, { align: "center" });
    yPos += 10;
    
    // Línea divisoria
    doc.setFontSize(8);
    doc.text("------------------------------------------", pageWidth / 2, yPos, { align: "center" });
    yPos += 5;
    
    // Stock Info
    doc.setFontSize(9);
    doc.text(`STOCK DISPONIBLE: ${product.stock} UNI.`, pageWidth / 2, yPos, { align: "center" });
    yPos += 10;
    
    // Footer
    doc.setFontSize(7);
    doc.text("¡GRACIAS POR SU PREFERENCIA!", pageWidth / 2, yPos, { align: "center" });
    yPos += 4;
    doc.text(new Date().toLocaleString(), pageWidth / 2, yPos, { align: "center" });
    
    console.log("Guardando PDF...");
    doc.save(`Ticket_${product.sku}.pdf`);
    console.log("PDF guardado con éxito.");

    } catch (error) {
        console.error("Error fatal generando PDF:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error al generar PDF',
            text: 'Hubo un problema al crear el ticket. Revisa la consola para más detalles.'
        });
    }
}

// Helper para imagen
function getBase64ImageFromUrl(url) {
    return new Promise((resolve, reject) => {
        var img = new Image();
        img.setAttribute("crossOrigin", "anonymous");
        img.onload = () => {
            var canvas = document.createElement("canvas");
            canvas.width = img.width;
            canvas.height = img.height;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0);
            var dataURL = canvas.toDataURL("image/jpeg");
            resolve(dataURL);
        };
        img.onerror = error => reject(error);
        img.src = url;
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
    // Eventos de Categoría (Pills)
    $(document).on("click", ".catalog-pill", function() {
        $(".catalog-pill").removeClass("active");
        $(this).addClass("active");
        
        const category = $(this).data("category");
        $("#categoryFilter").val(category); // Sincronizar con el select oculto
        
        aplicarFiltros();
    });

    $("#searchInput").on("keyup", aplicarFiltros);
    $("#categoryFilter").on("change", function() {
        // Sincronizar Pills cuando cambia el select (por si se usa en móvil/consola)
        const val = $(this).val();
        $(`.catalog-pill[data-category="${val}"]`).click();
    });
    
    $("#statusFilter").on("change", aplicarFiltros);
    
    $("#clearSearch").on("click", function() {
        $("#searchInput").val("");
        $(".catalog-pill").first().click(); // Resetear a "TODOS"
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

    // Acción desde Quick View
    $(document).on("click", ".btnQuickView", function() {
        const id = $(this).data("id");
        abrirQuickView(id);
    });

    function abrirQuickView(id) {
        $.ajax({
            url: "app/controllers/productoController.php",
            type: "POST",
            data: { accion: "obtenerDetalleQuickView", id: id },
            dataType: "json",
            beforeSend: function() {
                Swal.fire({
                    title: 'Cargando...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            },
            success: function(response) {
                Swal.close();
                if (response.status === "success") {
                    const data = response.data;
                    const v = data.variante;
                    
                    console.log("Quick View Data:", v); // DEBUG: Ver qué llega del backend

                    // [MEJORA] Mostrar nombre del Padre + Variante
                    const nombrePadre = v.nombre_producto_padre || "Producto";
                    const nombreVariante = v.nombre || "";
                    
                    $("#qv-category").text(v.nombre_categoria);
                    $("#qv-sku").text("SKU: " + v.sku);
                    
                    // Actualizamos el título para que sea descriptivo
                    $("#qv-name").html(`<small class="text-muted d-block fs-6 mb-1">${nombrePadre}</small>${nombreVariante}`);
                    
                    $("#qv-price").text("$" + parseFloat(v.precio_venta).toFixed(2));
                    $("#qv-stock").text(v.stock + " unidades");
                    $("#qv-description").text(v.descripcion || "Sin descripción.");

                    // Galería
                    const $gallery = $("#qv-gallery-thumbs");
                    $gallery.empty();
                    const ruta = "app/views/assets/images/";
                    
                    if (data.imagenes.length > 0) {
                         $("#qv-main-img").attr("src", ruta + data.imagenes[0].ruta_imagen);
                         data.imagenes.forEach((img, idx) => {
                             $gallery.append(`<div class="quick-view-thumb ${idx===0?'active':''}"><img src="${ruta}${img.ruta_imagen}"></div>`);
                         });
                    } else {
                        $("#qv-main-img").attr("src", ruta + "default.png");
                    }
                    
                    // Atributos
                    const $attr = $("#qv-attributes");
                    $attr.empty();
                    if(data.atributos.length > 0){
                        let html = '<div class="d-flex flex-wrap gap-2">';
                        data.atributos.forEach(a => {
                            html += `<span class="badge bg-light text-dark border">${a.nombre_atributo}: ${a.valor}</span>`;
                        });
                        html += '</div>';
                        $attr.html(html);
                    }
                    
                    $("#modalQuickView").modal("show");
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function() {
                Swal.close();
                Swal.fire("Error", "No se pudo conectar", "error");
            }
        });
    }
    // Cambio de imagen en Quick View
    $(document).on("click", ".quick-view-thumb", function() {
        $(".quick-view-thumb").removeClass("active");
        $(this).addClass("active");
        const newSrc = $(this).find("img").attr("src");
        $("#qv-main-img").fadeOut(200, function() {
            $(this).attr("src", newSrc).fadeIn(200);
        });
    });

    // Google Maps dinámico en Registro

    // Google Maps dinámico en Registro
    $("#direccion").on("input", function() {
        const query = $(this).val().trim();
        const $btn = $("#verifyAddressBtn");
        
        if (query.length > 3) {
            $btn.attr("href", `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`);
            $btn.removeClass("d-none");
        } else {
            $btn.addClass("d-none");
        }
    });

    // Resetear botón de mapa al abrir/cerrar modal
    $("#modalSalidaProducto").on("show.bs.modal hidden.bs.modal", function() {
        $("#verifyAddressBtn").addClass("d-none").attr("href", "#");
    });

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

    // Reporte PDF
    $("#btnExportarPDF").on("click", function() {
        generarPDFCatalogo();
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

/**
 * Helper para convertir una URL de imagen a Base64
 * Útil para pdfmake que requiere imágenes en este formato
 */
function getBase64ImageFromURL(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.setAttribute("crossOrigin", "anonymous");
        img.onload = () => {
            const canvas = document.createElement("canvas");
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0);
            const dataURL = canvas.toDataURL("image/png");
            resolve(dataURL);
        };
        img.onerror = (error) => {
            console.warn("No se pudo cargar imagen para PDF:", url);
            // Retornar una imagen por defecto o vacía en caso de error
            resolve(null);
        };
        img.src = url;
    });
}

/**
 * Genera un PDF del catálogo de productos en formato de FICHAS CON IMAGEN
 * agrupado por categorías
 */
async function generarPDFCatalogo() {
    if (allProducts.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin Datos',
            text: 'No hay productos para generar el catálogo',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    Swal.fire({
        title: 'Generando Catálogo Visual',
        text: 'Procesando imágenes, por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // 1. Agrupar productos por categoría
        const productosPorCategoria = {};
        allProducts.forEach(product => {
            const cat = product.nombre_categoria || "Sin Categoría";
            if (!productosPorCategoria[cat]) {
                productosPorCategoria[cat] = [];
            }
            productosPorCategoria[cat].push(product);
        });

        // 2. Definir contenido inicial
        const pdfContent = [
            { text: 'CATÁLOGO VISUAL DE PRODUCTOS', style: 'header' },
            { text: 'Generado el: ' + new Date().toLocaleDateString(), style: 'subheader' },
            { text: '\n' }
        ];

        // 3. Procesar cada categoría
        const categoriasParaPdf = Object.keys(productosPorCategoria).sort();
        
        for (const categoria of categoriasParaPdf) {
            pdfContent.push({ text: categoria.toUpperCase(), style: 'categoryTitle' });
            
            const productos = productosPorCategoria[categoria].sort((a, b) => a.nombre.localeCompare(b.nombre));
            
            // Crear grid de fichas (3 por fila)
            const rows = [];
            for (let i = 0; i < productos.length; i += 3) {
                const chunk = productos.slice(i, i + 3);
                const columns = [];
                
                for (const p of chunk) {
                    const imgUrl = p.imagen ? `${ruta}${p.imagen}` : `${ruta}default.png`;
                    const base64Img = await getBase64ImageFromURL(imgUrl);
                    
                    columns.push({
                        stack: [
                            base64Img ? {
                                image: base64Img,
                                width: 100,
                                height: 100,
                                alignment: 'center',
                                margin: [0, 5, 0, 5]
                            } : { text: '\n(Imagen no disponible)\n', alignment: 'center', fontSize: 8, margin: [0, 40, 0, 40] },
                            { text: p.nombre_categoria || categoria, style: 'prodCategory' },
                            { text: p.nombre, style: 'prodName' },
                            { text: '$' + parseFloat(p.precio_venta).toFixed(2), style: 'prodPrice' }
                        ],
                        width: '33%',
                        margin: [5, 5, 5, 15],
                        canvas: [{ type: 'rect', x: 0, y: 0, w: 160, h: 175, r: 5, lineWidth: 0.5, lineColor: '#eee' }]
                    });
                }
                
                // Rellenar columnas vacías para mantener alineación
                while (columns.length < 3) {
                    columns.push({ text: '', width: '33%' });
                }
                
                rows.push({ columns: columns, columnGap: 10 });
            }
            
            pdfContent.push(...rows);
            pdfContent.push({ text: '\n', pageBreak: 'after' });
        }

        // Quitar el último pageBreak
        if (pdfContent.length > 0) {
            delete pdfContent[pdfContent.length - 1].pageBreak;
        }

        // 4. Configuración de estilos
        const docDefinition = {
            content: pdfContent,
            pageSize: 'A4',
            pageMargins: [40, 60, 40, 60],
            styles: {
                header: {
                    fontSize: 24,
                    bold: true,
                    alignment: 'center',
                    color: '#dc3545',
                    margin: [0, 0, 0, 5]
                },
                subheader: {
                    fontSize: 10,
                    alignment: 'center',
                    color: '#666',
                    margin: [0, 0, 0, 30]
                },
                categoryTitle: {
                    fontSize: 16,
                    bold: true,
                    color: '#333',
                    background: '#f4f4f4',
                    margin: [0, 20, 0, 15],
                    padding: [5, 5, 5, 5]
                },
                prodCategory: {
                    fontSize: 7,
                    bold: true,
                    alignment: 'center',
                    color: '#666',
                    margin: [0, 2, 0, 2]
                },
                prodName: {
                    fontSize: 9,
                    bold: true,
                    alignment: 'center',
                    margin: [0, 2, 0, 2],
                    color: '#222'
                },
                prodPrice: {
                    fontSize: 11,
                    bold: true,
                    alignment: 'center',
                    color: '#dc3545',
                    margin: [0, 0, 0, 5]
                }
            },
            footer: function(currentPage, pageCount) {
                return {
                    text: 'Página ' + currentPage.toString() + ' de ' + pageCount,
                    alignment: 'center',
                    fontSize: 9,
                    margin: [0, 20, 0, 0],
                    color: '#888'
                };
            }
        };

        window.pdfMake.createPdf(docDefinition).download('Catalogo_Visual_' + new Date().toISOString().split('T')[0] + '.pdf');
        
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: '¡Catálogo Creado!',
            text: 'El catálogo visual se ha descargado correctamente',
            timer: 3000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error("Error crítico en PDF:", error);
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error de Generación',
            text: 'Ocurrió un error al procesar las imágenes del catálogo',
            confirmButtonColor: '#dc3545'
        });
    }
}

// --- FUNCIONES PREMIUM ADICIONALES ---

function abrirQuickView(id) {
    $.ajax({
        url: "app/controllers/productoController.php",
        method: "POST",
        dataType: "json",
        data: { accion: "obtenerDetalleQuickView", id: id },
        beforeSend: function() {
            Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        },
        success: function(response) {
            Swal.close();
            if (response.status === "success") {
                const data = response.data;
                const v = data.variante;
                
                // Llenar datos básicos
                $("#qv-name").text(v.nombre);
                $("#qv-category").text(v.nombre_categoria);
                $("#qv-sku").text("SKU: " + v.sku);
                $("#qv-price").text("$" + parseFloat(v.precio_venta).toFixed(2));
                $("#qv-stock").text(v.stock + " unidades");
                $("#qv-description").text(v.descripcion || "Sin descripción disponible para este producto.");
                
                // Imágenes
                const $gallery = $("#qv-gallery-thumbs");
                $gallery.empty();
                
                if (data.imagenes.length > 0) {
                    $("#qv-main-img").attr("src", ruta + data.imagenes[0].ruta_imagen);
                    data.imagenes.forEach((img, index) => {
                        $gallery.append(`
                            <div class="quick-view-thumb ${index === 0 ? 'active' : ''}">
                                <img src="${ruta}${img.ruta_imagen}" alt="Thumbnail">
                            </div>
                        `);
                    });
                } else {
                    $("#qv-main-img").attr("src", ruta + "default.png");
                }

                // Atributos
                const $attrContainer = $("#qv-attributes");
                $attrContainer.empty();
                if (data.atributos.length > 0) {
                    $attrContainer.append('<h6 class="fw-bold text-muted small text-uppercase mb-2">Características</h6>');
                    let attrHtml = '<div class="row row-cols-2 g-2">';
                    data.atributos.forEach(attr => {
                        attrHtml += `
                            <div class="col">
                                <div class="p-2 border rounded bg-light small">
                                    <span class="text-muted">${attr.nombre_atributo}:</span> <br>
                                    <strong class="text-dark">${attr.valor}</strong>
                                </div>
                            </div>
                        `;
                    });
                    attrHtml += '</div>';
                    $attrContainer.append(attrHtml);
                }

                // Configurar botón de salida en el modal
                $("#btnSalidaFromQuick").data("id", v.id);

                $("#modalQuickView").modal("show");
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'No se pudo cargar el detalle.' });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error("Error QuickView:", error);
            Swal.fire({ icon: 'error', title: 'Error de Conexión', text: 'No se pudo conectar con el servidor.' });
        }
    });
}
