<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Marca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
</head>

<body style="background-image: url('../vista/imagenes/fondo_crud.png'); background-size: cover; background-position: center;">

    <!-- Header con el título -->
    <header class="bg-dark text-white text-center py-4 mb-4 shadow-sm">
        <h1 class="fw-bold">Registrar Marcas</h1>
        <a href="crud.php" class="btn btn-light mt-2">Volver al Panel de Productos</a>
    </header>

    <div class="container">
        <?php if (isset($_GET['message'])): ?>
        <div class="container mt-3">
            <div class="alert alert-info"><?= htmlspecialchars($_GET['message']); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulario centrado -->
        <div class="d-flex justify-content-center">
            <div class="bg-white bg-opacity-75 p-4 rounded shadow-sm mb-4 mt-4" style="max-width: 500px; width: 100%;">
                <h4 class="text-center mb-3">Agregar nueva marca</h4>
                <form action="altamarca.php" method="POST">
                    <div class="mb-3">
                        <label for="nombre_marca" class="form-label">Nombre de la Marca</label>
                        <input type="text" name="nombre_marca" id="nombre_marca" class="form-control" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success px-4">Registrar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla centrada -->
        <div class="bg-white bg-opacity-75 p-4 rounded shadow-sm mb-4">
            <h4 class="text-center mb-3">Listado de Marcas Registradas</h4>
            <?php
            include('conexion.php');
            $conn = conectarDB();

            $sql = "SELECT * FROM marca";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark text-white">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre de la Marca</th>
                                <th scope="col">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($marca = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($marca['id_marca']); ?></td>
                                    <td><?= htmlspecialchars($marca['nombre_marca']); ?></td>
                                    <td>
                                        <a href="eliminar_marca.php?id=<?= $marca['id_marca']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Estás seguro de eliminar esta marca?');">
                                           Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No hay marcas registradas.</p>
            <?php endif; ?>

            <?php $conn->close(); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>
</html>
