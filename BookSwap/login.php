<?php
// Iniciar sesión y conectar a la base de datos
session_start();
include('db.php'); // Archivo de conexión a la base de datos

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Consultar si el correo existe en la base de datos
    $sql = "SELECT * FROM Usuario WHERE usu_correo = '$correo'";
    $result = $conn->query($sql);

    // Si el correo existe
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($password, $usuario['usu_contrasena'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['usuario_id'] = $usuario['usu_id']; // Guardar el ID del usuario
            $_SESSION['usuario_nombre'] = $usuario['usu_nombre']; // Guardar el nombre
            header("Location: index.php"); // Redirigir a la página principal
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "No se encontró el usuario con ese correo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicia Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="logo.png" alt="Logo" class="logo-img">
    </header>
    <main>
        <div class="form-container">
            <h2>Ingresa a tu cuenta</h2>
            <!-- Acción al archivo PHP -->
            <form action="login.php" method="POST">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" placeholder="tucorreo@gmail.com" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="********" required>

                <button type="submit">Inicia Sesión</button>
            </form>
            <p>¿No tienes una cuenta? <a href="register.php"><i>Regístrate aquí</i></a></p>
        </div>
    </main>
</body>
</html>
