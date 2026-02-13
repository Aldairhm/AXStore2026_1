const ruta = "http://localhost/AXStore2026_1/app/views/assets/images/";
let allSalidas = [];
let filteredSalidas = [];
let currentPage = 1;
const itemsPerPage = 9;
let currentSalidaDetail = null; // Para almacenar la salida abierta en el modal

$(document).ready(function () {
    cargarTodasLasSalidas();
    setupEvents();
    setFechasIniciales();
});

// Establecer fechas iniciales (últimos 30 días)
function setFechasIniciales() {
    const hoy = new Date();
    const hace30Dias = new Date();
    hace30Dias.setDate(hoy.getDate() - 30);
    
    const offset = hoy.getTimezoneOffset() * 60000;
    const fechaHasta = new Date(hoy.getTime() - offset).toISOString().split('T')[0];
    const fechaDesde = new Date(hace30Dias.getTime() - offset).toISOString().split('T')[0];
    
    $("#fechaHasta").val(fechaHasta);
    $("#fechaDesde").val(fechaDesde);
}

// Cargar todas las salidas
function cargarTodasLasSalidas() {
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: { accion: "obtenerTodasLasSalidas" },
        success: function (response) {
            if (response.status === "success") {
                allSalidas = response.data;
                filteredSalidas = allSalidas;
                aplicarFiltros();
                calcularEstadisticas();
            } else {
                console.error("Error al cargar salidas");
                showNoResults();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la carga:", error);
            console.error("Respuesta del servidor:", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo cargar el historial de salidas',
                confirmButtonColor: '#dc3545'
            });
        },
    });
}

// Calcular estadísticas (excluyendo canceladas)
function calcularEstadisticas() {
    // Filtrar salidas activas (no canceladas)
    const salidasActivas = allSalidas.filter(s => s.estado !== 'Cancelado');

    const total = salidasActivas.length;
    const montoTotal = salidasActivas.reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
    const unidadesTotales = salidasActivas.reduce((sum, s) => sum + parseInt(s.cantidad || 0), 0);
    
    // Salidas de hoy (también solo activas)
    const offset = new Date().getTimezoneOffset() * 60000;
    const hoy = new Date(new Date().getTime() - offset).toISOString().split('T')[0];
    const salidasHoy = salidasActivas.filter(s => s.fecha_salida === hoy).length;
    
    $("#totalSalidas").text(total);
    $("#montoTotal").text("$" + montoTotal.toFixed(2));
    $("#unidadesTotales").text(unidadesTotales);
    $("#salidasHoy").text(salidasHoy);
}

// Aplicar filtros
function aplicarFiltros() {
    const searchTerm = $("#searchInput").val().toLowerCase().trim();
    const fechaDesde = $("#fechaDesde").val();
    const fechaHasta = $("#fechaHasta").val();
    const ordenar = $("#ordenar").val();
    let activeTab = $("#salidasTabs .nav-link.active").attr("id"); 
    // Fallback por si la pestaña no se detecta (para evitar mostrar historial por defecto)
    if (!activeTab) activeTab = 'pendientes-tab';

    filteredSalidas = allSalidas.filter(salida => {
        const estado = salida.estado || 'Pendiente';
        const matchesSearch = searchTerm === "" || 
            salida.sku.toLowerCase().includes(searchTerm) ||
            salida.nombre_producto.toLowerCase().includes(searchTerm) ||
            (salida.observaciones && salida.observaciones.toLowerCase().includes(searchTerm));

        const matchesFechaDesde = !fechaDesde || salida.fecha_salida >= fechaDesde;
        const matchesFechaHasta = !fechaHasta || salida.fecha_salida <= fechaHasta;

        // Filtro por pestaña
        let matchesTab = false;
        if (activeTab === 'pendientes-tab') {
            // Mostrar Pendiente, En camino, y Vencidos (que no sean Entregado/Cancelado)
            matchesTab = (estado !== 'Entregado' && estado !== 'Cancelado');
        } else if (activeTab === 'entregados-tab') {
            // Solo mostrar salidas Entregadas
            matchesTab = (estado === 'Entregado');
        } else if (activeTab === 'cancelados-tab') {
            // Solo mostrar salidas Canceladas
            matchesTab = (estado === 'Cancelado');
        }

        return matchesSearch && matchesFechaDesde && matchesFechaHasta && matchesTab;
    });

    // Ordenar
    switch(ordenar) {
        case 'fecha_desc':
            filteredSalidas.sort((a, b) => {
                const dateA = new Date(a.fecha_salida + ' ' + a.hora_salida);
                const dateB = new Date(b.fecha_salida + ' ' + b.hora_salida);
                return dateB - dateA;
            });
            break;
        case 'fecha_asc':
            filteredSalidas.sort((a, b) => {
                const dateA = new Date(a.fecha_salida + ' ' + a.hora_salida);
                const dateB = new Date(b.fecha_salida + ' ' + b.hora_salida);
                return dateA - dateB;
            });
            break;
        case 'monto_desc':
            filteredSalidas.sort((a, b) => parseFloat(b.total) - parseFloat(a.total));
            break;
        case 'monto_asc':
            filteredSalidas.sort((a, b) => parseFloat(a.total) - parseFloat(b.total));
            break;
    }

    currentPage = 1;
    renderSalidas();
    updateResultCount();
}

// Renderizar salidas con paginación
function renderSalidas() {
    const $salidasGrid = $("#salidas-grid");
    const $noResults = $("#noResults");
    
    $salidasGrid.empty();

    if (filteredSalidas.length === 0) {
        showNoResults();
        $("#paginationContainer").addClass("d-none");
        return;
    }

    $noResults.addClass("d-none");
    $salidasGrid.removeClass("d-none");

    // Calcular índices para paginación
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedSalidas = filteredSalidas.slice(startIndex, endIndex);

    $.each(paginatedSalidas, function (i, salida) {
        const card = crearCardSalida(salida);
        $salidasGrid.append(card);
    });

    renderPagination();
}

// Obtener badge de estado
// Obtener badge de estado (con lógica de vencimiento)
function getBadgeEstado(estado, fechaEntrega = null) {
    // Verificar vencimiento visualmente
    if (fechaEntrega && estado !== 'Entregado' && estado !== 'Cancelado') {
        const fechaEnt = new Date(fechaEntrega);
        const hoy = new Date();
        hoy.setHours(0,0,0,0);
        
        if (hoy > fechaEnt) {
            return '<span class="badge bg-danger">Vencido</span>';
        }
    }

    const badges = {
        'Pendiente': '<span class="badge bg-warning text-dark">Pendiente</span>',
        'En camino': '<span class="badge bg-primary">En camino</span>',
        'Entregado': '<span class="badge bg-success">Entregado</span>',
        'Cancelado': '<span class="badge bg-secondary">Cancelado</span>'
    };
    return badges[estado] || '<span class="badge bg-secondary">Desconocido</span>';
}

// Verificar si puede devolver (frontend)
function puedeDevolver(salida) {
    const estado = salida.estado || 'Pendiente';
    
    // No puede devolver si ya está cancelado
    if (estado === 'Cancelado') {
        return {
            puede: false,
            motivo: 'Salida cancelada'
        };
    }

    // No puede devolver si ya está entregado
    if (estado === 'Entregado') {
        return {
            puede: false,
            motivo: 'Producto ya entregado'
        };
    }

    /* 
    Se permite devolución solo para estados: Pendiente y En camino
    */

    if (salida.fecha_entrega) {
        const fechaEntrega = new Date(salida.fecha_entrega);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        // Calcular días restantes (puede ser negativo si venció, pero permitimos)
        const diffTime = fechaEntrega - hoy;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        return {
            puede: true,
            diasRestantes: diffDays
        };
    }

    // Si no tiene fecha de entrega, puede devolver
    return {
        puede: true,
        diasRestantes: null
    };
}

// Crear card de salida
function crearCardSalida(salida) {
    const total = parseFloat(salida.total);
    const subtotal = parseFloat(salida.subtotal);
    const precioEnvio = parseFloat(salida.precio_envio || 0);
    const costoExtra = parseFloat(salida.costo_extra || 0);
    
    // Formatear fecha y hora
    const fechaSalida = new Date(salida.fecha_salida + ' ' + salida.hora_salida);
    const fechaFormateada = fechaSalida.toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
    const horaFormateada = salida.hora_salida;

    // Manejar imagen
    const imagenSrc = salida.imagen ? `${ruta}${salida.imagen}` : `${ruta}default.png`;
    
    // Estado de la salida
    const estado = salida.estado || 'Pendiente';
    const badgeEstado = getBadgeEstado(estado, salida.fecha_entrega);
    
    // Verificar si puede devolver
    const validacionDevolucion = puedeDevolver(salida);
    const puedeDevol = validacionDevolucion.puede;
    
    // Badge de fecha de entrega y tiempo restante
    let badgeFechaEntrega = '';
    if (salida.fecha_entrega) {
        const fechaEnt = new Date(salida.fecha_entrega);
        const fechaEntFormateada = fechaEnt.toLocaleDateString('es-ES', { 
            day: 'numeric', 
            month: 'short' 
        });
        
        if (validacionDevolucion.diasRestantes !== null && validacionDevolucion.diasRestantes !== undefined) {
            const dias = validacionDevolucion.diasRestantes;
            let colorBadge = 'success';
            if (dias <= 3) colorBadge = 'danger';
            else if (dias <= 7) colorBadge = 'warning';
            
            badgeFechaEntrega = `
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-shipping-fast me-1"></i>Entrega: ${fechaEntFormateada}
                    </small>
                    ${puedeDevol ? `
                        <span class="badge bg-${colorBadge} ms-2">
                            <i class="fas fa-clock me-1"></i>${dias} día${dias !== 1 ? 's' : ''} para devolver
                        </span>
                    ` : ''}
                </div>
            `;
        }
    }

    return `
        <div class="col-md-6 col-lg-4">
            <div class="card card-salida h-100 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Header con fecha y estado -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-danger">ID: ${salida.id}</span>
                            ${badgeEstado}
                            <p class="text-muted small mb-0 mt-1">
                                <i class="far fa-calendar me-1"></i>${fechaFormateada}
                                <i class="far fa-clock ms-2 me-1"></i>${horaFormateada}
                            </p>
                            ${badgeFechaEntrega}
                        </div>
                        <button class="btn btn-sm btn-outline-primary btnVerDetalle" 
                                data-id="${salida.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Producto -->
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <img src="${imagenSrc}" 
                                 class="rounded border" 
                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                 alt="${salida.nombre_producto}"
                                 onerror="this.src='${ruta}default.png'">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">${salida.nombre_producto}</h6>
                            <p class="text-muted small mb-0">SKU: ${salida.sku}</p>
                            <span class="badge bg-secondary mt-1">${salida.cantidad} unidades</span>
                        </div>
                    </div>

                    <!-- Detalles financieros -->
                    <div class="border-top pt-3">
                        <div class="info-item">
                            <span class="text-muted small">Subtotal:</span>
                            <strong class="small">$${subtotal.toFixed(2)}</strong>
                        </div>
                        ${precioEnvio > 0 ? `
                        <div class="info-item">
                            <span class="text-muted small">Envío:</span>
                            <strong class="small text-info">$${precioEnvio.toFixed(2)}</strong>
                        </div>
                        ` : ''}
                        ${costoExtra > 0 ? `
                        <div class="info-item">
                            <span class="text-muted small">Extra:</span>
                            <strong class="small text-warning">$${costoExtra.toFixed(2)}</strong>
                        </div>
                        ` : ''}
                        <div class="info-item pt-2">
                            <span class="fw-bold">TOTAL:</span>
                            <strong class="text-danger fs-5">$${total.toFixed(2)}</strong>
                        </div>
                    </div>

                    <!-- Dirección si existe -->
                    ${salida.direccion && salida.direccion.trim() ? `
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <p class="text-muted small mb-0 flex-grow-1">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                            ${salida.direccion.substring(0, 40)}${salida.direccion.length > 40 ? '...' : ''}
                        </p>
                        <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(salida.direccion)}" 
                           target="_blank" 
                           class="btn btn-sm btn-light border ms-2 text-primary"
                           title="Ver en Google Maps">
                            <i class="fas fa-directions"></i>
                        </a>
                    </div>
                    ` : ''}

                    <!-- Botón de devolución -->
                    ${puedeDevol ? `
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-danger w-100 btnDevolverSalida" 
                                data-id="${salida.id}"
                                data-cantidad="${salida.cantidad}"
                                data-variante="${salida.id_variante}"
                                data-nombre="${salida.nombre_producto}"
                                data-fecha-entrega="${salida.fecha_entrega || ''}">
                            <i class="fas fa-undo me-1"></i>Devolver Stock
                        </button>
                    </div>
                    ` : estado !== 'Cancelado' ? `
                    <div class="mt-3">
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="fas fa-ban me-1"></i>${validacionDevolucion.motivo}
                        </button>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Renderizar paginación
function renderPagination() {
    const totalPages = Math.ceil(filteredSalidas.length / itemsPerPage);
    const $pagination = $("#pagination");
    const $paginationContainer = $("#paginationContainer");
    
    $pagination.empty();

    if (totalPages <= 1) {
        $paginationContainer.addClass("d-none");
        return;
    }

    $paginationContainer.removeClass("d-none");

    // Botón anterior
    $pagination.append(`
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `);

    // Números de página
    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 || 
            i === totalPages || 
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            $pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            $pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
    }

    // Botón siguiente
    $pagination.append(`
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `);
}

// Ver detalle de salida
function verDetalleSalida(id) {
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: { 
            accion: "obtenerDetalleSalida",
            id: id 
        },
        success: function (response) {
            if (response.status === "success") {
                mostrarModalDetalle(response.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el detalle',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Mostrar modal con detalle completo
function mostrarModalDetalle(salida) {
    currentSalidaDetail = salida; // Guardar para impresión
    const total = parseFloat(salida.total);
    const subtotal = parseFloat(salida.subtotal);
    const precioEnvio = parseFloat(salida.precio_envio || 0);
    const costoExtra = parseFloat(salida.costo_extra || 0);
    const precioUnitario = parseFloat(salida.precio_unitario);
    
    const fechaSalida = new Date(salida.fecha_salida + ' ' + salida.hora_salida);
    const fechaFormateada = fechaSalida.toLocaleDateString('es-ES', { 
        weekday: 'long',
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });

    // Manejar imagen
    const imagenSrc = salida.imagen ? `${ruta}${salida.imagen}` : `${ruta}default.png`;
    
    const estado = salida.estado || 'Pendiente';
    const badgeEstado = getBadgeEstado(estado, salida.fecha_entrega);

    // Información de devolución
    let infoDevolucion = '';
    if (salida.puede_devolver == 1 && salida.dias_para_devolucion !== null) {
        const dias = parseInt(salida.dias_para_devolucion);
        let colorAlert = 'success';
        if (dias <= 3) colorAlert = 'danger';
        else if (dias <= 7) colorAlert = 'warning';
        
        infoDevolucion = `
            <div class="alert alert-${colorAlert} mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Devolución disponible:</strong> Tienes ${dias} día${dias !== 1 ? 's' : ''} para procesar la devolución
            </div>
        `;
    } else if (salida.puede_devolver == 0 && estado !== 'Cancelado') {
        infoDevolucion = `
            <div class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Plazo vencido:</strong> El tiempo para devolución ha expirado
            </div>
        `;
    }

    const contenido = `
        <div class="row">
            <!-- Columna izquierda: Producto -->
            <div class="col-md-5 border-end">
                <h6 class="fw-bold text-muted mb-3">PRODUCTO</h6>
                
                <div class="text-center mb-3">
                    <img src="${imagenSrc}" 
                         class="img-fluid rounded border" 
                         style="max-height: 250px; object-fit: contain;" 
                         alt="${salida.nombre_producto}"
                         onerror="this.src='${ruta}default.png'">
                </div>

                <div class="bg-light p-3 rounded">
                    <h5 class="fw-bold mb-3">${salida.nombre_producto}</h5>
                    <p class="mb-2">
                        <strong>SKU:</strong> 
                        <span class="badge bg-dark">${salida.sku}</span>
                    </p>
                    ${salida.nombre_categoria ? `
                    <p class="mb-2">
                        <strong>Categoría:</strong> 
                        ${salida.nombre_categoria}
                    </p>
                    ` : ''}
                    <p class="mb-2">
                        <strong>Precio Unitario:</strong> 
                        <span class="text-primary fw-bold">$${precioUnitario.toFixed(2)}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Cantidad:</strong> 
                        <span class="badge bg-danger fs-6">${salida.cantidad} unidades</span>
                    </p>
                    <p class="mb-0">
                        <strong>Stock Actual:</strong> 
                        <span class="badge bg-success">${salida.stock_actual || 0} un.</span>
                    </p>
                </div>
            </div>

            <!-- Columna derecha: Detalles de salida -->
            <div class="col-md-7">
                <h6 class="fw-bold text-muted mb-3">DETALLES DE LA SALIDA</h6>

                ${infoDevolucion}

                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 p-3 rounded mb-3">
                        <p class="mb-2">
                            <i class="fas fa-hashtag me-2 text-danger"></i>
                            <strong>ID de Salida:</strong> #${salida.id}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-flag me-2 text-danger"></i>
                            <strong>Estado:</strong> ${badgeEstado}
                        </p>
                        <p class="mb-2">
                            <i class="far fa-calendar-alt me-2 text-danger"></i>
                            <strong>Fecha de Salida:</strong> ${fechaFormateada}
                        </p>
                        <p class="mb-2">
                            <i class="far fa-clock me-2 text-danger"></i>
                            <strong>Hora:</strong> ${salida.hora_salida}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-user me-2 text-danger"></i>
                            <strong>Registrado por:</strong> ${salida.usuario || 'N/A'}
                        </p>
                    </div>

                    ${salida.fecha_entrega ? `
                    <div class="alert alert-info">
                        <i class="fas fa-shipping-fast me-2"></i>
                        <strong>Entrega Estimada:</strong> 
                        ${new Date(salida.fecha_entrega).toLocaleDateString('es-ES', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        })}
                    </div>
                    ` : ''}

                    ${salida.direccion && salida.direccion.trim() ? `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="mb-0">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                Dirección de Entrega:
                            </strong>
                            <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(salida.direccion)}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary py-1 px-3 fs-7">
                                <i class="fas fa-map-marked-alt me-1"></i>Abrir en Google Maps
                            </a>
                        </div>
                        <p class="bg-light p-3 rounded mb-0">${salida.direccion}</p>
                    </div>
                    ` : ''}

                    ${salida.observaciones && salida.observaciones.trim() ? `
                    <div class="mb-3">
                        <strong class="d-block mb-2">
                            <i class="fas fa-sticky-note me-2 text-danger"></i>
                            Observaciones:
                        </strong>
                        <p class="bg-light p-3 rounded mb-0">${salida.observaciones}</p>
                    </div>
                    ` : ''}
                </div>

                <!-- Resumen financiero -->
                <div class="border rounded p-3 bg-light">
                    <h6 class="fw-bold mb-3">Resumen Financiero</h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (${salida.cantidad} × $${precioUnitario.toFixed(2)}):</span>
                        <strong>$${subtotal.toFixed(2)}</strong>
                    </div>
                    
                    ${precioEnvio > 0 ? `
                    <div class="d-flex justify-content-between mb-2">
                        <span>Precio de Envío:</span>
                        <strong class="text-info">$${precioEnvio.toFixed(2)}</strong>
                    </div>
                    ` : ''}
                    
                    ${costoExtra > 0 ? `
                    <div class="d-flex justify-content-between mb-2">
                        <span>Costo Extra:</span>
                        <strong class="text-warning">$${costoExtra.toFixed(2)}</strong>
                    </div>
                    ` : ''}
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">TOTAL:</span>
                        <strong class="text-danger fs-4">$${total.toFixed(2)}</strong>
                    </div>
                </div>

                <!-- Acciones según estado -->
                <div class="mt-4 pt-3 border-top">
                    ${estado === 'Pendiente' ? `
                    <button class="btn btn-primary w-100 btnCambiarEstado mb-2" 
                            data-id="${salida.id}" data-nuevo-estado="En camino" data-accion="Despachar">
                        <i class="fas fa-truck me-2"></i>Marcar como En Camino
                    </button>
                    ` : ''}

                    ${estado === 'En camino' ? `
                    <button class="btn btn-success w-100 btnCambiarEstado mb-2" 
                            data-id="${salida.id}" data-nuevo-estado="Entregado" data-accion="Confirmar Entrega">
                        <i class="fas fa-check-circle me-2"></i>Confirmar Entrega
                    </button>
                    ` : ''}

                    ${estado !== 'Cancelado' && estado !== 'Entregado' ? `
                    <button class="btn btn-outline-danger w-100 btnDevolverSalida" 
                            data-id="${salida.id}"
                            data-cantidad="${salida.cantidad}"
                            data-variante="${salida.id_variante}"
                            data-nombre="${salida.nombre_producto}"
                            data-fecha-entrega="${salida.fecha_entrega || ''}">
                        <i class="fas fa-undo me-2"></i>Devolver Stock (Cancelar)
                    </button>
                    ` : estado === 'Entregado' ? `
                    <div class="alert alert-success text-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>Producto Entregado - No se puede devolver
                    </div>
                    ` : `
                    <div class="alert alert-danger text-center mb-0">
                        <i class="fas fa-ban me-2"></i>Salida Cancelada
                    </div>
                    `}
                </div>
            </div>
        </div>
    `;

    $("#detalleContent").html(contenido);
    
    // El footer ya no necesita inyección dinámica para acciones principales,
    // pero podemos limpiarlo para evitar duplicados residuales.
    $("#modalDetalleSalida .modal-footer .btnDevolverSalida, #modalDetalleSalida .modal-footer .alert-devolucion").remove();
    
    const modalElement = document.getElementById('modalDetalleSalida');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Devolver salida (cambiar estado a Cancelado y restaurar stock)
function devolverSalida(id, cantidad, idVariante, nombreProducto, fechaEntrega) {
    // Verificar primero con el backend si puede devolver
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: {
            accion: "verificarPuedeDevolver",
            id: id
        },
        success: function(validacion) {
            if (validacion.status === "success" && validacion.puede_devolver) {
                // Mostrar días restantes si aplica
                let mensajeDias = '';
                if (validacion.dias_restantes !== null) {
                    mensajeDias = `<p class="text-info small">Plazo restante: ${validacion.dias_restantes} día(s)</p>`;
                }
                
                Swal.fire({
                    title: '¿Devolver Stock?',
                    html: `
                        <p>Se cancelará la salida y se devolverán <strong>${cantidad} unidades</strong> de:</p>
                        <p class="text-primary fw-bold">${nombreProducto}</p>
                        ${mensajeDias}
                        <p class="text-muted small">Esta acción no se puede deshacer</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, devolver',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        procesarDevolucion(id);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Devolución No Permitida',
                    text: validacion.motivo || 'No se puede procesar esta devolución',
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo verificar la devolución',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Procesar la devolución
function procesarDevolucion(id) {
    $.ajax({
        url: "app/controllers/salidaController.php",
        method: "POST",
        dataType: "json",
        data: {
            accion: "devolverSalida",
            id: id,
            motivo: "Devolución solicitada por usuario"
        },
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Devolución Exitosa',
                    html: `
                        <p>${response.message}</p>
                        <p class="text-muted small">Stock actualizado: ${response.stock_actualizado} unidades</p>
                    `,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    cargarTodasLasSalidas(); // Recargar datos
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
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo procesar la devolución',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Mostrar mensaje cuando no hay resultados
function showNoResults() {
    $("#salidas-grid").addClass("d-none");
    $("#noResults").removeClass("d-none");
    $("#paginationContainer").addClass("d-none");
}

// Actualizar contador
function updateResultCount() {
    $("#resultCount").text(filteredSalidas.length);
}

// Exportar Historial a PDF (Respeta Filtros)
async function exportarSalidasPDF() {
    if (filteredSalidas.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin Datos',
            text: 'No hay salidas para exportar con los filtros actuales',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    Swal.fire({
        title: 'Generando Reporte PDF',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const totalMonto = filteredSalidas.reduce((sum, s) => sum + parseFloat(s.total || 0), 0);
        const totalUnidades = filteredSalidas.reduce((sum, s) => sum + parseInt(s.cantidad || 0), 0);
        
        let activeTab = $("#salidasTabs .nav-link.active").text().trim();
        const fechaDesc = `Desde: ${$("#fechaDesde").val() || 'Inicio'} Hasta: ${$("#fechaHasta").val() || 'Hoy'}`;

        const docDefinition = {
            pageSize: 'A4',
            pageOrientation: 'landscape',
            pageMargins: [30, 40, 30, 40],
            header: function(currentPage, pageCount) {
                return {
                    text: 'AX STORE - Reporte de Historial de Salidas',
                    alignment: 'center',
                    margin: [0, 15, 0, 0],
                    fontSize: 8,
                    color: '#999'
                };
            },
            footer: function(currentPage, pageCount) {
                return {
                    text: `Página ${currentPage} de ${pageCount}`,
                    alignment: 'center',
                    margin: [0, 10, 0, 0],
                    fontSize: 8
                };
            },
            content: [
                {
                    columns: [
                        { text: 'REPORTE DE SALIDAS', style: 'mainHeader' },
                        { 
                            text: [
                                { text: 'Fecha Generación: ', bold: true },
                                new Date().toLocaleString()
                            ], 
                            alignment: 'right', 
                            fontSize: 9,
                            margin: [0, 5, 0, 0]
                        }
                    ]
                },
                {
                    canvas: [{ type: 'line', x1: 0, y1: 5, x2: 780, y2: 5, lineWidth: 1, lineColor: '#0b5ee1' }]
                },
                { text: '\n' },
                {
                    columns: [
                        {
                            stack: [
                                { text: 'FILTROS APLICADOS', style: 'sectionTitle' },
                                { text: `Pestaña: ${activeTab}`, fontSize: 9 },
                                { text: `Rango: ${fechaDesc}`, fontSize: 9 },
                                { text: `Búsqueda: ${$("#searchInput").val() || 'Ninguna'}`, fontSize: 9 }
                            ]
                        },
                        {
                            stack: [
                                { text: 'RESUMEN GENERAL', style: 'sectionTitle', alignment: 'right' },
                                { text: `Total Salidas: ${filteredSalidas.length}`, alignment: 'right', fontSize: 9 },
                                { text: `Cant. Total Unidades: ${totalUnidades}`, alignment: 'right', fontSize: 9 },
                                { text: `Monto Acumulado: $${totalMonto.toFixed(2)}`, alignment: 'right', fontSize: 11, bold: true, color: '#0b5ee1' }
                            ]
                        }
                    ]
                },
                { text: '\n' },
                {
                    table: {
                        headerRows: 1,
                        widths: [40, 60, '*', 50, 40, 50, 50, 60, 60, 60],
                        body: [
                            [
                                { text: 'ID', style: 'tableHeader' },
                                { text: 'FECHA', style: 'tableHeader' },
                                { text: 'PRODUCTO', style: 'tableHeader' },
                                { text: 'SKU', style: 'tableHeader' },
                                { text: 'CANT', style: 'tableHeader' },
                                { text: 'PRECIO', style: 'tableHeader' },
                                { text: 'ENVÍO', style: 'tableHeader' },
                                { text: 'TOTAL', style: 'tableHeader' },
                                { text: 'ESTADO', style: 'tableHeader' },
                                { text: 'USUARIO', style: 'tableHeader' }
                            ],
                            ...filteredSalidas.map(s => [
                                { text: s.id, alignment: 'center', fontSize: 8 },
                                { text: s.fecha_salida, alignment: 'center', fontSize: 8 },
                                { text: s.nombre_producto, fontSize: 8 },
                                { text: s.sku, alignment: 'center', fontSize: 7 },
                                { text: s.cantidad, alignment: 'center', fontSize: 8 },
                                { text: '$' + parseFloat(s.precio_unitario).toFixed(2), alignment: 'right', fontSize: 8 },
                                { text: '$' + parseFloat(s.precio_envio || 0).toFixed(2), alignment: 'right', fontSize: 8 },
                                { text: '$' + parseFloat(s.total).toFixed(2), alignment: 'right', fontSize: 8, bold: true },
                                { 
                                    text: s.estado.toUpperCase(), 
                                    alignment: 'center', 
                                    fontSize: 7, 
                                    bold: true,
                                    color: s.estado === 'Entregado' ? '#28a745' : s.estado === 'Cancelado' ? '#dc3545' : '#ffc107'
                                },
                                { text: s.usuario || 'N/A', fontSize: 8 }
                            ])
                        ]
                    },
                    layout: {
                        fillColor: function (rowIndex, node, columnIndex) {
                            return (rowIndex % 2 === 0) ? '#f8f9fa' : null;
                        },
                        hLineWidth: function (i, node) { return (i === 0 || i === node.table.body.length) ? 0.5 : 0.1; },
                        vLineWidth: function (i, node) { return 0; },
                        hLineColor: function (i, node) { return '#e2e8f0'; }
                    }
                }
            ],
            styles: {
                mainHeader: { fontSize: 18, bold: true, color: '#0b5ee1' },
                sectionTitle: { fontSize: 10, bold: true, color: '#333', margin: [0, 0, 0, 5] },
                tableHeader: {
                    bold: true,
                    fontSize: 9,
                    color: 'white',
                    fillColor: '#0b5ee1',
                    alignment: 'center',
                    margin: [0, 2, 0, 2]
                }
            }
        };

        const fechaArchivo = new Date().toISOString().split('T')[0];
        window.pdfMake.createPdf(docDefinition).download(`Reporte_Salidas_${fechaArchivo}.pdf`);
        Swal.close();

        Swal.fire({
            icon: 'success',
            title: 'Reporte Generado',
            text: 'El archivo PDF ha sido descargado correctamente.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

    } catch (error) {
        console.error("Error al generar PDF:", error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el reporte PDF' });
    }
}

// Cambiar estado con confirmación
function cambiarEstadoSalida(id, nuevoEstado, accionNombre) {
    Swal.fire({
        title: `¿${accionNombre}?`,
        text: `La salida pasará a estado "${nuevoEstado}"`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "app/controllers/salidaController.php",
                method: "POST",
                dataType: "json",
                data: {
                    accion: "cambiarEstado",
                    id: id,
                    nuevo_estado: nuevoEstado
                },
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Actualizado!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            cargarTodasLasSalidas();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetalleSalida'));
                            if (modal) modal.hide();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                }
            });
        }
    });
}

// Configurar eventos
function setupEvents() {
    // Filtros
    $("#searchInput").on("keyup", aplicarFiltros);
    $("#fechaDesde, #fechaHasta, #ordenar").on("change", aplicarFiltros);
    
    // Limpiar filtros
    $("#btnLimpiarFiltros").on("click", function() {
        $("#searchInput").val("");
        setFechasIniciales();
        $("#ordenar").val("fecha_desc");
        aplicarFiltros();
    });

    // Estadísticas interactivas
    $("#totalSalidas").closest(".stat-card").on("click", function() {
        $("#searchInput").val("");
        setFechasIniciales();
        $("#ordenar").val("fecha_desc");
        aplicarFiltros();
        Swal.fire({ icon: 'info', title: 'Filtros Reiniciados', text: 'Viendo todas las salidas', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });

    $("#montoTotal").closest(".stat-card").on("click", function() {
        $("#ordenar").val("monto_desc");
        aplicarFiltros();
        Swal.fire({ icon: 'info', title: 'Ordenado por Monto', text: 'Filtrando por mayores ingresos', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });

    $("#salidasHoy").closest(".stat-card").on("click", function() {
        const hoy = new Date().toISOString().split('T')[0];
        $("#fechaDesde, #fechaHasta").val(hoy);
        $("#ordenar").val("fecha_desc");
        aplicarFiltros();
        Swal.fire({ icon: 'info', title: 'Filtrado: Hoy', text: 'Viendo solo las salidas de este día', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });

    // Exportar
    $("#btnExportar").on("click", exportarSalidasPDF);

    // Cambio de pestaña
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        aplicarFiltros();
    });

    // Ver detalle
    $(document).on("click", ".btnVerDetalle", function() {
        const id = $(this).data("id");
        verDetalleSalida(id);
    });

    // Acción de botones de estado (Despachar, Entregar)
    $(document).on("click", ".btnCambiarEstado", function() {
        const id = $(this).data("id");
        const nuevoEstado = $(this).data("nuevo-estado");
        const accionNombre = $(this).data("accion");
        
        cambiarEstadoSalida(id, nuevoEstado, accionNombre);
    });

    // Devolver salida
    $(document).on("click", ".btnDevolverSalida", function() {
        const id = $(this).data("id");
        const cantidad = $(this).data("cantidad");
        const idVariante = $(this).data("variante");
        const nombreProducto = $(this).data("nombre");
        const fechaEntrega = $(this).data("fecha-entrega");
        devolverSalida(id, cantidad, idVariante, nombreProducto, fechaEntrega);
    });

    // Paginación
    $(document).on("click", ".pagination .page-link", function(e) {
        e.preventDefault();
        const page = parseInt($(this).data("page"));
        if (page && page !== currentPage) {
            currentPage = page;
            renderSalidas();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Imprimir detalle (Ticket PDF)
    $("#btnImprimirDetalle").on("click", function() {
        if (currentSalidaDetail) {
            generarPDFTicket(currentSalidaDetail);
        } else {
            window.print();
        }
    });
}

/**
 * Helper para convertir una URL de imagen a Base64
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
            resolve(null);
        };
        img.src = url;
    });
}

/**
 * Genera un Ticket PDF compacto (layout de 80mm) con pdfmake
 */
async function generarPDFTicket(salida) {
    Swal.fire({
        title: 'Generando Ticket',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // En este archivo 'ruta' ya está definido arriba
        const imgUrl = salida.imagen ? `${ruta}${salida.imagen}` : `${ruta}default.png`;
        const base64Img = await getBase64ImageFromURL(imgUrl);

        const docDefinition = {
            pageSize: { width: 226.77, height: 'auto' }, // ~80mm width
            pageMargins: [10, 10, 10, 10],
            content: [
                { text: 'AX STORE', style: 'storeName' },
                { text: 'Comprobante de Salida', style: 'ticketTitle' },
                { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 206.77, y2: 5, lineWidth: 0.5 }] },
                { text: '\n' },
                {
                    columns: [
                        { text: 'ID Salida:', bold: true, fontSize: 8 },
                        { text: '#' + salida.id, alignment: 'right', fontSize: 8 }
                    ]
                },
                {
                    columns: [
                        { text: 'Fecha:', bold: true, fontSize: 8 },
                        { text: salida.fecha_salida + ' ' + salida.hora_salida, alignment: 'right', fontSize: 8 }
                    ]
                },
                {
                    columns: [
                        { text: 'Estado:', bold: true, fontSize: 8 },
                        { text: (salida.estado || 'Pendiente').toUpperCase(), alignment: 'right', fontSize: 8, color: '#dc3545' }
                    ]
                },
                { text: '\n' },
                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 206.77, y2: 0, lineWidth: 0.5 }] },
                { text: 'PRODUCTO', style: 'sectionHeader' },
                base64Img ? {
                    image: base64Img,
                    width: 60,
                    alignment: 'center',
                    margin: [0, 5, 0, 5]
                } : {},
                { text: salida.nombre_producto, bold: true, fontSize: 9, alignment: 'center' },
                { text: 'SKU: ' + salida.sku, fontSize: 7, alignment: 'center', color: '#666' },
                { text: '\n' },
                {
                    table: {
                        widths: ['*', 'auto'],
                        body: [
                            [
                                { text: 'Cant x Precio', fontSize: 8 },
                                { text: salida.cantidad + ' x $' + parseFloat(salida.precio_unitario).toFixed(2), alignment: 'right', fontSize: 8 }
                            ],
                            [
                                { text: 'Subtotal', bold: true, fontSize: 8 },
                                { text: '$' + parseFloat(salida.subtotal).toFixed(2), alignment: 'right', bold: true, fontSize: 8 }
                            ]
                        ]
                    },
                    layout: 'noBorders'
                },
                { text: '\n' },
                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 206.77, y2: 0, lineWidth: 0.5 }] },
                { text: 'ENVÍO / ENTREGA', style: 'sectionHeader' },
                { text: 'Dirección:', bold: true, fontSize: 7 },
                { text: salida.direccion_entrega || 'N/A', fontSize: 7, margin: [0, 0, 0, 5] },
                {
                    columns: [
                        { text: 'Fecha Est. Entrega:', bold: true, fontSize: 7 },
                        { text: salida.fecha_entrega || 'N/A', alignment: 'right', fontSize: 7 }
                    ]
                },
                { text: '\n' },
                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 206.77, y2: 0, lineWidth: 1 }] },
                {
                    table: {
                        widths: ['*', 'auto'],
                        body: [
                            [
                                { text: 'Gastos de Envío', fontSize: 8 },
                                { text: '$' + parseFloat(salida.precio_envio || 0).toFixed(2), alignment: 'right', fontSize: 8 }
                            ],
                            [
                                { text: 'Costo Extra', fontSize: 8 },
                                { text: '$' + parseFloat(salida.costo_extra || 0).toFixed(2), alignment: 'right', fontSize: 8 }
                            ],
                            [
                                { text: 'TOTAL A PAGAR', style: 'totalLabel' },
                                { text: '$' + parseFloat(salida.total).toFixed(2), style: 'totalValue' }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 5, 0, 5]
                },
                { text: '\n' },
                { text: '¡Gracias por su preferencia!', alignment: 'center', fontSize: 8, italic: true },
                { text: 'AX STORE', alignment: 'center', fontSize: 7, margin: [0, 10, 0, 0], color: '#aaa' }
            ],
            styles: {
                storeName: { fontSize: 16, bold: true, alignment: 'center', margin: [0, 0, 0, 2] },
                ticketTitle: { fontSize: 10, alignment: 'center', color: '#666' },
                sectionHeader: { fontSize: 8, bold: true, margin: [0, 10, 0, 5], color: '#333' },
                totalLabel: { fontSize: 10, bold: true, margin: [0, 5, 0, 0] },
                totalValue: { fontSize: 12, bold: true, alignment: 'right', color: '#dc3545' }
            }
        };

        window.pdfMake.createPdf(docDefinition).download('Ticket_Salida_' + salida.id + '.pdf');
        Swal.close();

    } catch (error) {
        console.error("Error al generar ticket:", error);
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo generar el ticket PDF' });
    }
}

