<?php
require_once('verificar_admin.php');
require_once("conexion.php");

$conexion = conectarDB();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    $sql = "SELECT * FROM producto WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_object();
    } else {
        echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
        exit;
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>ID de producto no recibido.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>Editar Producto</title>
    <style>
        body {
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            background-position: center;
        }
        .admin-info {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid" style="margin-top: 50px;">
        <div class="admin-info">
            <strong>Administrador:</strong> <?php 
                $usuario = obtenerUsuarioActual();
                echo htmlspecialchars($usuario['nombre_completo']); 
            ?>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">Editar Producto</h4>
                    </div>
                    <div class="card-body">
                        <form action="actualizar.php" method="post">
                            <input type="hidden" name="id_producto" value="<?= $producto->id_producto ?>">

                            <div class="form-group mb-3">
                                <label for="descripcion">Descripción</label>
                                <input type="text" class="form-control" name="descripcion" 
                                       value="<?= htmlspecialchars($producto->descripcion) ?>" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="id_marca">Marca</label>
                                <select class="form-select" name="id_marca" required>
                                    <option selected disabled>Seleccione una marca:</option>
                                    <?php
                                    $tabla_marca = "SELECT id_marca, nombre_marca FROM marca ORDER BY nombre_marca";
                                    $consulta_marca = mysqli_query($conexion, $tabla_marca);
                                    
                                    if (!$consulta_marca) {
                                        die("Error en la consulta: " . mysqli_error($conexion));
                                    }
                                    
                                    while ($consulta_array_marca = mysqli_fetch_array($consulta_marca)) {
                                        $selected = ($consulta_array_marca['id_marca'] == $producto->id_marca) ? "selected" : "";
                                        echo '<option value="' . htmlspecialchars($consulta_array_marca['id_marca']) . '" ' . $selected . '>' 
                                             . htmlspecialchars($consulta_array_marca['nombre_marca']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="stock">Stock</label>
                                <input type="number" class="form-control" name="stock" 
                                       value="<?= htmlspecialchars($producto->stock) ?>" min="0" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="stock_minimo">Stock mínimo</label>
                                <input type="number" class="form-control" name="stock_minimo" 
                                       value="<?= htmlspecialchars($producto->stock_minimo) ?>" min="0" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="precio">Precio</label>
                                <input type="number" step="0.01" class="form-control" name="precio" 
                                       value="<?= htmlspecialchars($producto->precio) ?>" min="0.01" required>
                            </div>

                            <hr>
                            <div class="text-center">
                                <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
                                <a href="crud.php"><button type="button" class="btn btn-danger">Volver</button></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conexion->close(); ?>