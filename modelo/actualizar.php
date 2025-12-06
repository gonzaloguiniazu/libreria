<?php
require_once('verificar_admin.php');

include("conexion.php");

if (isset($_POST['actualizar'])) {
    $conexion = conectarDB();

    if (isset($_POST['id_producto']) && !empty($_POST['id_producto'])) {
        $id_producto = intval($_POST['id_producto']);
    } else {
        echo "<div class='alert alert-danger'>ID de producto no recibido.</div>";
        exit;
    }

    $descripcion = trim($_POST['descripcion']);
    $id_marca = intval($_POST['id_marca']);
    $stock = intval($_POST['stock']);
    $stock_minimo = intval($_POST['stock_minimo']);
    $precio = floatval($_POST['precio']);

    if (empty($descripcion)) {
        echo "<div class='alert alert-danger'>La descripción no puede estar vacía.</div>";
        exit;
    }

    if ($id_marca <= 0) {
        echo "<div class='alert alert-danger'>Debe seleccionar una marca válida.</div>";
        exit;
    }

    if ($stock < 0 || $stock_minimo < 0) {
        echo "<div class='alert alert-danger'>El stock no puede ser negativo.</div>";
        exit;
    }

    if ($precio <= 0) {
        echo "<div class='alert alert-danger'>El precio debe ser mayor a cero.</div>";
        exit;
    }

    $sql = "UPDATE producto SET descripcion = ?, id_marca = ?, stock = ?, stock_minimo = ?, precio = ? WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siiidi", $descripcion, $id_marca, $stock, $stock_minimo, $precio, $id_producto);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>
                    alert('Producto actualizado exitosamente.');
                    window.location.href = 'crud.php';
                  </script>";
        } else {
            echo "<script>
                    alert('No se realizaron cambios en el producto.');
                    window.location.href = 'crud.php';
                  </script>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar el producto: " . htmlspecialchars($stmt->error) . "</div>";
    }

    $stmt->close();
    $conexion->close();
}
?>
<script>
    history.replaceState(null, null, location.pathname);
</script>