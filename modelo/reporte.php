<?php
require_once('verificar_admin.php');
include('conexion.php');
$conn = conectarDB();

$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$filtro_cliente = $_GET['cliente'] ?? '';

$sql = "SELECT v.id_venta, v.fecha_venta, v.total, v.estado, c.nombre, c.apellido, c.email,
               DATE(v.fecha_venta) as fecha_dia, TIME(v.fecha_venta) as hora_compra
        FROM ventas v
        INNER JOIN cliente c ON v.id_cliente = c.id_cliente WHERE 1=1";

$params = [];
$types = "";

if ($filtro_fecha_desde) {
    $sql .= " AND DATE(v.fecha_venta) >= ?";
    $params[] = $filtro_fecha_desde;
    $types .= "s";
}

if ($filtro_fecha_hasta) {
    $sql .= " AND DATE(v.fecha_venta) <= ?";
    $params[] = $filtro_fecha_hasta;
    $types .= "s";
}

if ($filtro_cliente) {
    $sql .= " AND (c.nombre LIKE ? OR c.apellido LIKE ? OR c.email LIKE ?)";
    $busqueda = "%$filtro_cliente%";
    $params[] = $busqueda;
    $params[] = $busqueda;
    $params[] = $busqueda;
    $types .= "sss";
}

$sql .= " ORDER BY v.fecha_venta DESC";
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$total_ventas = 0;
$cantidad_ventas = 0;
$ventas_por_fecha = [];

while ($venta = $resultado->fetch_assoc()) {
    $ventas_por_fecha[$venta['fecha_dia']][] = $venta;
    $total_ventas += $venta['total'];
    $cantidad_ventas++;
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
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="../vista/reporte_styles.css">
</head>
<body>
    <header>
        <h1><i class="fas fa-chart-line"></i> Reporte de Ventas</h1>
        <div class="admin-info">
            <span class="admin-welcome">
                <i class="fas fa-user-shield"></i> 
                Admin: <?php echo htmlspecialchars(obtenerUsuarioActual()['nombre_completo']); ?>
            </span>
            <a href="crud.php" class="btn-volver">Volver</a>
            <a href="cerrar_sesion_admin.php" class="btn-volver btn-danger">Cerrar Sesión</a>
        </div>
    </header>

    <div class="reportes-container">
        <div class="estadisticas-card">
            <h3 class="text-center mb-4"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
            <div class="row">
                <div class="col-md-4"><div class="estadistica-item">
                    <div class="estadistica-valor"><?= $cantidad_ventas ?></div>
                    <div class="estadistica-label"><i class="fas fa-shopping-cart"></i> Total de Ventas</div>
                </div></div>
                <div class="col-md-4"><div class="estadistica-item">
                    <div class="estadistica-valor" style="color: #28a745;">$<?= number_format($total_ventas, 2, ',', '.') ?></div>
                    <div class="estadistica-label"><i class="fas fa-dollar-sign"></i> Ingresos Totales</div>
                </div></div>
                <div class="col-md-4"><div class="estadistica-item">
                    <div class="estadistica-valor" style="color: #ffc107;">$<?= $cantidad_ventas > 0 ? number_format($total_ventas / $cantidad_ventas, 2, ',', '.') : '0,00' ?></div>
                    <div class="estadistica-label"><i class="fas fa-chart-pie"></i> Promedio</div>
                </div></div>
            </div>
        </div>

        <div class="filtros-card">
            <h4><i class="fas fa-filter"></i> Filtrar Ventas</h4>
            <form method="GET" class="row g-3 mt-2">
                <div class="col-md-3">
                    <label>Fecha Desde:</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                </div>
                <div class="col-md-3">
                    <label>Fecha Hasta:</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                </div>
                <div class="col-md-4">
                    <label>Buscar Cliente:</label>
                    <input type="text" name="cliente" class="form-control" placeholder="Nombre, email..." value="<?= htmlspecialchars($filtro_cliente) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </form>
            <div class="mt-3">
                <a href="reporte.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Limpiar</a>
            </div>
        </div>

        <?php if (empty($ventas_por_fecha)): ?>
            <div class="no-ventas">
                <i class="fas fa-inbox"></i>
                <h3>No se encontraron ventas</h3>
            </div>
        <?php else: ?>
            <?php foreach ($ventas_por_fecha as $fecha => $ventas): ?>
                <div class="fecha-grupo">
                    <div class="fecha-titulo">
                        <i class="fas fa-calendar-alt"></i>
                        <?php
                        $dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
                        echo date('d/m/Y', strtotime($fecha)) . ' - ' . ucfirst($dias[date('w', strtotime($fecha))]);
                        ?>
                        <span style="float: right;">
                            <?= count($ventas) ?> venta(s) - Total: $<?= number_format(array_sum(array_column($ventas, 'total')), 2, ',', '.') ?>
                        </span>
                    </div>

                    <?php foreach ($ventas as $venta): ?>
                        <div class="venta-card">
                            <div class="venta-header">
                                <div>
                                    <div class="cliente-info">
                                        <i class="fas fa-user"></i>
                                        <span class="cliente-nombre"><?= htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido']) ?></span>
                                        <br><small style="color: #666;"><i class="fas fa-envelope"></i> <?= htmlspecialchars($venta['email']) ?></small>
                                    </div>
                                    <div class="venta-hora mt-2">
                                        <i class="fas fa-receipt"></i> Venta #<?= $venta['id_venta'] ?> | 
                                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($venta['fecha_venta'])) ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="venta-total">$<?= number_format($venta['total'], 2, ',', '.') ?></div>
                                    <span class="estado-badge estado-<?= $venta['estado'] ?>"><?= ucfirst($venta['estado']) ?></span>
                                </div>
                            </div>

                            <button class="btn-detalle" onclick="toggleDetalle(<?= $venta['id_venta'] ?>)">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </button>

                            <div id="detalle-<?= $venta['id_venta'] ?>" class="detalle-productos">
                                <?php
                                $sql_det = "SELECT dv.cantidad, dv.precio, dv.subtotal, p.descripcion
                                           FROM detalle_ventas dv
                                           INNER JOIN producto p ON dv.id_producto = p.id_producto
                                           WHERE dv.id_venta = ?";
                                $stmt_det = $conn->prepare($sql_det);
                                $stmt_det->bind_param("i", $venta['id_venta']);
                                $stmt_det->execute();
                                $res_det = $stmt_det->get_result();
                                ?>
                                <h5><i class="fas fa-box"></i> Productos:</h5>
                                <?php while ($det = $res_det->fetch_assoc()): ?>
                                    <div class="producto-item">
                                        <div>
                                            <strong><?= htmlspecialchars($det['descripcion']) ?></strong><br>
                                            <small>Cant: <?= $det['cantidad'] ?> x $<?= number_format($det['precio'], 2, ',', '.') ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong>$<?= number_format($det['subtotal'], 2, ',', '.') ?></strong>
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
        function toggleDetalle(id) {
            var detalle = document.getElementById('detalle-' + id);
            detalle.classList.toggle('show');
            var btn = event.target.closest('.btn-detalle');
            var icon = btn.querySelector('i');
            if (detalle.classList.contains('show')) {
                icon.className = 'fas fa-eye-slash';
                btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar';
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