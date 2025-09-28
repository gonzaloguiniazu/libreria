<?php
// Asegúrate de establecer el tipo de contenido como JSON
header('Content-Type: application/json');

include("conexion.php");

// Conectar a la base de datos
$conn = conectarDB();
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit();
}

// Obtener los datos enviados desde JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los datos estén bien recibidos
if (isset($data['productos']) && is_array($data['productos'])) {
    $productos = $data['productos'];
    $success = true;  // Variable de éxito

    // Usar declaraciones preparadas para evitar inyección SQL
    $query = "INSERT INTO carrito (descripcion, cantidad, precio, total) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta: ' . $conn->error]);
        exit();
    }

    // Recorrer los productos y hacer la inserción
    foreach ($productos as $producto) {
        $descripcion = $producto['descripcion'];
        $cantidad = (int)$producto['cantidad'];
        $precio = (float)str_replace('$', '', $producto['precio']);
        //linea agregada
        $precio= $precio*1000;
        $total = (float)$producto['total'];
        //linea agregada
        $total=$total*1000;

        // Vincular los parámetros para evitar inyecciones
        $stmt->bind_param("sids", $descripcion, $cantidad, $precio, $total); // s: string, i: integer, d: double

        // Ejecutar la consulta
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => 'Error al insertar en la base de datos: ' . $stmt->error]);
            exit();
        }
    }

    $stmt->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incorrectos o no recibidos.']);
}

$conn->close();

?>
