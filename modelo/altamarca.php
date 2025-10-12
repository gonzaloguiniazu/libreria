<?php
// altamarca.php
include('conexion.php');    // Asegurate que la ruta sea correcta
$conn = conectarDB();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre_marca']) ? trim($_POST['nombre_marca']) : '';

    if ($nombre === '') {
        // Redirigir con mensaje de error (opcional)
        header("Location: altamarca_index.php?message=" . urlencode("El nombre de la marca no puede estar vacío"));
        exit;
    }

    // Verificar si ya existe la marca
    $stmtCheck = $conn->prepare("SELECT id_marca FROM marca WHERE nombre_marca = ?");
    $stmtCheck->bind_param("s", $nombre);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if ($resCheck && $resCheck->num_rows > 0) {
        header("Location: altamarca_index.php?message=" . urlencode("La marca ya existe"));
        exit;
    }
    $stmtCheck->close();

    // Insertar la marca
    $stmt = $conn->prepare("INSERT INTO marca (nombre_marca) VALUES (?)");
    if (!$stmt) {
        // error en preparación
        header("Location: altamarca_index.php?message=" . urlencode("Error en la consulta: " . $conn->error));
        exit;
    }
    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        header("Location: altamarca_index.php?message=" . urlencode("Marca registrada con éxito"));
        exit;
    } else {
        header("Location: altamarca_index.php?message=" . urlencode("Error al registrar: " . $stmt->error));
        exit;
    }
} else {
    // Si se accede por GET, redirigir a la página
    header("Location: altamarca_index.php");
    exit;
}

