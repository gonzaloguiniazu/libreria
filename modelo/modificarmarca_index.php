<?php
include("conexion.php");

$conn = conectarDB();

$id = $_GET["id"];


$sql = $conn->query("select * from marca where id_marca=$id");


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
    <title>Modificar marca</title>
</head>

<body>
    <div class="container-fluid column">
        <div class="card col-4 p-1">
            <div class="card-header card-secondary m-auto">
                Modificar nombre de marca
            </div>

            <div class="card-body">
                <form action="" method="POST">

                    <input type="hidden" name="id" value="<?= $_GET["id"] ?>">
                    <?php



                    include("modificarmarca.php");
                    $sql = $conn->query("select * from marca where id_marca=$id");

                    while ($datos = $sql->fetch_object()) { ?>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Marca</label>
                            <input type="text" class="form-control" name="marca" value="<?= $datos->nombre_marca ?>" required>
                        </div>

                    <?php }
                    ?>

                    <br>
                    <hr>
                    <div class="text-right">
                        <!-- boton para registrar  -->
                        <button type="submit" name="registrar" class="btn btn-primary" value="ok">Modificar</button>
                        <!-- boton para cancelar y volver al indice de productos -->
                        <a href="altamarca_index.php"><button type="button" class="btn btn-danger">Volver</button></a>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>