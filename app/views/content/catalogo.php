<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "./app/views/inc/head.php"; ?>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/ss.css" />
</head>

<body class="bg-light">
    <?php require_once "./app/views/inc/header.php"; ?>
    
    <main class="py-5 mt-5">
        <section class="container fade-in">
            
            <!-- SMART TOOLBAR PILL (Experimental Design) -->
            <div class="smart-toolbar slide-in-pill">
                <div class="search-focus">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre o SKU...">
                    <button class="btn-clear-luxury me-2" type="button" id="clearSearch" title="Borrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="toolbar-divider d-none d-md-block"></div>
                
                <div class="pill-select-wrapper d-none d-md-block">
                    <select class="pill-select" id="categoryFilter">
                        <option value="all">Todas las Categorías</option>
                    </select>
                </div>
                
                <div class="action-hub mt-3 mt-md-0">
                    <a href="salidas" class="btn-pill-action" data-tooltip="Ver Salidas">
                        <i class="fas fa-shipping-fast"></i>
                    </a>
                    <button id="btnExportarPDF" class="btn-pill-action primary" data-tooltip="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>

            <!-- NAVEGACIÓN DE CATEGORÍAS (PILL SCROLL) -->
            <div class="category-segment-container fade-in" style="animation-delay: 0.1s;">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="text-uppercase small fw-bold letter-spacing-2 mb-0 me-3 text-muted">Colecciones</h5>
                    <div class="category-divider-line flex-grow-1"></div>
                </div>
                <div class="category-pill-wrapper" id="catalogo-categories-nav">
                    <!-- Las categorías se cargarán aquí como botones/pills -->
                    <div class="category-pill-skeleton"></div>
                </div>
            </div>

            <!-- Cabecera de Resultados (Minimal) -->
            <div class="mb-4 fade-in" style="animation-delay: 0.3s;">
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                    <i class="fas fa-box-open me-2 text-primary"></i>
                    <span id="resultCount" class="fw-bold">0</span> productos encontrados
                </span>
            </div>

            <!-- Grid de productos -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="product-grid"></div>

            <!-- Mensaje cuando no hay resultados (Luxury style) -->
            <div id="noResults" class="text-center py-5 d-none fade-in">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="font-luxury text-muted">Sin coincidencias</h3>
                <p class="text-muted mx-auto" style="max-width: 400px;">
                    No hemos encontrado productos que coincidan con tu búsqueda. Intenta simplificar los términos o cambiar de categoría.
                </p>
                <button class="btn btn-link text-luxury text-decoration-none fw-bold" onclick="document.getElementById('clearSearch').click()">
                    RESETEAR BÚSQUEDA
                </button>
            </div>

        </section>
    </main>

    <!-- Modal de Salida de Producto -->
    <div class="modal fade" id="modalSalidaProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-shipping-fast me-2"></i>Registrar Salida de Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formSalidaProducto" novalidate>
                    <div class="modal-body">
                        <input type="hidden" name="id_variante" id="id_variante_salida">
                        <input type="hidden" name="precio_unitario" id="precio_unitario_salida">

                        <div class="row">
                            <!-- Columna izquierda: Información del producto -->
                            <div class="col-md-5 border-end">
                                <h6 class="fw-bold text-muted mb-3">INFORMACIÓN DEL PRODUCTO</h6>
                                
                                <!-- Imagen del producto -->
                                <div class="text-center mb-3">
                                    <div class="border rounded p-2 bg-light" style="height: 200px;">
                                        <img id="imgProductoSalida" src="" class="img-fluid h-100" style="object-fit: contain;" alt="Producto">
                                    </div>
                                </div>

                                <!-- Detalles del producto -->
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-2"><strong>Producto:</strong> <span id="nombreProductoSalida"></span></p>
                                    <p class="mb-2"><strong>SKU:</strong> <span id="skuProductoSalida" class="badge bg-dark"></span></p>
                                    <p class="mb-2"><strong>Precio:</strong> <span id="precioProductoSalida" class="text-primary fw-bold"></span></p>
                                    <p class="mb-0">
                                        <strong>Stock Disponible:</strong> 
                                        <span id="stockProductoSalida" class="badge bg-success"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Columna derecha: Formulario de salida -->
                            <div class="col-md-7">
                                <h6 class="fw-bold text-muted mb-3">DATOS DE LA SALIDA</h6>

                                <!-- Cantidad -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Cantidad a Despachar <span class="text-danger">*</span></label>
                                    <input type="number" name="cantidad" id="cantidad_salida" class="form-control" min="1">
                                    <small class="text-muted">Unidades que saldrán del inventario</small>
                                </div>

                                <div class="row g-2 mb-3">
                                    <!-- Fecha de salida -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Fecha de Salida <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_salida" id="fecha_salida" class="form-control">
                                    </div>

                                    <!-- Hora de salida -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Hora de Salida <span class="text-danger">*</span></label>
                                        <input type="time" name="hora_salida" id="hora_salida" class="form-control">
                                    </div>
                                </div>

                                <!-- Fecha de entrega estimada -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Fecha de Entrega Estimada <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                                </div>

                                <!-- Dirección de entrega -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold small mb-0">Dirección de Entrega <span class="text-danger">*</span></label>
                                        <a href="#" id="verifyAddressBtn" target="_blank" class="text-primary small text-decoration-none d-none">
                                            <i class="fas fa-map-marked-alt me-1"></i>Verificar en Mapa
                                        </a>
                                    </div>
                                    <textarea name="direccion" id="direccion" class="form-control" rows="2" placeholder="Ingrese la dirección completa"></textarea>
                                </div>

                                <div class="row g-2 mb-3">
                                    <!-- Precio de envío -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Precio de Envío ($) <span class="text-danger">*</span></label>
                                        <input type="number" name="precio_envio" id="precio_envio" class="form-control" step="0.01" value="0.00">
                                    </div>

                                    <!-- Costo extra -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Costo Extra ($)</label>
                                        <input type="number" name="costo_extra" id="costo_extra" class="form-control" step="0.01" value="0.00">
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Observaciones</label>
                                    <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Notas adicionales sobre la salida"></textarea>
                                </div>

                                <!-- Resumen de totales -->
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Subtotal:</span>
                                        <strong id="subtotalSalida">$0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Envío:</span>
                                        <strong id="envioSalida">$0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Extra:</span>
                                        <strong id="extraSalida">$0.00</strong>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">TOTAL:</span>
                                        <strong class="text-primary fs-5" id="totalSalida">$0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" id="btnRegistrarSalida">
                            <i class="fas fa-check me-1"></i> Registrar Salida
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Quick View (Vista Rápida Premium) -->
    <?php include 'app/views/inc/modal_quickview.php'; ?>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="app/views/assets/js/catalogo.js"></script>
</body>

</html>