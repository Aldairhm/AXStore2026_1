<!-- Modal de Quick View (Vista Rápida) Shared -->
<div class="modal fade modal-quickview" id="modalQuickView" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-index-10" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="row g-0">
                    <!-- Galería Izquierda -->
                    <div class="col-lg-7">
                        <div class="quickview-img-large">
                            <img id="qv-main-img" src="" alt="Vista Rápida">
                        </div>
                        <div class="px-4 pb-4">
                            <div class="quick-view-gallery" id="qv-gallery-thumbs">
                                <!-- Miniaturas dinámicas -->
                            </div>
                        </div>
                    </div>
                    <!-- Información Derecha -->
                    <div class="col-lg-5 bg-white p-4 p-md-5">
                        <div class="mb-2">
                            <span id="qv-category" class="badge bg-light text-dark border">Categoría</span>
                            <span id="qv-sku" class="badge bg-dark ms-2">SKU</span>
                        </div>
                        <h2 id="qv-name" class="fw-bold mb-3">Nombre del Producto</h2>
                        <h3 id="qv-price" class="text-primary fw-bold mb-4">$0.00</h3>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted small text-uppercase mb-2">Descripción</h6>
                            <p id="qv-description" class="text-muted lh-lg">No hay descripción disponible.</p>
                        </div>

                        <div id="qv-attributes" class="mb-4">
                            <!-- Atributos dinámicos -->
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="bg-light p-3 rounded d-flex align-items-center h-100">
                                    <i class="fas fa-warehouse text-success me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Existencias</small>
                                        <strong id="qv-stock" class="fs-6">0 un.</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-3 rounded d-flex align-items-center h-100">
                                    <i class="fas fa-hand-holding-usd text-warning me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Reserva</small>
                                        <strong id="qv-reserva" class="fs-6">0 un.</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-grid gap-2">
                            <!-- Aquí irían botones adicionales como "Agregar al Carrito" -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
