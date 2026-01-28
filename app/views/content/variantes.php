<!DOCTYPE html>
<html lang="en">

<?php $id = isset($_GET['id']) ? (int)($_GET['id']) : 0 ?>

<head>
    <?php require_once "./app/views/inc/head.php"; ?>
</head>

<body class="bg-light">
    <?php require_once "./app/views/inc/header.php"; ?>
    <main class="py-5 mt-5" id="variantes-container" data-id="<?php echo $id; ?>">
        <section class="container fade-in">
            <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2 class="section-title">Variantes Registradas</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                    <i class="fas fa-plus"></i> Nueva Variante
                </button>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="product-grid">

            </div>
        </section>
    </main>

    <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom-0">
                    <h5 class="modal-title fw-bold" id="modalProductoLabel">
                        <i class="fas fa-cart-plus text-primary me-2"></i>Configuración de Nuevo Producto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="formProducto" enctype="multipart/form-data">
                        <input type="hidden" name="id_producto" id="id_producto" value="0">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="card-form shadow-sm h-100">
                                    <div class="card-header-custom py-2">
                                        <span class="small fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>DATOS GENERALES</span>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Nombre del Producto</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" style="width: 80%;" required>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold">Categoría</label>
                                                <select name="id_categoria" id="id_categoria" class="form-select form-select-sm select-categoria" required></select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold">Estado</label>
                                                <select name="estado" id="estado" class="form-select form-select-sm">
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small fw-bold">Descripción</label>
                                            <textarea name="descripcion" id="descripcion" class="form-control form-control-sm" rows="2" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="card-form shadow-sm d-flex flex-column h-100">
                                    <div class="card-header-custom d-flex justify-content-between align-items-center py-1">
                                        <span class="small fw-bold text-dark"><i class="fas fa-tags me-2"></i>ATRIBUTOS</span>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnNuevoAtributo"><i class="fas fa-cog"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarFilaAtributo"><i class="fas fa-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-dark" id="btnGenerarMatriz">
                                                <i class="fas fa-sync-alt"></i> Generar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body bg-light-subtle p-2"
                                        style="min-height: 100px; max-height: 220px; overflow-y: auto; overflow-x: hidden;">
                                        <div id="contenedorAtributos">
                                            <p class="text-muted small text-center pt-2 mb-0" id="msgSinAtributos">Define atributos para la matriz.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="table-variantes-container shadow-sm bg-white border rounded">
                                    <div class="card-header-custom bg-dark text-white border-0 py-1">
                                        <span class="small fw-bold"><i class="fas fa-th-list me-2"></i>MATRIZ DE VARIANTES</span>
                                    </div>
                                    <div class="table-responsive" style="max-height: 300px;">
                                        <table class="table table-sm table-hover align-middle mb-0" id="tablaVariantes">
                                            <thead class="table-light sticky-top">
                                                <tr style="font-size: 0.75rem; text-transform: uppercase;">
                                                    <th class="ps-3">Variante</th>
                                                    <th width="120px">SKU</th>
                                                    <th width="120px">Imagen</th>
                                                    <th width="100px">P. Compra</th>
                                                    <th width="100px">P. Venta</th>
                                                    <th width="80px">Stock</th>
                                                    <th width="80px">Reserva</th>
                                                    <th width="80px">Comision</th>
                                                    <th width="40px"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="cuerpoVariantes" style="font-size: 0.85rem;">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnGuardarRegistro">
                                <i class="fas fa-save me-1"></i> Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarVariante" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title fw-bold text-uppercase">
                        <i class="fas fa-edit me-2"></i>Edición Técnica de Variante
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formEditarVariante">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_variante_edit" id="id_variante_edit">
                        <input type="hidden" name="id_producto" id="id_producto">

                        <div class="row">
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">Datos de Control</h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">SKU (Código Único)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="sku_edit" id="sku_edit" class="form-control fw-bold border-primary" readonly>
                                        <button class="btn btn-outline-primary" type="button" id="btnRegenerarSku" title="Sincronizar con atributos">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.7rem;">El SKU es basado en los atributos y el nombre del producto.</small>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Stock Actual</label>
                                        <input type="number" id="stock_actual_edit" name="stock_actual_edit" class="form-control form-control-sm bg-light fw-bold" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Reserva (Mín.)</label>
                                        <input type="number" name="stock_minimo_edit" id="stock_minimo_edit" class="form-control form-control-sm border-warning" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Precio de Venta ($)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-success text-white border-success">$</span>
                                        <input type="number" name="precio_venta_edit" id="precio_venta_edit" class="form-control form-control-sm border-success fw-bold" step="0.01" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Comisión (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="comision_edit" id="comision_edit" class="form-control form-control-sm border-info" step="0.01" required>
                                        <span class="input-group-text bg-info text-white border-info">%</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Cálculo aplicado sobre el precio de venta final.</small>
                                </div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <div class="mb-4">
                                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Atributos de Variante</h6>
                                    <div id="contenedorAtributosEdit" class="p-3 border rounded bg-light shadow-sm" style="min-height: 100px;">
                                        <div class="text-center py-3 text-muted">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Cargando esquema...
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Representación Visual</h6>
                                    <div class="text-center p-3 border rounded bg-white shadow-sm">
                                        <div class="mb-3 mx-auto border rounded" style="width: 120px; height: 120px; overflow: hidden;">
                                            <img id="imgPrevEdit" src="" class="w-100 h-100" style="object-fit: contain;">
                                        </div>
                                        <input type="file" name="foto_edit" id="foto_edit" class="form-control form-control-sm" accept="image/*">
                                        <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;">Cambie la imagen solo si es necesario.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-sm btn-link text-muted" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-dark px-4 rounded-pill shadow-sm">
                            <i class="fas fa-save me-1"></i> APLICAR CAMBIOS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="app/views/assets/js/variantes.js?v=55"></script>
    <script src="app/views/assets/js/producto.js?v=44"></script>
</body>

</html>