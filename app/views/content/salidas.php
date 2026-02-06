<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "./app/views/inc/head.php"; ?>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/ss.css" />
    <style>
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }
        .card-salida {
            transition: all 0.3s ease;
            border-left: 4px solid #dc3545;
        }
        .card-salida:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body class="bg-light">
    <?php require_once "./app/views/inc/header.php"; ?>
    
    <main class="py-5 mt-5">
        <section class="container fade-in">
            
            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fas fa-truck text-danger me-2"></i>Historial de Salidas
                    </h2>
                    <p class="text-muted mb-0">Gestión y seguimiento de salidas de productos</p>
                </div>
                
            </div>

            <!-- Estadísticas resumidas -->
            <div class="row g-3 mb-4" id="statsCards">
                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #dc3545 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Total Salidas</p>
                                    <h3 class="fw-bold mb-0" id="totalSalidas">0</h3>
                                </div>
                                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-box-open fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #28a745 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Monto Total</p>
                                    <h3 class="fw-bold mb-0 text-success" id="montoTotal">$0.00</h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #ffc107 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Unidades Despachadas</p>
                                    <h3 class="fw-bold mb-0 text-warning" id="unidadesTotales">0</h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-cubes fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #17a2b8 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Hoy</p>
                                    <h3 class="fw-bold mb-0 text-info" id="salidasHoy">0</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-calendar-day fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="SKU, producto...">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Fecha Desde</label>
                            <input type="date" class="form-control" id="fechaDesde">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fechaHasta">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Ordenar por</label>
                            <select class="form-select" id="ordenar">
                                <option value="fecha_desc">Más recientes</option>
                                <option value="fecha_asc">Más antiguos</option>
                                <option value="monto_desc">Mayor monto</option>
                                <option value="monto_asc">Menor monto</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-outline-secondary me-2" id="btnLimpiarFiltros">
                                <i class="fas fa-eraser me-1"></i>Limpiar
                            </button>
                            <button class="btn btn-success" id="btnExportar">
                                <i class="fas fa-file-excel me-1"></i>Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contador de resultados -->
            <div class="mb-3">
                <small class="text-muted">
                    <span id="resultCount">0</span> salidas encontradas
                </small>
            </div>

            <!-- Grid de salidas -->
            <div class="row g-4" id="salidas-grid">
                <!-- Aquí se cargarán dinámicamente las salidas -->
            </div>

            <!-- Mensaje cuando no hay resultados -->
            <div id="noResults" class="text-center py-5 d-none">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron salidas</h5>
                <p class="text-muted">No hay salidas registradas con los filtros aplicados</p>
            </div>

            <!-- Paginación -->
            <nav aria-label="Paginación de salidas" class="mt-4 d-none" id="paginationContainer">
                <ul class="pagination justify-content-center" id="pagination">
                </ul>
            </nav>

        </section>
    </main>

    <!-- Modal de Detalles de Salida -->
    <div class="modal fade" id="modalDetalleSalida" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>Detalle de Salida
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="detalleContent">
                    <!-- Se cargará dinámicamente -->
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnImprimirDetalle">
                        <i class="fas fa-print me-1"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="app/views/assets/js/salidas.js"></script>
</body>

</html>