<?php
require_once('verificar_admin.php');

include("conexion.php");
$conn = conectarDB();

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: altamarca_index.php?message=" . urlencode("ID no proporcionado"));
    exit();
}

$id = intval($_GET["id"]);

if ($id <= 0) {
    header("Location: altamarca_index.php?message=" . urlencode("ID inválido"));
    exit();
}

$stmt = $conn->prepare("SELECT * FROM marca WHERE id_marca = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: altamarca_index.php?message=" . urlencode("Marca no encontrada"));
    exit();
}

$marca = $resultado->fetch_object();
$stmt->close();

if (!empty($_POST["registrar"])) {
    if (!empty($_POST["marca"])) {
        $nuevo_nombre = trim($_POST["marca"]);
        
        if (strlen($nuevo_nombre) < 2) {
            $error = "El nombre de la marca debe tener al menos 2 caracteres";
        } else {
            $stmt_check = $conn->prepare("SELECT id_marca FROM marca WHERE nombre_marca = ? AND id_marca != ?");
            $stmt_check->bind_param("si", $nuevo_nombre, $id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $error = "Ya existe una marca con ese nombre";
                $stmt_check->close();
            } else {
                $stmt_check->close();
                
                $stmt_update = $conn->prepare("UPDATE marca SET nombre_marca = ? WHERE id_marca = ?");
                $stmt_update->bind_param("si", $nuevo_nombre, $id);
                
                if ($stmt_update->execute()) {
                    if ($stmt_update->affected_rows > 0) {
                        $stmt_update->close();
                        $conn->close();
                        header("Location: altamarca_index.php?message=" . urlencode("Marca modificada exitosamente"));
                        exit();
                    } else {
                        $error = "No se realizaron cambios";
                    }
                } else {
                    $error = "Error al modificar marca: " . $stmt_update->error;
                }
                
                $stmt_update->close();
            }
        }
    } else {
        $error = "El nombre de la marca no puede estar vacío";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../vista/imagenes/favicon-32x32.png" sizes="32x32" type="image/png">
    <title>Modificar marca</title>
    <style>
        body {
            background-image: url('../vista/imagenes/fondo_crud.png');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
        .admin-info {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid" style="margin-top: 50px;">
        <div class="admin-info">
            <strong>Administrador:</strong> <?php 
                $usuario = obtenerUsuarioActual();
                echo htmlspecialchars($usuario['nombre_completo']); 
            ?>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Modificar nombre de marca
                    </div>

                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            
                            <div class="form-group mb-3">
                                <label for="marca">Marca</label>
                                <input type="text" class="form-control" name="marca" 
                                       value="<?= htmlspecialchars($marca->nombre_marca) ?>" 
                                       required minlength="2">
                            </div>

                            <hr>
                            <div class="text-center">
                                <button type="submit" name="registrar" class="btn btn-primary" value="ok">Modificar</button>
                                <a href="altamarca_index.php"><button type="button" class="btn btn-danger">Volver</button></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>