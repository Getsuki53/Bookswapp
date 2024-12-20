<?php
$servername = "localhost";
$username = "root"; // El usuario predeterminado en XAMPP
$password = ""; // La contraseña predeterminada en XAMPP es vacía
$dbname = "tu_base_de_datos"; // Cambia por el nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>