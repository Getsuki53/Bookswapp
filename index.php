<?php
// Conectar a la base de datos
include('db.php');

// Verificar si el usuario ya está autenticado (por ejemplo, si ya está logueado)
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: home.php"); // Si el usuario está logueado, lo redirigimos a la página principal
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSwap</title>
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
        <div class="content">
            <div class="text">
                <h1>HOLA, SOMOS <span class="highlight">BOOKSWAP!</span></h1>
                <p>
                    Para amantes de la lectura. Intercambia libros, conecta con lectores afines, 
                    descubre nuevas historias y ayuda al planeta. ¡Encuentra el libro perfecto 
                    para ti con BookSwap!
                </p>
                <div class="buttons">
                    <a href="registro.php" class="btn register">Regístrate aquí</a>
                    <a href="login.php" class="btn login">Inicia sesión</a>
                </div>
            </div>
            <div class="image">
                <img src="12-removebg-preview.png" alt="Libros en una laptop">
            </div>
        </div>
    </main>
</body>
</html>
