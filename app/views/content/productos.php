<!DOCTYPE html>
<html lang="es">
<?php require_once "./app/views/inc/head.php"; ?>

<body>
    <?php require_once "./app/views/inc/header.php"; ?>

    <section class="container fade-in" style="margin-top: 120px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title"><i class="fas fa-boxes me-2"></i>Gestión de Productos</h2>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalProducto" data-modo="nuevo">
                <i class="fas fa-plus-circle me-1"></i> Registrar Producto
            </button>
        </div>
        <div class="luxury-table-container shadow-sm p-3 bg-white rounded">
            <table class="luxury-table table w-100" id="tablaProductos">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Descipcion</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>

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

    <div class="modal fade" id="modalProductoEdicion" tabindex="-1" aria-labelledby="modalProductoEdicionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white">
                    <h5 class="modal-title fw-bold" id="modalProductoEdicionLabel">
                        <i class="fas fa-edit text-primary me-2"></i>Editar Información
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProductoEdicion" method="POST">
                        <input type="hidden" id="id_producto_edit" name="id_producto">
                        <div class="card border-0 bg-light-subtle shadow-sm">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted"><i class="fas fa-tag me-1"></i>Nombre del Producto</label>
                                        <input type="text" name="nombre" id="nombre_edit" class="form-control border-0 shadow-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted"><i class="fas fa-list me-1"></i>Categoría</label>
                                        <select name="id_categoria" id="id_categoria_edit" class="form-select border-0 shadow-sm select-categoria" required></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted"><i class="fas fa-toggle-on me-1"></i>Estado</label>
                                        <select name="estado" id="estado_edit" class="form-select border-0 shadow-sm">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted"><i class="fas fa-align-left me-1"></i>Descripción General</label>
                                        <textarea name="descripcion" id="descripcion_edit" class="form-control border-0 shadow-sm" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnActualizar">
                                <i class="fas fa-save me-2"></i>Actualizar Datos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="app/views/assets/js/producto.js?v=97"></script>
    
</body>

</html>