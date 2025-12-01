<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_cliente'])) {
    header("Location: ../vista/iniciar_sesion.html");
    exit();
}

include('conexion.php');
$conn = conectarDB();

$id_cliente = $_SESSION['id_cliente'];

// Obtener todas las compras del cliente agrupadas por fecha
$sql = "SELECT 
            v.id_venta,
            v.fecha_venta,
            v.total,
            v.estado,
            DATE(v.fecha_venta) as fecha_dia,
            TIME(v.fecha_venta) as hora_compra
        FROM ventas v
        WHERE v.id_cliente = ?
        ORDER BY v.fecha_venta DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();

// Agrupar compras por fecha
$compras_por_fecha = [];
while ($venta = $resultado->fetch_assoc()) {
    $fecha_dia = $venta['fecha_dia'];

    if (!isset($compras_por_fecha[$fecha_dia])) {
        $compras_por_fecha[$fecha_dia] = [];
    }

    $compras_por_fecha[$fecha_dia][] = $venta;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>Mis Compras</title>

    <style>
        body {
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
        }

        header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        .user-info {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: right;
        }

        .user-info strong {
            color: #333;
        }

        .btn-volver {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 15px;
            transition: background-color 0.3s;
        }

        .btn-volver:hover {
            background-color: #0056b3;
            color: white;
        }

        .compras-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .fecha-grupo {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .fecha-titulo {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .compra-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .compra-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .compra-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .compra-id {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
        }

        .compra-hora {
            color: #666;
            font-size: 0.95rem;
        }

        .compra-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #28a745;
        }

        .btn-detalle {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-detalle:hover {
            background-color: #138496;
        }

        .detalle-productos {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .detalle-productos.show {
            display: block;
        }

        .producto-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .producto-item:last-child {
            border-bottom: none;
        }

        .no-compras {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .no-compras i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .estado-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .estado-completada {
            background-color: #d4edda;
            color: #155724;
        }

        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .estado-cancelada {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <header>
        <h1>Mis Compras</h1>

        <div class="user-info">
            <strong><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></strong>
            <a href="productos.php" class="btn-volver">Volver a Productos</a>
        </div>
    </header>

    <div class="compras-container">
        <?php if (empty($compras_por_fecha)): ?>
            <div class="no-compras">
                <i class="fas fa-shopping-bag"></i>
                <h3>No has realizado compras aún</h3>
                <p>Cuando realices tu primera compra, aparecerá aquí.</p>
                <a href="productos.php" class="btn btn-primary mt-3">Ir a Productos</a>
            </div>
        <?php else: ?>
            <?php foreach ($compras_por_fecha as $fecha => $compras): ?>
                <div class="fecha-grupo">
                    <div class="fecha-titulo">
                        <i class="fas fa-calendar-alt"></i>
                        <?php
                        $dias_es = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
                        $dia_semana = $dias_es[date('w', strtotime($fecha))];
                        echo date('d/m/Y', strtotime($fecha)) . ' - ' . ucfirst($dia_semana);
                        ?>
                    </div>

                    <?php foreach ($compras as $compra): ?>
                        <div class="compra-card">
                            <div class="compra-header">
                                <div>
                                    <div class="compra-id">
                                        <i class="fas fa-receipt"></i>
                                        Compra #<?php echo $compra['id_venta']; ?>
                                    </div>
                                    <div class="compra-hora">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('H:i', strtotime($compra['fecha_venta'])); ?>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <div class="compra-total">
                                        $<?php echo number_format($compra['total'], 2, ',', '.'); ?>
                                    </div>
                                    <span class="estado-badge estado-<?php echo $compra['estado']; ?>">
                                        <?php echo ucfirst($compra['estado']); ?>
                                    </span>
                                </div>
                            </div>

                            <button class="btn-detalle" onclick="toggleDetalle(<?php echo $compra['id_venta']; ?>)">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </button>

                            <div id="detalle-<?php echo $compra['id_venta']; ?>" class="detalle-productos">
                                <?php
                                // Obtener detalles de la venta
                                $sql_detalle = "SELECT 
                                                    dv.cantidad,
                                                    dv.precio,
                                                    dv.subtotal,
                                                    p.descripcion
                                                FROM detalle_ventas dv
                                                INNER JOIN producto p ON dv.id_producto = p.id_producto
                                                WHERE dv.id_venta = ?";

                                $stmt_detalle = $conn->prepare($sql_detalle);
                                $stmt_detalle->bind_param("i", $compra['id_venta']);
                                $stmt_detalle->execute();
                                $resultado_detalle = $stmt_detalle->get_result();
                                ?>

                                <h5><i class="fas fa-box"></i> Productos:</h5>

                                <?php while ($detalle = $resultado_detalle->fetch_assoc()): ?>
                                    <div class="producto-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($detalle['descripcion']); ?></strong>
                                            <br>
                                            <small>Cantidad: <?php echo $detalle['cantidad']; ?> x $<?php echo number_format($detalle['precio'], 2, ',', '.'); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong>$<?php echo number_format($detalle['subtotal'], 2, ',', '.'); ?></strong>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetalle(idVenta) {
            var detalle = document.getElementById('detalle-' + idVenta);
            detalle.classList.toggle('show');

            // Cambiar icono del botón
            var btn = event.target.closest('.btn-detalle');
            var icon = btn.querySelector('i');

            if (detalle.classList.contains('show')) {
                icon.className = 'fas fa-eye-slash';
                btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar Detalle';
            } else {
                icon.className = 'fas fa-eye';
                btn.innerHTML = '<i class="fas fa-eye"></i> Ver Detalle';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php $conn->close(); ?>