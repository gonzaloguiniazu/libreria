<?php
require_once('verificar_admin.php');

include('conexion.php');
$conn = conectarDB();

if (!isset($_GET['id'])) {
    header("Location: altamarca_index.php?message=" . urlencode("ID no proporcionado"));
    exit();
}

$id = intval($_GET['id']);

if ($id <= 0) {
    header("Location: altamarca_index.php?message=" . urlencode("ID inválido"));
    exit();
}

$stmtCheck = $conn->prepare("SELECT COUNT(*) AS cnt FROM producto WHERE id_marca = ?");
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$res = $stmtCheck->get_result();
$row = $res->fetch_assoc();
$stmtCheck->close();

if ($row && intval($row['cnt']) > 0) {
    $conn->close();
    header("Location: altamarca_index.php?message=" . urlencode("No se puede eliminar: hay productos asociados a esta marca"));
    exit();
}

$stmt = $conn->prepare("DELETE FROM marca WHERE id_marca = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: altamarca_index.php?message=" . urlencode("Marca eliminada correctamente"));
    exit();
} else {
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: altamarca_index.php?message=" . urlencode("Error al eliminar: " . $error));
    exit();
}
?>