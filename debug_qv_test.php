<?php
// Include necessary files
require_once 'config/app.php';
require_once 'config/server.php';
require_once 'config/conexion.php';
require_once 'app/models/productoModel.php';

// Instantiate 
$productoModel = new Producto(); // Class name is Producto

// 1. Get all products to find a valid ID
$todos = $productoModel->obtenerTodosLosProductosConVariantes();

if (empty($todos)) {
    die("No products found in DB.");
}

$firstProduct = $todos[0];
$validId = $firstProduct['id'];

echo "Testing with Valid ID: " . $validId . "\n";

// Mock POST data
$_POST['accion'] = 'obtenerDetalleQuickView';
$_POST['id'] = $validId;


// Logic from Controller
$id = (int)$_POST["id"];
$variante = $productoModel->getVariantePorId($id);

// echo "Variante Raw:\n";
// print_r($variante);

if ($variante) {
    // echo "\nID Producto Padre: " . $variante['id_producto'] . "\n";

    // Agregar el nombre del producto padre, categoría y descripción
    $productoPadre = $productoModel->obtenerProductoPorId((int)$variante['id_producto']);

    echo "\nProducto Padre Raw:\n";
    print_r($productoPadre);

    if ($productoPadre) {
        $variante['nombre_categoria'] = $productoPadre['categoria'];
        $variante['descripcion'] = $productoPadre['descripcion'];
        // [NUEVO] Nombre padre
        $variante['nombre_producto_padre'] = $productoPadre['nombre'];
    }

    echo "\nVariante Final (Check nombre_producto_padre):\n";
    print_r([
        'id' => $variante['id'],
        'nombre_variante' => $variante['nombre_variante'],
        'nombre_producto_padre' => $variante['nombre_producto_padre'] ?? 'NULL'
    ]);
}
else {
    echo "No matching variant found for ID $id.";
}
