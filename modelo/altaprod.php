<?php
require_once('verificar_admin.php');

include('conexion.php');
$conn = conectarDB();

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
$descripcion = $precio = $stock = $stock_minimo = $id_marca = $imagen = "";

if ($id_producto > 0) {
    $sql = "SELECT * FROM producto WHERE id_producto = $id_producto";
    $resultado = $conn->query($sql);
    if ($resultado && $resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $descripcion = $producto['descripcion'];
        $precio = $producto['precio'];
        $stock = $producto['stock'];
        $stock_minimo = $producto['stock_minimo'];
        $id_marca = $producto['id_marca'];
        $imagen = $producto['imagen'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id_producto > 0 ? "Editar Producto" : "Registrar Producto" ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <style>
        header.container-header {
            background-color: rgba(33,37,41,0.95);
            color: white;
            margin-bottom: 20px;
        }
        header .container-fluid {
            position: relative;
        }
        header .titulo-centrado {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }
        .card-form {
            max-width: 700px;
            margin: 0 auto;
        }
        .admin-info {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body style="background-image: url('../vista/imagenes/fondo_crud.png'); background-size: cover; background-position: center;">

    <header class="container-header">
        <div class="container-fluid py-3 d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white ms-3" href="crud.php">Volver al listado de productos</a>
            <h2 class="titulo-centrado"><?= $id_producto > 0 ? "Editar Producto" : "Registrar Producto" ?></h2>
            <div class="admin-info me-3">
                <?php 
                    $usuario = obtenerUsuarioActual();
                    echo htmlspecialchars($usuario['nombre_completo']); 
                ?>
            </div>
        </div>
    </header>

    <div class="container mb-5">
        <div class="bg-white bg-opacity-75 p-4 rounded shadow-sm card-form">
            <form action="altaprod_guardar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_producto" value="<?= $id_producto ?>">

                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-bold">Descripción del producto</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required value="<?= htmlspecialchars($descripcion) ?>">
                </div>

                <div class="mb-3">
                    <label for="imagen" class="form-label fw-bold">Ruta de la imagen</label>
                    <div class="input-group">
                        <input type="text" id="imagen" name="imagen" class="form-control" placeholder="../vista/imagenes/ejemplo.jpg" value="<?= htmlspecialchars($imagen) ?>">
                        <button class="btn btn-secondary" type="button" id="btn-explorar">Explorar</button>
                    </div>
                    <input type="file" id="file-input" accept="image/*" style="display: none;">
                    <div class="form-text">Al seleccionar un archivo, se completará el nombre en el campo anterior.</div>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label fw-bold">Precio</label>
                    <input type="number" class="form-control" id="precio" name="precio" required min="0" step="0.01" value="<?= ($precio !== "") ? htmlspecialchars($precio) : "0.00" ?>">
                </div>

                <div class="mb-3">
                    <label for="id_marca" class="form-label fw-bold">Marca</label>
                    <select class="form-select" id="id_marca" name="id_marca" required>
                        <option value="">Seleccione una marca</option>
                        <?php
                        $marcas = $conn->query("SELECT id_marca, nombre_marca FROM marca ORDER BY nombre_marca");
                        while ($row = $marcas->fetch_assoc()) {
                            $sel = ($id_marca == $row['id_marca']) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($row['id_marca'])."' $sel>".htmlspecialchars($row['nombre_marca'])."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label fw-bold">Stock actual</label>
                    <input type="number" class="form-control" id="stock" name="stock" required min="0" value="<?= ($stock !== "") ? htmlspecialchars($stock) : "0" ?>">
                </div>

                <div class="mb-3">
                    <label for="stock_minimo" class="form-label fw-bold">Stock mínimo</label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required min="0" value="<?= ($stock_minimo !== "") ? htmlspecialchars($stock_minimo) : "0" ?>">
                </div>

                <div class="text-center mt-4 d-flex gap-3 justify-content-center">
                    <button type="submit" class="btn btn-success px-4"><?= $id_producto > 0 ? "Guardar Cambios" : "Guardar Producto" ?></button>
                    <a href="crud.php" class="btn btn-secondary px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('btn-explorar').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    document.getElementById('file-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        document.getElementById('imagen').value = 'imagenes/' + file.name;
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>