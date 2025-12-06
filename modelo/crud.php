<?php
require_once('verificar_admin.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>ABM Productos</title>   
    
    <style>
        body {
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            background-position: center;
        }
        header {
            background-color: rgba(33, 37, 41, 0.95);
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
                <span class="navbar-brand">Panel de Administración</span>
                <div class="d-flex">
                    <span class="text-white me-3">
                        Bienvenido: <?php 
                            $usuario = obtenerUsuarioActual();
                            echo htmlspecialchars($usuario['nombre_completo']); 
                        ?>
                    </span>
                    <a class="btn btn-danger btn-sm" href="cerrar_sesion_admin.php">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
        <h1>Altas, Bajas y Modificaciones de Productos</h1>
    </header>
    
    <div class="p-3 table-responsive" style="margin-bottom: 20px;">
        <a href="altaprod.php">
            <button type="button" class="btn btn-success mb-4">Registrar producto</button>
        </a>
        <a href="altamarca_index.php">
            <button type="button" class="btn btn-success mb-4">Registrar marca</button>
        </a>
        <script>
            function eliminar() {
                let res = confirm("¿Estás seguro de eliminar?");
                return res;
            }
        </script>
        <a href="reporte.php">
            <button type="button" class="btn btn-success mb-4">Reporte ventas</button>
        </a>

        <div class="bg-white bg-opacity-75 p-3 rounded shadow-sm">
            <?php
            include('conexion.php');
            $conn = conectarDB();

            $buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

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

            $sql_sin_stock = "SELECT descripcion FROM producto WHERE stock <= stock_minimo";
            $resultado_sin_stock = $conn->query($sql_sin_stock);

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

            <form method="POST" class="mb-4">
                <div class="input-group">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar productos..." 
                           aria-label="Buscar productos..." value="<?= htmlspecialchars($buscar); ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>

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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>