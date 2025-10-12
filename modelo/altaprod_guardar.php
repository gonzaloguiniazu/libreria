<?php
include('conexion.php');
$conn = conectarDB();

$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$stock_minimo = $_POST['stock_minimo'];
$id_marca = $_POST['id_marca'];
$imagen = $_POST['imagen'];

if ($id_producto > 0) {
    // Actualizar producto existente
    $sql = "UPDATE producto 
            SET descripcion='$descripcion', 
                precio='$precio', 
                stock='$stock', 
                stock_minimo='$stock_minimo', 
                id_marca='$id_marca', 
                imagen='$imagen'
            WHERE id_producto=$id_producto";
} else {
    // Insertar nuevo producto
    $sql = "INSERT INTO producto (descripcion, precio, stock, stock_minimo, id_marca, imagen) 
            VALUES ('$descripcion', '$precio', '$stock', '$stock_minimo', '$id_marca', '$imagen')";
}

if ($conn->query($sql) === TRUE) {
    // Mostrar mensaje de éxito antes de redirigir
    echo "<script>
        alert('✅ Producto guardado correctamente.');
        window.location.href = 'crud.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('❌ Error al guardar el producto: " . addslashes($conn->error) . "');
        window.location.href = 'crud.php';
    </script>";
}

$conn->close();
?>
