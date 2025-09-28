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


    <title>Catalogo de Productos</title>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="../vista/index.html">Cerrar sesion</a>
            </div>
        </nav>
    </div>

</head>

<body>
    <style>
        body {
            background-image: url('../vista/imagenes/imagen4.jpg');
            background-size: cover;
            /* Para que la imagen cubra toda la pantalla */
            background-position: center;
            /* Para centrar la imagen */
        }
    </style>


    <h1 class="text-center text-dark font-weight-bold p-4"> Catálogo de productos de libreria</h1>
    <?php
include('conexion.php');

// Llama a la función de conexión
$conn = conectarDB(); // Obtiene la conexión a la base de datos

// Inicializar la variable de búsqueda
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

// Preparar la consulta SQL
$sql = "SELECT p.id_producto, p.descripcion, p.stock, p.stock_minimo, m.nombre_marca AS nombre_marca 
        FROM producto p
        INNER JOIN marca m ON p.id_marca = m.id_marca";

// Agregar condición de búsqueda si hay un término ingresado
if ($buscar) {
    $sql .= " WHERE p.descripcion LIKE '%" . $conn->real_escape_string($buscar) . "%'";
}

// Ejecutar la consulta
$resultado = $conn->query($sql);

// Comprobar si hubo un error en la consulta
if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!-- Formulario de búsqueda -->
<form method="POST" class="mb-3">
    <div class="input-group">
        <input type="text" name="buscar" class="form-control" placeholder="Buscar productos..." aria-label="Buscar productos..." value="<?= htmlspecialchars($buscar); ?>">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
</form>

<!-- Tabla de productos -->
<table class="table table-hover table-striped align-middle">
    <thead class="table-dark text-white">
        <tr>
            <th scope="col">Descripción</th>
            <th scope="col">Marca</th>
            <th scope="col">Stock Disponible</th>
            <th scope="col">Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($datos = $resultado->fetch_object()): 
                $stock_disponible = $datos->stock - $datos->stock_minimo; // Calcular el stock disponible
            ?>
                <tr>
                    <td><?= htmlspecialchars($datos->descripcion); ?></td>
                    <td><?= htmlspecialchars($datos->nombre_marca); ?></td>
                    <td><?= htmlspecialchars($stock_disponible); ?></td>
                    <td>
                        <input type="number" class="form-control cantidad" min="1" max="<?= $stock_disponible; ?>" value="1" data-id="<?= $datos->id_producto; ?>" <?= ($stock_disponible <= 0) ? 'disabled' : ''; ?> required>
                        <button type="button" class="btn btn-success comprar" data-id="<?= $datos->id_producto; ?>" <?= ($stock_disponible <= 0) ? 'disabled' : ''; ?>>Comprar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No se encontraron productos.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Script AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.comprar').click(function() {
        var id_producto = $(this).data('id');
        var cantidad = $(this).siblings('.cantidad').val();

        $.ajax({
            url: 'comprar.php', // procesa la compra
            type: 'POST',
            data: {
                id_producto: id_producto,
                cantidad: cantidad
            },
            success: function(response) {
                alert(response); // Muestra la respuesta del servidor
                var productoRow = $("button[data-id='" + id_producto + "']").closest('tr');
                var stockDisponibleCell = productoRow.find('td').eq(2); // Columna del stock disponible

                // Actualizar el stock sin recargar la página
                var nuevoStock = parseInt(stockDisponibleCell.text()) - parseInt(cantidad);
                stockDisponibleCell.text(nuevoStock);

                if (nuevoStock <= 0) {
                    productoRow.find('.comprar').prop('disabled', true); // Desactivar botón si no hay stock
                }
            },
            error: function() {
                alert('Error al procesar la compra.');
            }
        });
    });
});
</script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>