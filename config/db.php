<?php

$servername = "localhost";
$username = "yayoh19"; // El usuario predeterminado en XAMPP
$password = "Metano1_Etano2"; // La contraseña predeterminada en XAMPP es vacía
$dbname = "bookswap"; // Cambia por el nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error)
{
    die("Conexión fallida: " . $conn->connect_error);
}
echo "Conexión exitosa";

// Establecer codificación de caracteres
$conn->set_charset("utf8");

?>
