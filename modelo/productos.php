<?php
session_start();

// Validación de sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../vista/iniciar_sesion.html");
    exit();
}

include('conexion.php');
$conn = conectarDB();

// Inicializar la variable de búsqueda
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

// Consulta SQL
$sql = "SELECT p.id_producto, p.descripcion, p.stock, p.stock_minimo, m.nombre_marca AS nombre_marca 
        FROM producto p
        INNER JOIN marca m ON p.id_marca = m.id_marca";

if ($buscar) {
    $sql .= " WHERE p.descripcion LIKE '%" . $conn->real_escape_string($buscar) . "%'";
}

$resultado = $conn->query($sql);
if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet" href="../vista/productos.css">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
</head>
<body>

<header class="custom-header">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="text-white">Catálogo de Librería</h1>
        <nav>
            <ul class="nav">
                <li class="nav-item">
                    <a class="btn btn-danger" href="../vista/index.html">Cerrar sesión</a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<main class="container py-4">
    <!-- Formulario de búsqueda -->
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar productos..."
                   value="<?= htmlspecialchars($buscar); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <!-- Catálogo de productos -->
    <div class="row">
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($datos = $resultado->fetch_object()): 
                $stock_disponible = $datos->stock - $datos->stock_minimo;
            ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card mb-4 shadow-sm product-card">
                        <img src="../vista/imagenes/<?= $datos->id_producto ?>.jpg" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($datos->descripcion); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($datos->descripcion); ?></h5>
                            <p class="card-text"><strong>Marca:</strong> <?= htmlspecialchars($datos->nombre_marca); ?></p>
                            <p class="card-text"><strong>Stock disponible:</strong> <?= $stock_disponible; ?></p>
                            <input type="number" class="form-control cantidad mb-2" 
                                   min="1" max="<?= $stock_disponible; ?>" value="1" 
                                   data-id="<?= $datos->id_producto; ?>" 
                                   <?= ($stock_disponible <= 0) ? 'disabled' : ''; ?>>
                            <button type="button" class="btn btn-success comprar w-100" 
                                    data-id="<?= $datos->id_producto; ?>" 
                                    <?= ($stock_disponible <= 0) ? 'disabled' : ''; ?>>
                                Comprar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No se encontraron productos.</p>
        <?php endif; ?>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.comprar').click(function() {
        var id_producto = $(this).data('id');
        var cantidad = $(this).siblings('.cantidad').val();

        $.ajax({
            url: 'comprar.php',
            type: 'POST',
            data: {
                id_producto: id_producto,
                cantidad: cantidad
            },
            success: function(response) {
                alert(response);
                var productoCard = $("button[data-id='" + id_producto + "']").closest('.card');
                var stockDisponible = productoCard.find('.card-text').eq(1); 

                var nuevoStock = parseInt(stockDisponible.text().replace(/\D/g,'')) - parseInt(cantidad);
                stockDisponible.html("<strong>Stock disponible:</strong> " + nuevoStock);

                if (nuevoStock <= 0) {
                    productoCard.find('.comprar').prop('disabled', true);
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
        crossorigin="anonymous"></script>
</body>
</html>
