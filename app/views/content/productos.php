<!DOCTYPE html>
<html lang="es">
<?php require_once "./app/views/inc/head.php"; ?>

<style>
    /* Estilos de DataTables heredados de tu vista de categorías */
    .dataTables_length label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .dataTables_length select {
        width: auto !important;
        display: inline-block;
    }

    .dataTables_filter {
        text-align: right;
    }

    .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .buttons-html5 {
        margin-right: 5px;
    }

    /* Estilos para la distribución de formularios dentro del modal */
    .card-form {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        height: 100%;
    }

    .card-header-custom {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1.25rem;
    }

    .table-variantes-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }

    .card-body {
        padding: 1.5rem !important;
        /* Esto despega todo de los bordes internos */
    }

    /* Ajuste para que los inputs no se vean tan pegados entre sí verticalmente */
    .mb-3 {
        margin-bottom: 1.25rem !important;
    }

    /* Espaciado para el contenedor de atributos a la derecha */
    #contenedorAtributos {
        min-height: 150px;
        padding: 10px;
        /* Espacio interno para los selectores que inyectará jQuery */
        border-radius: 8px;
    }

    /* Margen para la tabla de variantes para que no toque los bordes del modal */
    .table-variantes-container {
        margin: 0 10px;
        /* Pequeño margen lateral */
        padding: 5px;
    }
</style>

<body>
    <?php require_once "./app/views/inc/header.php"; ?>

    <section class="container fade-in" style="margin-top: 120px;">
        <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h2 class="section-title">Gestión de Productos</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                <i class="fas fa-plus"></i> Registrar Producto
            </button>
        </div>

        <div class="luxury-table-container">
            <table class="luxury-table table" id="tablaProductos" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="modalProducto" aria-labelledby="modalProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-white">
                    <h5 class="modal-title fw-bold" id="modalProductoLabel">Configuración de Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProducto" enctype="multipart/form-data">
                        <input type="hidden" id="id_producto" name="id_producto" value="0">

                        <div class="row g-4">

                            <div class="col-md-7">
                                <div class="card-form shadow-sm">
                                    <div class="card-header-custom">
                                        <span class="small fw-bold text-primary"><i class="fas fa-edit me-2"></i>DATOS GENERALES</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label small">Nombre del Producto</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Amortiguador Kit" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small">Categoría</label>
                                                <select name="id_categoria" id="id_categoria" class="form-select" required>
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small">Estado</label>
                                                <select name="estado" id="estado" class="form-select">
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small">Descripción</label>
                                            <textarea name="descripcion" id="descripcion" class="form-control" rows="2" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="card-form shadow-sm d-flex flex-column h-100">

                                    <div class="card-header-custom d-flex justify-content-between align-items-center p-3 bg-white border-bottom">
                                        <span class="small fw-bold text-dark"><i class="fas fa-list me-2"></i>ATRIBUTOS</span>

                                        <div class="btn-group shadow-sm">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnNuevoAtributo"
                                                data-bs-toggle="tooltip" title="Configurar nuevos tipos de atributo">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarFilaAtributo"
                                                data-bs-toggle="tooltip" title="Añadir nueva fila de valor">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body bg-light-subtle flex-grow-1" style="max-height: 350px; overflow-y: auto;">
                                        <div id="contenedorAtributos">
                                            <p class="text-muted small text-center pt-4">Define los atributos para habilitar la matriz.</p>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-white border-top p-3">
                                        <button type="button" class="btn btn-dark w-100 fw-bold shadow-sm" id="btnGenerarMatriz"
                                            data-bs-toggle="tooltip" title="Actualizar matriz de variantes">
                                            <i class="fas fa-sync-alt me-2"></i> GENERAR MATRIZ
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-variantes-container shadow-sm">
                                    <div class="card-header-custom bg-dark text-white border-0 py-2">
                                        <span class="small fw-bold"><i class="fas fa-boxes me-2"></i>MATRIZ DE VARIANTES</span>
                                    </div>
                                    <table class="table table-sm table-hover align-middle mb-0" id="tablaVariantes">
                                        <thead class="table-light">
                                            <tr style="font-size: 0.8rem;">
                                                <th class="ps-3">Variante</th>
                                                <th width="140px">Imagen</th>
                                                <th width="110px">Precio</th>
                                                <th width="90px">Stock</th>
                                                <th width="40px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="cuerpoVariantes">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary px-4" id="btnEnviar">Guardar Todo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="app/views/assets/js/producto.js?v=3"></script>

</body>

</html>