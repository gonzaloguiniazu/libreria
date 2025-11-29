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

$id_cliente = $_SESSION['id_cliente'];

// Determinar la acción a realizar
$action = '';

// Intentar obtener action desde POST (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data && isset($data['action'])) {
    $action = $data['action'];
} else if (isset($_POST['action'])) {
    // Si viene por POST normal
    $action = $_POST['action'];
} else if (isset($_GET['action'])) {
    // Si viene por GET
    $action = $_GET['action'];
}

// Log para depuración (puedes quitarlo después)
error_log("Acción recibida: " . $action);
error_log("Datos recibidos: " . print_r($data, true));

switch ($action) {
    case 'agregar':
        agregarProducto($conn, $id_cliente, $data);
        break;
    
    case 'actualizar':
        actualizarCantidad($conn, $id_cliente, $data);
        break;
    
    case 'eliminar':
        eliminarProducto($conn, $id_cliente, $data);
        break;
    
    case 'obtener':
        obtenerCarrito($conn, $id_cliente);
        break;
    
    case 'finalizar':
        finalizarCompra($conn, $id_cliente);
        break;
    
    case 'vaciar':
        vaciarCarrito($conn, $id_cliente);
        break;
    
    default:
        echo json_encode([
            'success' => false, 
            'error' => 'Acción no válida',
            'action_recibida' => $action,
            'post' => $_POST,
            'get' => $_GET,
            'input' => $input
        ]);
        break;
}

$conn->close();

// ========== FUNCIONES ==========

function agregarProducto($conn, $id_cliente, $data) {
    if (!isset($data['id_producto']) || !isset($data['cantidad'])) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }
    
    $id_producto = (int)$data['id_producto'];
    $cantidad = (int)$data['cantidad'];
    
    // Obtener precio del producto
    $stmt = $conn->prepare("SELECT precio, stock FROM producto WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        return;
    }
    
    $producto = $result->fetch_assoc();
    $precio = $producto['precio'];
    $stock = $producto['stock'];
    
    // Verificar stock disponible
    if ($cantidad > $stock) {
        echo json_encode(['success' => false, 'error' => 'Stock insuficiente']);
        return;
    }
    
    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT id_carrito, cantidad FROM carrito WHERE id_cliente = ? AND id_producto = ?");
    $stmt->bind_param("ii", $id_cliente, $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Actualizar cantidad
        $row = $result->fetch_assoc();
        $nueva_cantidad = $row['cantidad'] + $cantidad;
        
        if ($nueva_cantidad > $stock) {
            echo json_encode(['success' => false, 'error' => 'Stock insuficiente']);
            return;
        }
        
        $stmt = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id_carrito = ?");
        $stmt->bind_param("ii", $nueva_cantidad, $row['id_carrito']);
    } else {
        // Insertar nuevo producto
        $stmt = $conn->prepare("INSERT INTO carrito (id_cliente, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $id_cliente, $id_producto, $cantidad, $precio);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al agregar producto: ' . $stmt->error]);
    }
}

function actualizarCantidad($conn, $id_cliente, $data) {
    if (!isset($data['id_producto']) || !isset($data['cantidad'])) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }
    
    $id_producto = (int)$data['id_producto'];
    $cantidad = (int)$data['cantidad'];
    
    if ($cantidad < 1) {
        echo json_encode(['success' => false, 'error' => 'Cantidad debe ser al menos 1']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id_cliente = ? AND id_producto = ?");
    $stmt->bind_param("iii", $cantidad, $id_cliente, $id_producto);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar cantidad']);
    }
}

function eliminarProducto($conn, $id_cliente, $data) {
    if (!isset($data['id_producto'])) {
        echo json_encode(['success' => false, 'error' => 'ID de producto no proporcionado']);
        return;
    }
    
    $id_producto = (int)$data['id_producto'];
    
    $stmt = $conn->prepare("DELETE FROM carrito WHERE id_cliente = ? AND id_producto = ?");
    $stmt->bind_param("ii", $id_cliente, $id_producto);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar producto']);
    }
}

function obtenerCarrito($conn, $id_cliente) {
    $stmt = $conn->prepare("
        SELECT c.id_carrito, c.id_producto, c.cantidad, c.precio_unitario,
               p.descripcion, p.imagen, p.stock,
               (c.cantidad * c.precio_unitario) as subtotal
        FROM carrito c
        INNER JOIN producto p ON c.id_producto = p.id_producto
        WHERE c.id_cliente = ?
    ");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total += $row['subtotal'];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ]);
}

function finalizarCompra($conn, $id_cliente) {
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Obtener items del carrito
        $stmt = $conn->prepare("
            SELECT c.id_producto, c.cantidad, c.precio_unitario,
                   (c.cantidad * c.precio_unitario) as subtotal
            FROM carrito c
            WHERE c.id_cliente = ?
        ");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('El carrito está vacío');
        }
        
        $items = [];
        $total = 0;
        
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
            $total += $row['subtotal'];
        }
        
        // Crear venta
        $stmt = $conn->prepare("INSERT INTO ventas (id_cliente, total, estado) VALUES (?, ?, 'completada')");
        $stmt->bind_param("id", $id_cliente, $total);
        $stmt->execute();
        $id_venta = $conn->insert_id;
        
        // Insertar detalles de venta y actualizar stock
        $stmt_detalle = $conn->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, precio, cantidad) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE producto SET stock = stock - ? WHERE id_producto = ?");
        
        foreach ($items as $item) {
            // Insertar detalle
            $stmt_detalle->bind_param("iidi", $id_venta, $item['id_producto'], $item['precio_unitario'], $item['cantidad']);
            $stmt_detalle->execute();
            
            // Actualizar stock
            $stmt_stock->bind_param("ii", $item['cantidad'], $item['id_producto']);
            $stmt_stock->execute();
        }
        
        // Vaciar carrito
        $stmt = $conn->prepare("DELETE FROM carrito WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Compra finalizada con éxito',
            'id_venta' => $id_venta,
            'total' => $total
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function vaciarCarrito($conn, $id_cliente) {
    $stmt = $conn->prepare("DELETE FROM carrito WHERE id_cliente = ?");
    $stmt->bind_param("i", $id_cliente);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Carrito vaciado']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al vaciar carrito']);
    }
}
?>