<?php
// verificar_admin.php
// ====================================
// VALIDACIÓN DE SESIÓN CENTRALIZADA
// ====================================

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario esté logueado y sea administrador
 * Si no cumple las condiciones, redirige automáticamente
 */
function verificarAdmin() {
    // Verificar que el usuario esté logueado
    if (!isset($_SESSION['id_cliente'])) {
        header("Location: ../vista/iniciar_sesion.html");
        exit();
    }

    // Verificar que sea el administrador
    if ($_SESSION['email'] !== 'admin@admin') {
        echo "<script>
            alert('No tienes permisos para acceder a esta página.');
            window.location.href = 'productos.php';
        </script>";
        exit();
    }
}

/**
 * Verifica que el usuario esté logueado (sin validar si es admin)
 */
function verificarSesion() {
    if (!isset($_SESSION['id_cliente'])) {
        header("Location: ../vista/iniciar_sesion.html");
        exit();
    }
}

/**
 * Obtiene los datos del usuario actual
 */
function obtenerUsuarioActual() {
    if (!isset($_SESSION['id_cliente'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['id_cliente'],
        'nombre' => $_SESSION['nombre'] ?? '',
        'apellido' => $_SESSION['apellido'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'domicilio' => $_SESSION['domicilio'] ?? '',
        'nombre_completo' => ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '')
    ];
}

/**
 * Verifica si el usuario actual es administrador
 */
function esAdmin() {
    return isset($_SESSION['email']) && $_SESSION['email'] === 'admin@admin';
}

// Ejecutar la verificación automáticamente al incluir el archivo
verificarAdmin();
?>