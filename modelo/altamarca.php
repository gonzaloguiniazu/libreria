<?php
require_once('verificar_admin.php');

include('conexion.php');
$conn = conectarDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre_marca']) ? trim($_POST['nombre_marca']) : '';

    if ($nombre === '') {
        $conn->close();
        header("Location: altamarca_index.php?message=" . urlencode("El nombre de la marca no puede estar vacío"));
        exit();
    }

    if (strlen($nombre) < 2) {
        $conn->close();
        header("Location: altamarca_index.php?message=" . urlencode("El nombre debe tener al menos 2 caracteres"));
        exit();
    }

    $stmtCheck = $conn->prepare("SELECT id_marca FROM marca WHERE nombre_marca = ?");
    $stmtCheck->bind_param("s", $nombre);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if ($resCheck && $resCheck->num_rows > 0) {
        $stmtCheck->close();
        $conn->close();
        header("Location: altamarca_index.php?message=" . urlencode("La marca ya existe"));
        exit();
    }
    $stmtCheck->close();

    $stmt = $conn->prepare("INSERT INTO marca (nombre_marca) VALUES (?)");
    if (!$stmt) {
        $conn->close();
        header("Location: altamarca_index.php?message=" . urlencode("Error en la consulta"));
        exit();
    }
    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: altamarca_index.php?message=" . urlencode("Marca registrada con éxito"));
        exit();
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: altamarca_index.php?message=" . urlencode("Error al registrar: " . $error));
        exit();
    }
} else {
    $conn->close();
    header("Location: altamarca_index.php");
    exit();
}
?>