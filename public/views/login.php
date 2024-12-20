<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inicia Sesión</title>
        <!-- Fuente Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
        <!-- Hoja de estilos -->
        <link rel="stylesheet" href="../assets/css/styles.css">
    </head>
    <body>
        <!-- Encabezado -->
        <header>
            <div class="logo">
                <a href="index.php">
                    <img src="../assets/images/BookSwap-removebg-preview.png" alt="Logo de BookSwap" class="logo-img">
                </a>
            </div>
        </header>

        <!-- Contenido principal -->
        <main>
            <div class="form-container">
                <h2>Ingresa a tu cuenta</h2>
                <form action="../process/login_process.php" method="POST">
                    <!-- Campo para correo -->
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" placeholder="tucorreo@gmail.com" required>

                    <!-- Campo para contraseña -->
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="********" required>

                    <!-- Botón de envío -->
                    <button type="submit">Inicia Sesión</button>
                </form>
                <p>¿No tienes una cuenta? <a href="register.php"><i>Regístrate aquí</i></a></p>
            </div>
        </main>

        <!-- Pie de página -->
        <footer>
            <p>&copy; 2024 BookSwap. Todos los derechos reservados.</p>
        </footer>
    </body>
</html>
