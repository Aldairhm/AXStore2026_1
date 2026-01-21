<!DOCTYPE html>
<html lang="es">
<?php require_once "./app/views/inc/head.php"; ?>

<style>
    .dataTables_length label, .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }
    .dataTables_length select { width: auto !important; }
    .dataTables_filter { text-align: right; }
    .buttons-html5 { margin-right: 5px; }
</style>

<body>
    <?php require_once "./app/views/inc/header.php"; ?>

    <section class="container fade-in" style="margin-top: 120px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h2 class="section-title mb-0">Gestión de Usuarios</h2>
            
            <button class="btn btn-primary btn-abrir-modal-usuario">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </button>
        </div>

        <div class="luxury-table-container">
            <table class="luxury-table table table-hover" id="tablaUsuarios" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario (Email)</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="tituloModal">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario" autocomplete="off">
                        <input type="hidden" id="id" name="id" value="0">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_real" name="nombre_real" required placeholder="Ej: Juan Pérez">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Correo Electrónico (Usuario)</label>
                            <input type="email" class="form-control" id="username" name="username" required placeholder="correo@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 8 caracteres">
                            <small class="text-muted" id="msgPassword">Dejar vacío para mantener la actual al editar.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Rol</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="vendedor">Vendedor</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    
    <script src="<?php echo APP_URL; ?>app/ajax/js/usuario.js"></script>
</body>
</html>