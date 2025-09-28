
<?php
include("conexion.php");
if (isset($_POST['actualizar'])) {
    $conexion = conectarDB();

     // Verificar si el id_producto fue enviado
     if (isset($_POST['id_producto']) && !empty($_POST['id_producto'])) {
        $id_producto = $_POST['id_producto'];
    } else {
        echo "<div class='alert alert-danger'>ID de producto no recibido.</div>";
        exit;
    }

    $id_producto = $_POST['id_producto'];
    $descripcion = $_POST['descripcion'];
    $id_marca = $_POST['id_marca'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $precio = $_POST['precio'];

    // Actualizar el producto en la base de datos
    $sql = "UPDATE producto SET descripcion = ?, id_marca = ?, stock = ?, stock_minimo = ?, precio = ? WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siiidi", $descripcion, $id_marca, $stock, $stock_minimo, $precio, $id_producto);

    if ($stmt->execute()) {
        echo "<script>
                alert('Producto actualizado exitosamente.');
                window.location.href = 'crud.php';
              </script>";
      
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar el producto: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conexion->close();

}
?>
 <!-- script para que no te salga el cartel de reenviar formulario -->
 <script>
        history.replaceState(null, null, location.pathname);
 </script>