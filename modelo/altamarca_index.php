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
    <script>
        function eliminar(){
            var respuesta= confirm("estas seguro que deseas eliminar?");
            return respuesta;
        }
    </script>

    <div class="container-fluid column">
        <div class="card col-4 p-1">
            <div class="card-header m-auto">
                Gestionar Alta de una nueva marca
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Marca</label>
                        <input type="text" class="form-control" name="marca" required>
                    </div>
                    <br>
                    <hr>
                    <div class="text-right">
                        <!-- boton para registrar  -->
                        <button type="submit" name="registrar" class="btn btn-primary" value="ok">Registrar</button>
                        <!-- boton para cancelar y volver al indice de productos -->
                        <a href="crud.php"><button type="button" class="btn btn-danger">Volver</button></a>
                    </div>
                </form>
            </div>

        </div>
        <div class="col-8 p-2">
            <table class="table table-responsive">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include("conexion.php");

                    $conn = conectarDB();
                    include("altamarca.php");
                    include("eliminarmarca.php");
                    $sql = $conn->query("select * from marca");
                    while ($datos = $sql->fetch_object()) { ?>
                        <tr>
                            <td><?= $datos->id_marca ?></td>
                            <td><?= $datos->nombre_marca ?></td>
                            <td>
                                <a href="modificarmarca_index.php?id=<?= $datos->id_marca ?>" class="btn btn-warning">Editar</a>
                                <a onclick="return eliminar()" href="altamarca_index.php?id=<?= $datos->id_marca ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>