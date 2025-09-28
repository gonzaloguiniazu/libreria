<?php
// conexion.php
function conectarDB() {
    $servername = "localhost";
    $username = "root"; // Cambia esto si usas un usuario diferente
    $password = ""; // Cambia esto si usas una contraseña
    $dbname = "libreria"; // Nombre de tu base de datos

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Error en la conexión: " . $conn->connect_error);
    }

    return $conn; // Asegúrate de retornar la conexión
}

