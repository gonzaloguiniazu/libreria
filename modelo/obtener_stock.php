<?php
session_start();
header('Content-Type: application/json');

include("conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_cliente'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

$conn = conectarDB();
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit();
}

// Obtener todos los productos con su stock actual
$sql = "SELECT id_producto, stock, stock_minimo FROM producto ORDER BY id_producto ASC";
$resultado = $conn->query($sql);

if (!$resultado) {
    echo json_encode(['success' => false, 'error' => 'Error al obtener stock']);
    exit();
}

$productos = [];
while ($row = $resultado->fetch_assoc()) {
    $productos[] = [
        'id_producto' => (int)$row['id_producto'],
        'stock' => (int)$row['stock'],
        'stock_minimo' => (int)$row['stock_minimo']
    ];
}

echo json_encode([
    'success' => true,
    'productos' => $productos
]);

$conn->close();
?>