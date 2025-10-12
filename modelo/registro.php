<?php
// registro.php
include('conexion.php'); 

// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar a la base de datos
$conn = conectarDB(); // Llama a la función para obtener la conexión

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$domicilio = $_POST['domicilio'];
$email = $_POST['email'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Cambia 'password' a 'contrasena'

// Verificar si el email ya existe
$checkEmail = $conn->prepare("SELECT email FROM cliente WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$result = $checkEmail->get_result();

if ($result->num_rows > 0) {
    echo "El email ya está registrado. Por favor, utiliza otro.";
    exit();
}

// Preparar la consulta SQL
$sql = "INSERT INTO cliente (nombre, apellido, domicilio, email, contrasena) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nombre, $apellido, $domicilio, $email, $contrasena);

// Ejecutar la consulta y verificar si se realizó con éxito
if ($stmt->execute()) {
    header("Location: ../vista/registro.html?message=Registro exitoso");
    exit();
} else {
    echo "Error: " . $stmt->error; // Muestra el error si ocurre
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
