<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $correo = $_POST['correo'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $nombre_social = $_POST['nombre_social'];
    $username = $_POST['username'];
    $contrasena = $_POST['password'];
    $imagen = null; // Aquí puedes agregar lógica para manejar imágenes si lo deseas
    $calificacion = 0; // Valor inicial de calificación

    // Preparar la consulta SQL
    $sql = "INSERT INTO Usuario (usu_correo, usu_nombre, usu_apellido, usu_nombre_social, usu_username, usu_imagen, usu_contrasena, usu_calificacion)
            VALUES ('$correo', '$nombre', '$apellido', '$nombre_social', '$username', '$imagen', '$contrasena', '$calificacion')";

    if ($conn->query($sql) === TRUE) {
        echo "Usuario registrado con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>