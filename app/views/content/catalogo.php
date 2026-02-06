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
            
            <!-- Barra de búsqueda y filtros -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar productos...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="all">Todas las categorías</option>
                    </select>
                </div>

<div class="col-md-4 mb-3">
               <li>
                        <a href="salidas">
                            <span>Salidas</span>

                        </a>
                    </li>
                 </div>
            </div>

            <!-- Contador de resultados -->
            <div class="mb-3">
                <small class="text-muted">
                    <span id="resultCount">0</span> productos encontrados
                </small>
            </div>

            <!-- Grid de productos -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="product-grid"></div>

            <!-- Mensaje cuando no hay resultados -->
            <div id="noResults" class="text-center py-5 d-none">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron productos</h5>
                <p class="text-muted">Intenta con otros términos de búsqueda</p>
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

                <form id="formSalidaProducto">
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
                                    <input type="number" name="cantidad" id="cantidad_salida" class="form-control" min="1" required>
                                    <small class="text-muted">Unidades que saldrán del inventario</small>
                                </div>

                                <div class="row g-2 mb-3">
                                    <!-- Fecha de salida -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Fecha de Salida <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_salida" id="fecha_salida" class="form-control" required>
                                    </div>

                                    <!-- Hora de salida -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Hora de Salida <span class="text-danger">*</span></label>
                                        <input type="time" name="hora_salida" id="hora_salida" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Fecha de entrega estimada -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Fecha de Entrega Estimada</label>
                                    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control">
                                </div>

                                <!-- Dirección de entrega -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Dirección de Entrega</label>
                                    <textarea name="direccion" id="direccion" class="form-control" rows="2" placeholder="Ingrese la dirección completa"></textarea>
                                </div>

                                <div class="row g-2 mb-3">
                                    <!-- Precio de envío -->
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Precio de Envío ($)</label>
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

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="app/views/assets/js/catalogo.js"></script>
</body>

</html>