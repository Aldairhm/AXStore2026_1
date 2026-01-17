<?php
// 1. Definimos el título dinámico antes de cargar el header
include  "./app/views/inc/head.php";
// 2. Cargamos la cabecera (que ya contiene el <head> y el menú)
include  "./app/views/inc/header.php";
?>

<section class="container fade-in" style="margin-top: 120px;">
    <div class="d-flex" style="justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <h2 class="section-title">Categorías Registradas</h2>
        <button class="btn" onclick="abrirModal()">
            <i class="fas fa-plus"></i> Nueva Categoría
        </button>
    </div>

    <div class="luxury-table-container">
        <table class="luxury-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaCategorias">
                <tr>
                    <td>#1</td>
                    <td style="font-family: 'Playfair Display', serif; font-weight: 600;">Accesorios de Carro</td>
                    <td style="color: var(--text-light);">Pasillo A</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<div id="modalCategoria" class="checkout-modal">
    <div class="checkout-content">
        <div class="checkout-header">
            <h3>Registrar Categoría</h3>
            <span class="close-checkout" onclick="cerrarModal()">&times;</span>
        </div>
        <div class="checkout-body">
            <form id="formCategoria">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <label>Descripcion</label>
                    <input type="text" name="ubicacion">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">Guardar</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// 3. Cargamos el pie de página (scripts y cierre de etiquetas)
require_once "./app/views/inc/footer.php";
?>

<script>
    function abrirModal() {
        document.getElementById('modalCategoria').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalCategoria').style.display = 'none';
    }
</script>