const CTRL_USUARIO = 'app/controllers/usuarioController.php';

$(document).ready(function () {

    // -------------------------------------------------------------------------
    // 1. FUNCIONES DE UTILIDAD Y VALIDACIÓN
    // -------------------------------------------------------------------------

    function limpiarErroresFormulario() {
        $('#nombre_real, #username, #rol, #password, #estado').removeClass('is-invalid');
    }

    function validarFormularioUsuario(isEdit) {
        limpiarErroresFormulario();

        const nombre   = $('#nombre_real').val().trim();
        const username = $('#username').val().trim(); // Es el correo
        const rol      = $('#rol').val();
        const password = $('#password').val();

        // Validar Nombre
        if (nombre === '') {
            $('#nombre_real').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Dato requerido',
                text: 'El nombre real del usuario es obligatorio'
            });
            return false;
        }

        // Validar Correo (Username)
        if (username === '') {
            $('#username').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Dato requerido',
                text: 'El correo electrónico es obligatorio'
            });
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(username)) {
            $('#username').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Formato incorrecto',
                text: 'Ingrese un correo electrónico válido'
            });
            return false;
        }

        // Validar Rol
        if (!rol || rol === '') {
            $('#rol').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Dato requerido',
                text: 'Debe seleccionar un rol para el usuario'
            });
            return false;
        }

        // Validar Contraseña (Solo obligatoria si es Nuevo Usuario)
        if (!isEdit && (password.trim() === '')) {
            $('#password').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Contraseña requerida',
                text: 'Debe asignar una contraseña al nuevo usuario'
            });
            return false;
        }

        // Validar Longitud de Contraseña (si se escribió algo)
        if (password.trim() !== '' && password.length < 8) {
            $('#password').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Seguridad',
                text: 'La contraseña debe tener al menos 8 caracteres'
            });
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // 2. CONFIGURACIÓN DE ELEMENTOS Y DATATABLE
    // -------------------------------------------------------------------------

    const modalUsuarioEl = document.getElementById('modalUsuario'); // Asegúrate que tu HTML tenga este ID
    const modalUsuario   = new bootstrap.Modal(modalUsuarioEl);

    // Inicializar DataTable
    const tabla = $('#tablaUsuarios').DataTable({
        ajax: {
            url: `${CTRL_USUARIO}?opcion=listar`,
            type: 'GET',
            dataType: 'json',
            dataSrc: function (json) {
                if (json.status === 'success') return json.data;
                console.warn(json.message || 'Sin datos');
                return [];
            },
            error: e => console.error(e.responseText)
        },
        language: { url: 'app/ajax/idioma.json' },
        dom: "<'row mb-3'<'col-md-6'l><'col-md-6 text-end'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        aaSorting: [], // Sin orden inicial forzado
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, 'Todos']],
        pageLength: 5,
        responsive: true,
        columns: [
            { data: 'nombre_real' },
            { data: 'username' },
            { 
                data: 'rol',
                className: 'text-center',
                render: function (data) {
                    // Estilos según el rol
                    let badgeClass = 'bg-secondary';
                    if(data === 'administrador') badgeClass = 'bg-primary';
                    if(data === 'vendedor') badgeClass = 'bg-info text-dark';
                    
                    return `<span class="badge ${badgeClass} text-uppercase">${data}</span>`;
                }
            },
            { 
                data: 'estado',
                className: 'text-center',
                render: function (data, type, row) {
                    const isActive   = data == 1;
                    const badgeClass = isActive ? 'bg-success' : 'bg-danger';
                    const badgeText  = isActive ? 'Activo' : 'Inactivo';
                    const checked    = isActive ? 'checked' : '';

                    // Switch interactivo
                    return `
                    <div class="d-inline-flex align-items-center gap-2">
                        <span class="badge ${badgeClass} mb-0" style="width:60px;">${badgeText}</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input switch-estado" 
                                   type="checkbox" 
                                   data-id="${row.id}" 
                                   ${checked}>
                        </div>
                    </div>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                    <button type="button" class="btn btn-sm btn-warning btn-editar" data-id="${row.id}">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id}">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>`;
                }
            }
        ]
    });

    const $form   = $('#formUsuario');
    const $id     = $('#id'); // Input hidden para el ID
    const $titulo = $('#tituloModal'); // Título del Modal

    // -------------------------------------------------------------------------
    // 3. EVENTOS DEL DOM
    // -------------------------------------------------------------------------

    // Botón "Nuevo Usuario"
    // Asume que tienes un botón con onclick="abrirModalUsuario()" o id="btnNuevoUsuario"
    // Si usas el onclick del HTML anterior, podemos enlazarlo aquí si le pones id.
    // O mejor, usa un selector global para el botón que abre el modal.
    $(document).on('click', '.btn-abrir-modal-usuario', function() { // Añade esta clase a tu botón de "Nuevo Usuario"
        $titulo.text('Nuevo Usuario');
        $form[0].reset();
        $id.val('0'); // 0 indica nuevo
        limpiarErroresFormulario();

        // Mostrar campo contraseña y hacerlo requerido
        $('#password').closest('.mb-3').show(); 
        $('#msgPassword').text('Mínimo 8 caracteres');
        
        modalUsuario.show();
    });

    // Enviar Formulario (Crear / Editar)
    $form.on('submit', function (e) {
        e.preventDefault();

        const id     = $id.val();
        const isEdit = (id != 0);
        const opcion = isEdit ? 'actualizar' : 'agregar';

        // Validar antes de enviar
        if (!validarFormularioUsuario(isEdit)) {
            return;
        }

        const fd = new FormData(this);

        // Ajuste: si el campo password está vacío en edición, lo quitamos para que el backend sepa que no se cambia
        // Ojo: tu controlador usa trim($_POST['password']), así que vacío está bien, 
        // pero asegurémonos de enviarlo limpio.
        
        $.ajax({
            url: `${CTRL_USUARIO}?opcion=${opcion}`,
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (r) {
                if (r.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: r.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    modalUsuario.hide();
                    tabla.ajax.reload(null, false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: r.message || 'No se pudo completar la operación'
                    });
                }
            },
            error: function (xhr) {
                console.error(xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: 'Ocurrió un problema al procesar la solicitud.'
                });
            }
        });
    });

    // Botón Editar (en la tabla)
    $('#tablaUsuarios').on('click', '.btn-editar', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.getJSON(`${CTRL_USUARIO}?opcion=obtener&id=${id}`, function (r) {
            if (r.status !== 'success') {
                Swal.fire({ icon: 'error', title: 'Usuario no encontrado' });
                return;
            }

            const u = r.data;

            $titulo.text('Editar Usuario');
            $id.val(u.id);
            $('#nombre_real').val(u.nombre_real);
            $('#username').val(u.username);
            $('#rol').val(u.rol);
            $('#estado').val(u.estado);

            // Manejo de contraseña en edición
            $('#password').val(''); // Limpiar campo
            $('#msgPassword').text('Dejar vacío para mantener la contraseña actual.');
            limpiarErroresFormulario();

            modalUsuario.show();
        });
    });

    // Switch Cambiar Estado (en la tabla)
    $('#tablaUsuarios').on('change', '.switch-estado', function () {
        const idUsuario = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const nuevoEstado = isChecked ? 1 : 0;

        // Deshabilitar temporalmente para evitar doble click
        const switchEl = $(this);
        switchEl.prop('disabled', true);

        $.post(
            `${CTRL_USUARIO}?opcion=estado`,
            { id: idUsuario, estado: nuevoEstado },
            function (r) {
                switchEl.prop('disabled', false);
                
                if (r.status === 'success') {
                    // Actualizar tabla para refrescar badges y colores
                    tabla.ajax.reload(null, false);
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: 'success',
                        title: isChecked ? 'Usuario activado' : 'Usuario desactivado'
                    });
                } else {
                    // Revertir cambio visual si falla
                    switchEl.prop('checked', !isChecked);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: r.message || 'No se pudo cambiar el estado'
                    });
                }
            },
            'json'
        ).fail(function() {
            switchEl.prop('disabled', false);
            switchEl.prop('checked', !isChecked);
            Swal.fire('Error', 'Fallo de conexión con el servidor', 'error');
        });
    });

    // Botón Eliminar (en la tabla)
    $('#tablaUsuarios').on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar Usuario?',
            text: 'Esta acción borrará permanentemente al usuario del sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(
                    `${CTRL_USUARIO}?opcion=eliminar`,
                    { id: id },
                    function (r) {
                        if (r.status === 'success') {
                            Swal.fire(
                                '¡Eliminado!',
                                r.message,
                                'success'
                            );
                            tabla.ajax.reload(null, false);
                        } else {
                            Swal.fire(
                                'Error',
                                r.message || 'No se pudo eliminar el usuario',
                                'error'
                            );
                        }
                    },
                    'json'
                );
            }
        });
    });

});