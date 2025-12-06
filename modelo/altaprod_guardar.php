<?php
require_once('verificar_admin.php');

include('conexion.php');
$conn = conectarDB();

$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
$descripcion = trim($_POST['descripcion']);
$precio = floatval($_POST['precio']);
$stock = intval($_POST['stock']);
$stock_minimo = intval($_POST['stock_minimo']);
$id_marca = intval($_POST['id_marca']);
$imagen = trim($_POST['imagen']);

// Validar datos
if (empty($descripcion) || empty($id_marca) || $precio < 0 || $stock < 0 || $stock_minimo < 0) {
    echo "<script>
        alert('❌ Datos inválidos. Por favor verifica los campos.');
        window.location.href = 'altaprod.php" . ($id_producto > 0 ? "?id=$id_producto" : "") . "';
    </script>";
    exit();
}

if ($id_producto > 0) {
    // Actualizar producto existente con prepared statement
    $stmt = $conn->prepare("UPDATE producto 
            SET descripcion = ?, 
                precio = ?, 
                stock = ?, 
                stock_minimo = ?, 
                id_marca = ?, 
                imagen = ?
            WHERE id_producto = ?");
    
    $stmt->bind_param("sdiissi", $descripcion, $precio, $stock, $stock_minimo, $id_marca, $imagen, $id_producto);
    
    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Producto actualizado correctamente.');
            window.location.href = 'crud.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error al actualizar el producto: " . addslashes($stmt->error) . "');
            window.location.href = 'crud.php';
        </script>";
    }
    
    $stmt->close();
} else {
    // Insertar nuevo producto con prepared statement
    $stmt = $conn->prepare("INSERT INTO producto (descripcion, precio, stock, stock_minimo, id_marca, imagen) 
            VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sdiiss", $descripcion, $precio, $stock, $stock_minimo, $id_marca, $imagen);
    
    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Producto registrado correctamente.');
            window.location.href = 'crud.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error al guardar el producto: " . addslashes($stmt->error) . "');
            window.location.href = 'crud.php';
        </script>";
    }
    
    $stmt->close();
}

$conn->close();
?>