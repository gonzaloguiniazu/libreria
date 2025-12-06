<?php
// Este archivo se incluye en crud.php, la sesión ya está validada

if (!empty($_GET["id"])) {
    $id = intval($_GET["id"]);
    
    if ($id <= 0) {
        echo "<div class='alert alert-danger'>Error: ID inválido</div>";
    } else {
        $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM detalle_ventas WHERE id_producto = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();
        
        if ($row_check['count'] > 0) {
            echo "<div class='alert alert-warning'>No se puede eliminar el producto porque tiene ventas asociadas.</div>";
        } else {
            $stmt = $conn->prepare("DELETE FROM producto WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "<div class='alert alert-success'>Registro eliminado con éxito</div>";
                } else {
                    echo "<div class='alert alert-warning'>No se encontró el producto a eliminar</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error al eliminar: " . htmlspecialchars($stmt->error) . "</div>";
            }
            
            $stmt->close();
        }
    }
?>
    <script>
        history.replaceState(null, null, location.pathname);
    </script>
<?php
}
?>