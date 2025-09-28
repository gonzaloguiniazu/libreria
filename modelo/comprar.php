<?php
include('conexion.php');
$conn = conectarDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que se recibieron los parámetros necesarios
    if (isset($_POST['id_producto']) && isset($_POST['cantidad']) && is_numeric($_POST['cantidad'])) {
        $id_producto = intval($_POST['id_producto']);
        $cantidad = intval($_POST['cantidad']);

        // Preparar la consulta para obtener el stock actual del producto
        $stmt = $conn->prepare("SELECT stock FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_object();

        if ($producto) {
            $stock_disponible = $producto->stock;

            // Verificar si la cantidad solicitada supera el stock disponible
            if ($cantidad > $stock_disponible) {
                echo 'La cantidad ingresada supera el stock disponible.';
            } else {
                // Actualizar el stock en la base de datos
                $nuevo_stock = $stock_disponible - $cantidad;
                $update_stmt = $conn->prepare("UPDATE producto SET stock = ? WHERE id_producto = ?");
                $update_stmt->bind_param("ii", $nuevo_stock, $id_producto);

                if ($update_stmt->execute()) {
                    echo 'Compra realizada con éxito.';
                } else {
                    echo 'Error al realizar la compra. Inténtalo de nuevo.';
                }
            }
        } else {
            echo 'Producto no encontrado.';
        }
    } else {
        echo 'Datos inválidos. Asegúrate de ingresar una cantidad válida.';
    }
} else {
    echo 'Método no permitido.';
}

$conn->close();
?>
