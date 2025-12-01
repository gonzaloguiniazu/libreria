<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_cliente'])) {
    header("Location: ../vista/iniciar_sesion.html");
    exit();
}

include('conexion.php');
$conn = conectarDB();

// Obtener todos los productos de la base de datos
$sql = "SELECT p.id_producto, p.descripcion, p.precio, p.imagen, p.stock, p.stock_minimo, m.nombre_marca 
        FROM producto p
        INNER JOIN marca m ON p.id_marca = m.id_marca
        WHERE p.stock > 0
        ORDER BY p.id_producto ASC";

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../vista/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>Productos - Tienda Online</title>
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        body {
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        header {
            background-color: #333;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        header h1 {
            text-align: center;
            margin: 0;
            font-size: 2.5rem;
            font-family: "Dancing Script", cursive;
        }
        
        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 15px 30px;
            margin: 15px 20px 0 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .user-welcome {
            font-size: 1.1rem;
            color: #333;
            font-weight: bold;
        }
        
        .user-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-action {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-compras {
            background-color: #28a745;
            color: white;
        }
        
        .btn-compras:hover {
            background-color: #218838;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn-logout {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-logout:hover {
            background-color: #c82333;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        section.contenedor {
            flex: 1;
        }
        
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Productos Disponibles</h1>
        
        <!-- Información del usuario -->
        <div class="user-info">
            <div class="user-welcome">
                <i class="fas fa-user-circle"></i>
                Bienvenido: <?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?>
            </div>
            <div class="user-buttons">
                <a href="mis_compras.php" class="btn-action btn-compras">
                    <i class="fas fa-shopping-bag"></i> Mis Compras
                </a>
                <a href="cerrar_sesion.php" class="btn-action btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </header>
    
    <section class="contenedor">
        <!-- Contenedor de elementos -->
        <div class="contenedor-items">
            
            <?php 
            // Generar productos dinámicamente
            if ($resultado->num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    // Calcular stock disponible
                    $stock_disponible = $producto['stock'] - $producto['stock_minimo'];
                    
                    // Verificar si hay stock
                    $sin_stock = $stock_disponible <= 0;
                    
                    // Formatear precio
                    $precio_formateado = number_format($producto['precio'], 0, ',', '.');
                    
                    // Determinar la ruta de la imagen
                    $imagen = !empty($producto['imagen']) ? $producto['imagen'] : 'imagenes/sin-imagen.jpg';
                    
                    // Si la imagen no tiene la ruta completa, agregar el prefijo
                    if (strpos($imagen, '../vista/') === false && strpos($imagen, 'imagenes/') !== false) {
                        $imagen = '../vista/' . $imagen;
                    }
            ?>
            
            <!-- Producto ID: <?php echo $producto['id_producto']; ?> -->
            <div class="item">
                <span class="titulo-item"><?php echo htmlspecialchars(strtoupper($producto['descripcion'])); ?></span>
                
                <img src="<?php echo htmlspecialchars($imagen); ?>" 
                     alt="<?php echo htmlspecialchars($producto['descripcion']); ?>" 
                     class="img-item"
                     onerror="this.src='../vista/imagenes/sin-imagen.jpg'">
                
                <span class="precio-item">$<?php echo $precio_formateado; ?></span>
                
                <?php if ($sin_stock): ?>
                    <button class="boton-item" disabled style="background-color: #ccc; cursor: not-allowed;" data-id-producto="<?php echo $producto['id_producto']; ?>">
                        Sin Stock
                    </button>
                    <small class="stock-info" style="color: red; display: block; margin-top: 5px;">
                        Stock no disponible
                    </small>
                <?php else: ?>
                    <button class="boton-item" data-id-producto="<?php echo $producto['id_producto']; ?>">
                        Agregar al Carrito
                    </button>
                    <small class="stock-info" style="color: green; display: block; margin-top: 5px; font-size: 16px;">
                        Stock disponible: <?php echo $stock_disponible; ?>
                    </small>
                <?php endif; ?>
            </div>
            
            <?php 
                } // fin while
            } else {
                echo '<p style="text-align: center; color: white; font-size: 20px;">No hay productos disponibles en este momento.</p>';
            }
            
            $conn->close();
            ?>
            
        </div>
        
        <!-- Carrito de Compras -->
        <div class="carrito" id="carrito">
            <div class="header-carrito">
                <h2>Carrito de Compras</h2>
            </div>

            <div class="carrito-items">
                <!-- Los items se cargarán dinámicamente -->
            </div>
            
            <div class="carrito-total">
                <div class="fila">
                    <strong>Total a Pagar</strong>
                    <span class="carrito-precio-total">$0,00</span>
                </div>
                <button class="btn-pagar">Finalizar Compra <i class="fa-solid fa-bag-shopping"></i></button>
            </div>
        </div>
    </section>
    
    <script src="../controlador/carrito.js"></script>
    
    <footer>
        <p>&copy; 2025 Estación del Arte - Todos los derechos reservados</p>
    </footer>
</body>
</html>