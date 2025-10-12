<?php
// eliminar_marca.php
include('conexion.php');
$conn = conectarDB();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    header("Location: altamarca_index.php?message=" . urlencode("ID no proporcionado"));
    exit;
}

$id = intval($_GET['id']);

if ($id <= 0) {
    header("Location: altamarca_index.php?message=" . urlencode("ID inválido"));
    exit;
}

// Opción 1: eliminar si no hay productos asociados
// Verificar si existen productos con esta marca
$stmtCheck = $conn->prepare("SELECT COUNT(*) AS cnt FROM producto WHERE id_marca = ?");
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$res = $stmtCheck->get_result();
$row = $res->fetch_assoc();
$stmtCheck->close();

if ($row && intval($row['cnt']) > 0) {
    header("Location: altamarca_index.php?message=" . urlencode("No se puede eliminar: hay productos asociados a esta marca"));
    exit;
}

// Si no hay productos asociados, eliminar
$stmt = $conn->prepare("DELETE FROM marca WHERE id_marca = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: altamarca_index.php?message=" . urlencode("Marca eliminada correctamente"));
    exit;
} else {
    header("Location: altamarca_index.php?message=" . urlencode("Error al eliminar: " . $stmt->error));
    exit;
}
