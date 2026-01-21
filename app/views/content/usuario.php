<div id="modalUsuario"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloUsuario"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloUsuario">Usuario</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="post" id="formUsuario" enctype="multipart/form-data" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <input type="hidden" id="id_odontologo" name="id_odontologo">

          <div class="row g-3">
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="nombreUsuario" name="nombreUsuario"
                       type="text" placeholder=" " maxlength="100" required>
                <label for="nombreUsuario">Nombre</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="username1" name="username1"
                       type="email" placeholder=" " required>
                <label for="username1">Correo electrónico</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="password1" name="password1"
                       type="password" placeholder=" " required>
                <label for="password1">Contraseña</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="rol" name="rol"
                        class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="administrador">Administrador</option>
                  <option value="vendedor">Vendedor</option>
                </select>
                <label for="rol">Rol</label>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <select id="estado" name="estado"
                        class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
                <label for="estado">Estado</label>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnGuardarUsuario" class="btn btn-success">Guardar</button>
        </div>

      </form>
    </div>
  </div>
</div>



<div id="modalUsuarioVer"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloUsuarioVer"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloUsuarioVer"></h4>
                <h5 class="modal-title">Listado de usuarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            

            <form method="post" id="formUsuarioVer" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body py-4 px-4">

                    <div style="padding:10px;">
                        <table id="tablaUsuarios" class="table table-bordered border-primary table-striped nowrap table-bordered" width="100%" cellspacing="0">
                            <thead class="table-primary">
                                <tr class="p-3 mb-2 bg-secondary text-white text-center">
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Correo</th>
                                    <th class="text-center">Rol</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center notexport">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>