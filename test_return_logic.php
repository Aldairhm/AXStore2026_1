<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/app/models/salidaModel.php';

$salidaModel = new Salida();
$pdo = Conexion::conectar();

// 1. Create a dummy sale to test with
echo "Creating dummy sale...\n";
$datos = [
    'id_variante' => 1, // Assumes variant 1 exists
    'id_usuario' => 2,
    'cantidad' => 5,
    'fecha_salida' => date('Y-m-d'),
    'hora_salida' => date('H:i:s'),
    'precio_unitario' => 10.0,
    'subtotal' => 50.0,
    'descuento' => 0.0,
    'total' => 50.0
];

$id = $salidaModel->registrarSalida($datos);
if (!$id) {
    die("Failed to create dummy sale.\n");
}
echo "Created sale ID: $id\n";

// 2. Process a TOTAL return
echo "Processing total return for ID: $id...\n";
$resultado = $salidaModel->procesarDevolucion($id, "Prueba de registro detallado", 5);

echo "Result: " . ($resultado['exito'] ? "SUCCESS" : "FAILURE") . "\n";
echo "Message: " . $resultado['mensaje'] . "\n";

// 3. Inspect the row
echo "\nInspecting row in DB...\n";
$res = $pdo->prepare("SELECT id, estado, observations as observaciones, fecha_cancelacion FROM salida WHERE id = ?");
$res->execute([$id]);
$row = $res->fetch(PDO::FETCH_ASSOC);

print_r($row);

if (empty($row['fecha_cancelacion'])) {
    echo "ERROR: fecha_cancelacion IS STILL EMPTY!\n";
}
else {
    echo "SUCCESS: fecha_cancelacion registered as: " . $row['fecha_cancelacion'] . "\n";
}

// Cleanup: remove the test row
//$pdo->prepare("DELETE FROM salida WHERE id = ?")->execute([$id]);
unlink(__FILE__);
echo "\nTest finished.\n";
