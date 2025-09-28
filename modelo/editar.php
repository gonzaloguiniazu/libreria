<?php

require_once("conexion.php"); // Conexión a la base de datos
$conexion = conectarDB(); // Conectar a la base de datos

// Verificar si se recibió el id del producto
if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    // Obtener los datos del producto desde la base de datos
    $sql = "SELECT * FROM producto WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_object(); // Solo se define si hay resultados
    } else {
        // Mensaje de error si no se encuentra el producto
        echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
        exit; // Detenemos la ejecución si no hay producto
    }
} else {
    // Mensaje de error si no se recibe el id
    echo "<div class='alert alert-danger'>ID de producto no recibido.</div>";
    exit; // Detenemos la ejecución si no hay ID
}

$conexion->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
        <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">


    <title>ABM Productos</title>
</head>

<body>

    <div class="container-fluid" style="margin-top: 50px;">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Editar Producto
                    </div>
                    <div class="card-body">

                        <?php if (isset($producto)) { // Verificamos si $producto está definido 
                        ?>

                            <form action="actualizar.php" method="post">

                                <input type="hidden" name="id_producto" value="<?= $producto->id_producto ?>">

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Descripcion</label>
                                    <input type="text" class="form-control" name="descripcion" value="<?= $producto->descripcion ?>">
                                </div>

                                <br>

                                <label for="id_marca">Marca</label>
                                <select class="form-select" name="id_marca">
                                    <option selected disabled>Seleccione una marca:</option>
                                    <?php
                                    $conexion = conectarDB();
                                    $tabla_marca = "SELECT id_marca, nombre_marca FROM marca";
                                    $consulta_marca = mysqli_query($conexion, $tabla_marca) or die("ERROR:" . mysqli_error($conexion));
                                    while ($consulta_array_marca = mysqli_fetch_array($consulta_marca)) {
                                        // Verificar si la marca actual es la misma que la del producto
                                        $selected = ($consulta_array_marca['id_marca'] == $producto->id_marca) ? "selected" : "";
                                        echo '<option value="' . htmlspecialchars($consulta_array_marca['id_marca']) . '" ' . $selected . '>' . htmlspecialchars($consulta_array_marca['nombre_marca']) . '</option>';
                                    }
                                    $conexion->close();
                                    ?>
                                </select>

                                <br>
                                <br>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Stock</label>
                                    <input type="number" class="form-control" name="stock" value="<?= $producto->stock ?>">
                                </div>

                                <br>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Stock minimo</label>
                                    <input type="number" class="form-control" name="stock_minimo" value="<?= $producto->stock_minimo ?>">
                                </div>

                                <br>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Precio</label>
                                    <input type="number" step="0.01" class="form-control" name="precio" value="<?= $producto->precio ?>">
                                </div>

                                <hr>
                                <div class="text-right">
                                    <!-- boton para registrar  -->
                                    <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
                                    <!-- boton para cancelar y volver al indice de productos -->
                                    <a href="crud.php"><button type="button" class="btn btn-danger">Volver</button></a>
                                </div>

                            </form>
                        <?php } ?>
                    </div>


                </div>
            </div>
        </div>
    </div>








    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>