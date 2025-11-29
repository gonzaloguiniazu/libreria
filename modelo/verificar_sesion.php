<?php
session_start();
header('Content-Type: application/json');

// Verificar si hay una sesión activa
$sesion_activa = isset($_SESSION['id_cliente']) && !empty($_SESSION['id_cliente']);

echo json_encode([
    'sesion_activa' => $sesion_activa,
    'usuario' => $sesion_activa ? $_SESSION['nombre'] : null
]);
?>