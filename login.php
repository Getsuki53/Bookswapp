<?php
// Iniciar sesión y conectar a la base de datos
session_start();
include('db.php'); // Archivo de conexión a la base de datos

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['password'];

    // Consulta para obtener el hash de la contraseña
    $sql = $conn->prepare("SELECT usu_pass FROM Usuario WHERE usu_mail = ?");
    $sql->bind_param("s", $correo);
    $sql->execute();
    $result = $sql->get_result();


    echo password_hash("hola123", PASSWORD_DEFAULT);


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hash_almacenado = $row['usu_pass'];

        // Verifica la contraseña
        if (password_verify($contrasena, $hash_almacenado)) {
            echo "Inicio de sesión exitoso";
            // Redirige a la página principal
            header("Location: home.php");
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
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
        <div class="form-container">
            <h2>Ingresa a tu cuenta</h2>
            <form action="login.php" method="POST">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" placeholder="tucorreo@gmail.com" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="********" required>

                <button type="submit">Inicia Sesión</button>
            </form>
            <p>¿No tienes una cuenta? <a href="register.php"><i>Regístrate aquí</i></a></p>
        </div>
