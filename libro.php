<?php
// Inicia sesión y conecta a la base de datos
session_start();
include('db.php');

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $username = $_POST['username'];
    $comuna = $_POST['comuna'];
    $contrasena = $_POST['password'];

    // Elimina la encriptación de la contraseña
    // $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Inserta en la tabla Usuario
    $sql_libro = "INSERT INTO Libro (usu_mail, usu_pass, T_usuario, Oculto)
                    VALUES ('$correo', '$contrasena', 'Lector', 1)";
    if ($conn->query($sql_usuario) === TRUE) {
        // Inserta en la tabla Lector
        $sql_lector = "INSERT INTO Lector (usu_nom, usu_apellido, usu_mail, usu_username, usu_comuna)
                       VALUES ('$nombre', '$apellido', '$correo', '$username', '$comuna')";
        if ($conn->query($sql_lector) === TRUE) {
            // Redirige al login
            header("Location: login.php");
            exit();
        } else {
            echo "Error al registrar el usuario en Lector: " . $conn->error;
        }
    } else {
        echo "Error al registrar el usuario en Usuario: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Libro</title>
</head>
<body>
    <form action="libro.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>
        </div>
        <div>
            <label for="nombre_autor">Nombre del Autor:</label>
            <input type="text" id="nombre_autor" name="nombre_autor" required>
        </div>
        <div>
            <label for="apellido_autor">Apellido del Autor:</label>
            <input type="text" id="apellido_autor" name="apellido_autor" required>
        </div>
        <div>
            <label for="editorial">Editorial:</label>
            <input type="text" id="editorial" name="editorial" required>
        </div>
        <div>
            <label for="idioma">Idioma:</label>
            <input type="text" id="idioma" name="idioma" required>
        </div>
        <div>
            <label for="etiqueta">Etiqueta:</label>
            <input type="text" id="etiqueta" name="etiqueta" required>
        </div>
        <div>
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>
                <option value="1">1 - Nuevo</option>
                <option value="2">2 - Muy bueno</option>
                <option value="3">3 - Bueno</option>
                <option value="4">4 - Regular</option>
                <option value="5">5 - Dañado</option>
            </select>
        </div>
        <div>
            <label for="valorEstimado">Valor Estimado:</label>
            <input type="number" id="valorEstimado" name="valorEstimado" required>
        </div>
        <div>
            <label for="imagen">Cargar Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>
        </div>
        <div>
            <button type="submit">Registrar Libro</button>
        </div>
    </form>
</body>
</html>