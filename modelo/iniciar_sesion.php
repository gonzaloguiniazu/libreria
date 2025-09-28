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
            // La contraseña es correcta, inicia la sesión
            $_SESSION['id'] = $user['id']; // Guarda el ID del usuario en la sesión

            if ($user['email'] === 'admin@admin') {
                echo "<script>
                alert('Bienvenido, " . $user['nombre'] . "'); // Usa el nombre del usuario
                window.location.href = 'crud.php'; // Redirige a la página del crud
              </script>";
            } else {
                // Mensaje de bienvenida con JavaScript
                echo "<script>
                alert('Bienvenido, " . $user['nombre'] . "'); // Usa el nombre del usuario
                window.location.href = 'productos.php'; // Redirige a la página de productos
              </script>";
            }
            exit();
        } else {
            // La contraseña es incorrecta
            echo "<script>
                    alert('Contraseña incorrecta. Intenta de nuevo.');
                    window.location.href = '../vista/iniciar_sesion.html'; // Regresa a la página de inicio de sesión
                  </script>";
        }
    } else {
        // El usuario no existe
        echo "<script>
                alert('Usuario no registrado. Por favor, regístrate primero.');
                window.location.href = '../vista/registro.html'; // Regresa a la página de inicio de sesión
              </script>";
    }
}

// Cierra la conexión
$conn->close();
