<?php
// semilla.php

require_once "config/app.php";
require_once "config/conexion.php";
require_once "app/models/encriptarModel.php";
require_once "app/models/usuarioModel.php";

$usuarioModel = new Usuario();

// Lista de usuarios a crear (puedes editar esto)
$usuarios = [
    [
        "nombre" => "Administrador Principal",
        "email"  => "admin@gmail.com",
        "pass"   => "12345678",
        "rol"    => "administrador"
    ],
    [
        "nombre" => "Vendedor Tienda",
        "email"  => "vendedor@gmail.com",
        "pass"   => "12345678",
        "rol"    => "vendedor"
    ]
];

echo "<h3>Generando usuarios...</h3>";

foreach ($usuarios as $u) {
    // Verificamos si ya existe para no duplicar
    if (!$usuarioModel->existeCorreo($u['email'])) {
        $resultado = $usuarioModel->agregar(
            $u['nombre'],
            $u['email'],
            $u['pass'],
            $u['rol'],
            1 // Estado 1 = Activo
        );

        if ($resultado) {
            echo "<p style='color:green'>✔ Usuario <b>{$u['email']}</b> creado con éxito. (Pass: {$u['pass']})</p>";
        } else {
            echo "<p style='color:red'>✘ Error al crear {$u['email']}</p>";
        }
    } else {
        echo "<p style='color:orange'>⚠ El usuario <b>{$u['email']}</b> ya existe.</p>";
    }
}
?>