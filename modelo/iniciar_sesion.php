<?php
session_start();
include('conexion.php');

$conn = conectarDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['contrasena'];

    // Consulta a la base de datos para verificar el usuario
    $sql = "SELECT * FROM cliente WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifica si la contraseña es correcta
        if (password_verify($password, $user['contrasena'])) {
            // ✅ Guardar datos del cliente en la sesión
            $_SESSION['id_cliente'] = $user['id_cliente'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['apellido'] = $user['apellido'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['domicilio'] = $user['domicilio'];

            // Verificar si es administrador
            if ($user['email'] === 'admin@admin') {
                echo "<script>
                    alert('Bienvenido Administrador, " . addslashes($user['nombre']) . "');
                    window.location.href = 'crud.php';
                  </script>";
            } else {
                // ✅ Redirigir a la página DINÁMICA de productos
                echo "<script>
                    alert('Bienvenido, " . addslashes($user['nombre']) . "');
                    window.location.href = 'productos.php';
                  </script>";
            }
            exit();
        } else {
            // Contraseña incorrecta
            echo "<script>
                    alert('Contraseña incorrecta. Intenta de nuevo.');
                    window.location.href = '../vista/iniciar_sesion.html';
                  </script>";
        }
    } else {
        // Usuario no existe
        echo "<script>
                alert('Usuario no registrado. Por favor, regístrate primero.');
                window.location.href = '../vista/registro.html';
              </script>";
    }
}

// Cierra la conexión
$conn->close();
?>