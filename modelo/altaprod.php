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
    <style>
        body {
            background-image: url('../vista/imagenes/imagen4.jpg');
            background-size: cover; /* Para que la imagen cubra toda la pantalla */
            background-position: center; /* Para centrar la imagen */
        }
    </style>
    
</head>

<body>

    <div class="container-fluid" style="margin-top: 50px;">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Gestionar Alta de un Nuevo Producto
                    </div>
                    <div class="card-body">

                        <form action="" method="post">

                            <div class="form-group">
                                <label for="exampleInputEmail1">Descripcion</label>
                                <input type="text" class="form-control" name="descripcion" required>
                            </div>

                            <br>

                            <label for="exampleInputEmail1">Marca</label>
                            <select class="custom-select" name="id_marca">
                                <option selected disabled>Seleccione una marca:</option>
                                <?php
                                include("conexion.php");
                                $conexion = conectarDB();
                                $tabla_marca = "SELECT id_marca, nombre_marca FROM marca ";
                                $consulta_marca = mysqli_query($conexion, $tabla_marca) or die("ERROR:" . mysqli_error($conexion));
                                while ($consulta_array_marca = mysqli_fetch_array($consulta_marca)) {
                                    echo '<option value="' . $consulta_array_marca['id_marca'] . '">' . $consulta_array_marca['nombre_marca'] . '</option>';
                                }
                                $conexion->close();
                                ?>
                            </select>

                            <br>
                            <br>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Stock</label>
                                <input type="number" class="form-control" name="stock" required>
                            </div>
                            
                            <br>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Stock minimo</label>
                                <input type="number" class="form-control" name="stock_minimo" required>
                            </div>
                            
                            <br>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Precio</label>
                                <input type="number" step="0.01" class="form-control" name="precio" required>
                            </div>

                            <hr>
                            <div class="text-right">
                                <!-- boton para registrar  -->
                                <button type="submit" name="registrar" class="btn btn-primary">Registrar</button>
                                <!-- boton para cancelar y volver al indice de productos -->
                                <a href="crud.php"><button type="button" class="btn btn-danger">Volver</button></a>
                            </div>

                            <?php
                            if (isset($_POST['registrar'])) {
                                // Conexión a la base de datos
                                $conexion = conectarDB();
                            
                                // Capturar los datos del formulario
                                $descripcion = $_POST['descripcion'];
                                $id_marca = $_POST['id_marca'];
                                $stock = $_POST['stock'];
                                $stock_minimo = $_POST['stock_minimo'];
                                $precio = $_POST['precio'];
                            
                                // Validar que los campos no estén vacíos
                                if (!empty($descripcion) && !empty($id_marca) && !empty($stock) && !empty($stock_minimo) && !empty($precio)) {
                                    
                                    // Insertar los datos en la tabla
                                    $sql = "INSERT INTO producto (descripcion, id_marca, stock, stock_minimo, precio) VALUES (?, ?, ?, ?, ?)";
                                    
                                    // Preparar la consulta
                                    if ($stmt = $conexion->prepare($sql)) {
                                        // Vincular los parámetros
                                        $stmt->bind_param("sidii", $descripcion, $id_marca, $stock, $stock_minimo, $precio); // "s" string, "i" integer, "d" decimal
                                        
                                        // Ejecutar la consulta
                                        if ($stmt->execute()) {
                                            echo "<div class='alert alert-success'>Producto registrado exitosamente.</div>";
                                        } else {
                                            echo "<div class='alert alert-danger'>Error al registrar el producto: " . $conexion->error . "</div>";
                                        }
                                        $stmt->close();
                                    }
                                } else {
                                    echo "<div class='alert alert-warning'>Por favor, completa todos los campos.</div>";
                                }
                            
                                $conexion->close();
                            }
                            ?>
                        </form>
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