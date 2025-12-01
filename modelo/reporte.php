<?php
session_start();

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['id_cliente']) || $_SESSION['email'] !== 'admin@admin') {
    header("Location: ../vista/iniciar_sesion.html");
    exit();
}

include('conexion.php');
$conn = conectarDB();

// Filtros
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';
$filtro_cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';

// Construir consulta con filtros
$sql = "SELECT 
            v.id_venta,
            v.fecha_venta,
            v.total,
            v.estado,
            c.nombre,
            c.apellido,
            c.email,
            DATE(v.fecha_venta) as fecha_dia,
            TIME(v.fecha_venta) as hora_compra
        FROM ventas v
        INNER JOIN cliente c ON v.id_cliente = c.id_cliente
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($filtro_fecha_desde)) {
    $sql .= " AND DATE(v.fecha_venta) >= ?";
    $params[] = $filtro_fecha_desde;
    $types .= "s";
}

if (!empty($filtro_fecha_hasta)) {
    $sql .= " AND DATE(v.fecha_venta) <= ?";
    $params[] = $filtro_fecha_hasta;
    $types .= "s";
}

if (!empty($filtro_cliente)) {
    $sql .= " AND (c.nombre LIKE ? OR c.apellido LIKE ? OR c.email LIKE ?)";
    $busqueda = "%$filtro_cliente%";
    $params[] = $busqueda;
    $params[] = $busqueda;
    $params[] = $busqueda;
    $types .= "sss";
}

$sql .= " ORDER BY v.fecha_venta DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Calcular estadísticas
$total_ventas = 0;
$cantidad_ventas = 0;
$ventas_por_fecha = [];

while ($venta = $resultado->fetch_assoc()) {
    $fecha_dia = $venta['fecha_dia'];

    if (!isset($ventas_por_fecha[$fecha_dia])) {
        $ventas_por_fecha[$fecha_dia] = [];
    }

    $ventas_por_fecha[$fecha_dia][] = $venta;
    $total_ventas += $venta['total'];
    $cantidad_ventas++;
}

// Obtener lista de clientes para el filtro
$sql_clientes = "SELECT DISTINCT c.id_cliente, c.nombre, c.apellido 
                 FROM cliente c 
                 INNER JOIN ventas v ON c.id_cliente = v.id_cliente 
                 WHERE c.email != 'admin@admin'
                 ORDER BY c.nombre, c.apellido";
$resultado_clientes = $conn->query($sql_clientes);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>Reporte de Ventas</title>

    <style>
        body {
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: rgba(33, 37, 41, 0.95);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        header h1 {
            text-align: center;
            margin: 0;
            font-size: 2.5rem;
        }

        .admin-info {
            text-align: center;
            margin-top: 10px;
        }

        .btn-volver {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-volver:hover {
            background-color: #0056b3;
            color: white;
        }

        .reportes-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .estadisticas-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .estadistica-item {
            text-align: center;
            padding: 20px;
        }

        .estadistica-valor {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .estadistica-label {
            color: #666;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .filtros-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .fecha-grupo {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .fecha-titulo {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .venta-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .venta-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .venta-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .cliente-info {
            font-size: 1.1rem;
            color: #333;
        }

        .cliente-nombre {
            font-weight: bold;
            color: #007bff;
        }

        .venta-hora {
            color: #666;
            font-size: 0.95rem;
        }

        .venta-total {
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

        .no-ventas {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .no-ventas i {
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

        .btn-exportar {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        .btn-exportar:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <header>
        <h1><i class="fas fa-chart-line"></i> Reporte de Ventas</h1>
        <div class="admin-info">

            <a href="crud.php" class="btn-volver">Volver al Panel</a>
            <a href="cerrar_sesion.php" class="btn-volver" style="background-color: #dc3545;">Cerrar Sesión</a>
        </div>
    </header>

    <div class="reportes-container">
        <!-- Estadísticas Generales -->
        <div class="estadisticas-card">
            <h3 class="text-center mb-4"><i class="fas fa-chart-bar"></i> Estadísticas Generales</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="estadistica-item">
                        <div class="estadistica-valor">
                            <?php echo $cantidad_ventas; ?>
                        </div>
                        <div class="estadistica-label">
                            <i class="fas fa-shopping-cart"></i> Total de Ventas
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="estadistica-item">
                        <div class="estadistica-valor" style="color: #28a745;">
                            $<?php echo number_format($total_ventas, 2, ',', '.'); ?>
                        </div>
                        <div class="estadistica-label">
                            <i class="fas fa-dollar-sign"></i> Ingresos Totales
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="estadistica-item">
                        <div class="estadistica-valor" style="color: #ffc107;">
                            $<?php echo $cantidad_ventas > 0 ? number_format($total_ventas / $cantidad_ventas, 2, ',', '.') : '0,00'; ?>
                        </div>
                        <div class="estadistica-label">
                            <i class="fas fa-chart-pie"></i> Promedio por Venta
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-card">
            <h4><i class="fas fa-filter"></i> Filtrar Ventas</h4>
            <form method="GET" action="reporte.php" class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label">Fecha Desde:</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?php echo htmlspecialchars($filtro_fecha_desde); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Hasta:</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?php echo htmlspecialchars($filtro_fecha_hasta); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar Cliente:</label>
                    <input type="text" name="cliente" class="form-control" placeholder="Nombre, apellido o email..." value="<?php echo htmlspecialchars($filtro_cliente); ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <a href="reporte.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Limpiar Filtros
                </a>
                <button onclick="exportarExcel()" class="btn-exportar">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </button>
            </div>
        </div>

        <!-- Ventas por Fecha -->
        <?php if (empty($ventas_por_fecha)): ?>
            <div class="no-ventas">
                <i class="fas fa-inbox"></i>
                <h3>No se encontraron ventas</h3>
                <p>No hay ventas registradas con los filtros aplicados.</p>
            </div>
        <?php else: ?>
            <?php foreach ($ventas_por_fecha as $fecha => $ventas): ?>
                <div class="fecha-grupo">
                    <div class="fecha-titulo">
                        <i class="fas fa-calendar-alt"></i>
                        <?php
                        $dias_es = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
                        $dia_semana = $dias_es[date('w', strtotime($fecha))];
                        echo date('d/m/Y', strtotime($fecha)) . ' - ' . ucfirst($dia_semana);
                        ?>
                        <span style="float: right;">
                            <?php
                            $total_dia = array_sum(array_column($ventas, 'total'));
                            echo count($ventas) . " venta(s) - Total: $" . number_format($total_dia, 2, ',', '.');
                            ?>
                        </span>
                    </div>

                    <?php foreach ($ventas as $venta): ?>
                        <div class="venta-card">
                            <div class="venta-header">
                                <div>
                                    <div class="cliente-info">
                                        <i class="fas fa-user"></i>
                                        <span class="cliente-nombre">
                                            <?php echo htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido']); ?>
                                        </span>
                                        <br>
                                        <small style="color: #666;">
                                            <i class="fas fa-envelope"></i>
                                            <?php echo htmlspecialchars($venta['email']); ?>
                                        </small>
                                    </div>
                                    <div class="venta-hora mt-2">
                                        <i class="fas fa-receipt"></i> Venta #<?php echo $venta['id_venta']; ?>
                                        |
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('H:i', strtotime($venta['fecha_venta'])); ?>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <div class="venta-total">
                                        $<?php echo number_format($venta['total'], 2, ',', '.'); ?>
                                    </div>
                                    <span class="estado-badge estado-<?php echo $venta['estado']; ?>">
                                        <?php echo ucfirst($venta['estado']); ?>
                                    </span>
                                </div>
                            </div>

                            <button class="btn-detalle" onclick="toggleDetalle(<?php echo $venta['id_venta']; ?>)">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </button>

                            <div id="detalle-<?php echo $venta['id_venta']; ?>" class="detalle-productos">
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
                                $stmt_detalle->bind_param("i", $venta['id_venta']);
                                $stmt_detalle->execute();
                                $resultado_detalle = $stmt_detalle->get_result();
                                ?>

                                <h5><i class="fas fa-box"></i> Productos Comprados:</h5>

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

        function exportarExcel() {
            // Crear tabla HTML con los datos visibles
            var tabla = '<table border="1"><thead><tr>' +
                '<th>Fecha</th><th>Hora</th><th>Venta #</th>' +
                '<th>Cliente</th><th>Email</th><th>Total</th><th>Estado</th>' +
                '</tr></thead><tbody>';

            <?php foreach ($ventas_por_fecha as $fecha => $ventas): ?>
                <?php foreach ($ventas as $venta): ?>
                    tabla += '<tr>' +
                        '<td><?php echo $fecha; ?></td>' +
                        '<td><?php echo date("H:i", strtotime($venta["fecha_venta"])); ?></td>' +
                        '<td><?php echo $venta["id_venta"]; ?></td>' +
                        '<td><?php echo htmlspecialchars($venta["nombre"] . " " . $venta["apellido"]); ?></td>' +
                        '<td><?php echo htmlspecialchars($venta["email"]); ?></td>' +
                        '<td>$<?php echo number_format($venta["total"], 2, ",", "."); ?></td>' +
                        '<td><?php echo $venta["estado"]; ?></td>' +
                        '</tr>';
                <?php endforeach; ?>
            <?php endforeach; ?>

            tabla += '</tbody></table>';

            // Crear blob y descargar
            var blob = new Blob([tabla], {
                type: 'application/vnd.ms-excel'
            });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'reporte_ventas_' + new Date().toISOString().split('T')[0] + '.xls';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php $conn->close(); ?>