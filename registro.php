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
    $sql_usuario = "INSERT INTO Usuario (usu_mail, usu_pass, T_usuario, Oculto)
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
    <title>Regístrate - BookSwap</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <div class="logo">
            <a href="index.php">
                <img src="BookSwap-removebg-preview.png" alt="Logo de BookSwap" class="logo-img">
            </a>
        </div>
        <div class="profile-icon">👤</div>
    </header>
    
    <!-- Contenido principal -->
    <main>
        <div class="form-container">
            <h2>Regístrate</h2>
            <form action="registro.php" method="POST">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Juan" required>

                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" placeholder="Perez" required>

                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" placeholder="juan.perez@gmail.com" required>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="user123" required>

                <label for="comuna">Comuna</label>
                <input type="text" id="comuna" name="comuna" placeholder="Santiago" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="********" required>

                <button type="submit">Regístrate</button>
            </form>

            <p>¿Ya tienes una cuenta? <a href="login.php"><i>Inicia sesión aquí</i></a></p>
        </div>
    </main>
</body>
</html>