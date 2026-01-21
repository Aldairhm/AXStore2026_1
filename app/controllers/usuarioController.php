<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/usuarioModel.php';

$usuarioModel = new Usuario();
$opcion = isset($_GET['opcion']) ? trim($_GET['opcion']):null;
$response = ['status'=>'error','message'=>'Opción no válida'];

try{
    switch ($opcion){

        case 'listar':
            $row = $usuarioModel->getUsuarios();
            $response = ['status'=>'success','data'=>$row];
            break;

        case 'agregar':
            $nombre_real = trim($_POST['nombre_real'] ?? '');
            $username    = trim($_POST['username'] ?? '');
            $password    = trim($_POST['password'] ?? '');
            $rol         = trim($_POST['rol'] ?? '');
            $estado      = intval($_POST['estado'] ?? 0);

            if(!$nombre_real || !$username || !$password){
                $response = ['status'=>'error','message'=>'Faltan datos obligatorios'];
                break;
            }
            if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
                $response = ['status'=>'error','message'=>'El correo electrónico no es válido'];
                break;
            }
            if($usuarioModel->existeCorreo($username)){
                $response = ['status'=>'error','message'=>'El correo electrónico ya está registrado'];
                break;
            }

            $ok = $usuarioModel->agregar($nombre_real, $username, $password, $rol, $estado);
            if($ok){
                $response = ['status'=>'success','message'=>'Usuario registrado correctamente'];
            } else {
                $response = ['status'=>'error','message'=>'No se pudo registrar el usuario'];
            }
            break;

        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            $row = $usuarioModel->getUsuarioById($id);
            $response = $row ? ['status'=>'success','data'=>$row] : ['status'=>'error','message'=>'Usuario no encontrado'];
            break;


        case 'actualizar':
            $id          = intval($_POST['id'] ?? 0);
            $nombre_real = trim($_POST['nombre_real'] ?? '');
            $username    = trim($_POST['username'] ?? '');
            $password    = trim($_POST['password'] ?? '');
            $rol         = trim($_POST['rol'] ?? '');
            $estado      = intval($_POST['estado'] ?? 0);

            if(!$id || !$nombre_real || !$username){
                $response = ['status'=>'error','message'=>'Faltan datos obligatorios'];
                break;
            }
            if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
                $response = ['status'=>'error','message'=>'El correo electrónico no es válido'];
                break;
            }
            $ok = $usuarioModel->actualizar($id, $nombre_real, $username, $password, $rol, $estado);
            if($ok !== null){
                $response = ['status'=>'success','message'=>'Usuario actualizado correctamente'];
            } else {
                $response = ['status'=>'error','message'=>'No se pudo actualizar el usuario'];
            }
            break;

        
        case 'actualizar_contraseña':
            $id = intval($_POST['id'] ?? 0);
            $password = trim($_POST['password'] ?? '');
            if(!$id || !$password){
                $response = ['status'=>'error','message'=>'Faltan datos obligatorios'];
                break;
            }
            if(strlen($password) < 8){
                $response = ['status'=>'error','message'=>'La contraseña debe tener al menos 8 caracteres'];
                break;  
            }
            $ok = $usuarioModel->actualizarContrasenia($id, $password);
            $response = $ok ? ['status'=>'success','message'=>'Contraseña actualizada correctamente'] : ['status'=>'error','message'=>'No se pudo actualizar la contraseña'];
            break;


        case 'eliminar':
            $id = intval($_POST['id'] ?? 0);
            if(!$id){
                $response = ['status'=>'error','message'=>'Faltan datos obligatorios'];
                break;
            }
            $ok = $usuarioModel->eliminar($id);
            $response = $ok ? ['status'=>'success','message'=>'Usuario eliminado correctamente'] : ['status'=>'error','message'=>'No se pudo eliminar el usuario'];
            break;


        case 'estado':
            $id = intval($_POST['id'] ?? 0);
            $estado = intval($_POST['estado'] ?? 0);
            if(!$id){
                $response = ['status'=>'error','message'=>'Faltan datos obligatorios'];
                break;
            }
            $ok = $usuarioModel->cambiarEstado($id, $estado);
            $response = $ok ? ['status'=>'success','message'=>'Estado actualizado correctamente'] : ['status'=>'error','message'=>'No se pudo actualizar el estado'];
            break;


        default:
        break;
    }
}catch (Throwable $e) {
    $msg = $e->getMessage();

    if (stripos($msg, 'El correo no tiene un formato válido') !== false) {
        $msg = 'El correo no tiene un formato válido';
    } elseif (stripos($msg, 'El correo ya está registrado') !== false) {
        $msg = 'El correo ya está registrado';
    } 

    $response = ['status' => 'error', 'message' => $msg];
}
echo json_encode($response);