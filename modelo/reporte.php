<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>Estación del Arte</title>

    <style>
        body {
            background-image: url('../vista/imagenes/imagen4.jpg');
            background-size: cover; /* Para que la imagen cubra toda la pantalla */
            background-position: center; /* Para centrar la imagen */
        }
    </style>
    

    <!-- link de bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    
    </head>
   
                        <!-- boton para cancelar y volver al indice de productos -->
                        <a href="crud.php"><button type="button" class="btn btn-danger">Volver</button></a>
<body bgcolor="gray">
    <div class="container">
        <h2>Reporte de Ventas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>descripcion</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                  
                    
                </tr>
            </thead>
            <tbody>
                <?php
                include("conexion.php");

                $conexion = conectarDB();
                if (!$conexion) {
                    die("Error de conexión: " . mysqli_connect_error());
                }

                // Consulta SQL 
               $sql = "SELECT * FROM carrito";
               
               $result = mysqli_query($conexion, $sql);
               // Verificar si se produjo un error en la consulta
               
               if (!$result) {
                
                die("Error en la consulta: " . mysqli_error($conexion));
            }
            
            // Si no hubo errores, procedemos a mostrar los resultados
            if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['descripcion'] . "</td>";
                        echo "<td>" . $row['precio'] . "</td>";
                        echo "<td>" . $row['cantidad'] . "</td>";
                        echo "<td>$" . number_format($row['total'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No se encontraron resultados.</td></tr>";
                }

                mysqli_close($conexion);
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
<?php
ob_end_flush();
?>