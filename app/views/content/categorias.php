<!DOCTYPE html>
<html lang="en">
<?php require_once "./app/views/inc/head.php"; ?>

<style>
    /* Alinea el selector de registros en una sola línea */
    .dataTables_length label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        /* Espacio entre el texto y el select */
        font-weight: 500;
    }

    /* Ajusta el ancho del selector para que no se vea gigante */
    .dataTables_length select {
        width: auto !important;
        display: inline-block;
    }

    /* Alinea el buscador a la derecha y lo hace más estético */
    .dataTables_filter {
        text-align: right;
    }

    .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    /* Espaciado para los botones de exportación */
    .buttons-html5 {
        margin-right: 5px;
    }
</style>

<body>
    <?php require_once "./app/views/inc/header.php"; ?>

    <section class="container fade-in" style="margin-top: 120px;">
        <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h2 class="section-title">Categorías Registradas</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                <i class="fas fa-plus"></i> Nueva Categoría
            </button>
        </div>

        <div class="luxury-table-container">
            <table class="luxury-table table" id="tablaCategorias" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCategoriaLabel">Registrar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCategoria">
                        <input type="hidden" id="id_categoria" name="id_categoria" value="0">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="btnEnviar">Guardar Categoría</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="app/views/assets/js/categoria.js?v=3"></script>

</body>

</html>