<!DOCTYPE html>
<html lang="es">
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
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            /* Para que la imagen cubra toda la pantalla */
            background-position: center;
            /* Para centrar la imagen */
        }
        header {
            background-color: rgba(33, 37, 41, 0.95); /* similar a bg-dark */
            color: white;
            padding: 15px 0;
            margin-bottom: 20px;
        }
        header h1 {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0 0 0;
        }
    </style>
       
</head>
<body>
    <header>
         <nav class="navbar navbar-expand-lg bg-dark navbar-dark w-100">
            <div class="container-fluid">
                <a class="navbar-brand" href="cerrar_sesion.php">Cerrar Sesion</a>
            </div>
        </nav>
        <h1> Altas, Bajas y Modificaciones de Productos</h1>
    </header>
    

    
    <div class="p-3 table-responsive" style="margin-bottom: 20px;">
        <!-- Boton de registrar producto -->
        <a href="altaprod.php">
            <button type="button" class="btn btn-success mb-4">
                Registrar producto
            </button>
        </a>
        <!-- Boton de registrar marca -->
        <a href="altamarca_index.php">
            <button type="button" class="btn btn-success mb-4">
                Registrar marca
            </button>
        </a>
        <!-- alerta para confirmar la eliminacion -->
        <script>
            function eliminar() {
                let res = confirm("estas seguro de eliminar?");
                return res;
            }
        </script>
        <!-- Boton de reporte -->
        <a href="reporte.php">
            <button type="button" class="btn btn-success mb-4">

                Reporte ventas
            </button>
        </a>
<div class="bg-white bg-opacity-75 p-3 rounded shadow-sm">

    <?php
    include('conexion.php');

    // Llama a la función de conexión
    $conn = conectarDB(); // Obtiene la conexión a la base de datos

    // Inicializar la variable de búsqueda
    $buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

    // Preparar la consulta SQL base
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

    // Consulta para obtener los productos sin stock disponible (stock <= stock mínimo)
    $sql_sin_stock = "SELECT descripcion FROM producto WHERE stock <= stock_minimo";
    $resultado_sin_stock = $conn->query($sql_sin_stock);

    // Almacenar los nombres de los productos sin stock en un array
    $productos_sin_stock = [];
    if ($resultado_sin_stock->num_rows > 0) {
        while ($fila = $resultado_sin_stock->fetch_assoc()) {
            $productos_sin_stock[] = $fila['descripcion'];
        }
    }

    include('eliminar.php');
    ?>

    <?php if (!empty($productos_sin_stock)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>¡Atención!</strong> Los siguientes productos no tienen stock disponible:
            <ul>
                <?php foreach ($productos_sin_stock as $producto): ?>
                    <li><?= htmlspecialchars($producto); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 🔹 Buscador separado de la tabla -->
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar productos..." 
                   aria-label="Buscar productos..." value="<?= htmlspecialchars($buscar); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <!-- 🔹 Tabla con bordes redondeados -->
    <table class="table table-hover table-striped align-middle rounded shadow-sm overflow-hidden">
        <thead class="table-dark text-white">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Descripción</th>
                <th scope="col">Marca</th>
                <th scope="col">Stock</th>
                <th scope="col">Stock Mínimo</th>
                <th scope="col">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($datos = $resultado->fetch_object()): ?>
                    <tr>
                        <th scope="row"><?= htmlspecialchars($datos->id_producto); ?></th>
                        <td><?= htmlspecialchars($datos->descripcion); ?></td>
                        <td><?= htmlspecialchars($datos->nombre_marca); ?></td>
                        <td><?= htmlspecialchars($datos->stock); ?></td>
                        <td><?= htmlspecialchars($datos->stock_minimo); ?></td>
                        <td>
                            <a href="altaprod.php?id=<?= $datos->id_producto ?>" class="btn btn-warning">Editar</a>
                            <a href="crud.php?id=<?= $datos->id_producto ?>" class="btn btn-danger" 
                               onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">
                               Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No se encontraron productos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>