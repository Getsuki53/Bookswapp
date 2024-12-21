<?php
    // Inicializamos variables
    $correo = "";
    $errorCorreo = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $correo = trim($_POST['correo']); // Capturamos el valor del correo

        // Función para validar el correo
        function validarCorreo($correo) {
            $dominiosReconocidos = [
                'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com',
                'edu.cl', 'gov.cl', 'mil.cl',
                'es', 'cl', 'mx', 'ar', 'uk', 'de',
                'tech', 'dev', 'store', 'ai'
            ];

            // Verificar que haya un solo '@'
            $partes = explode('@', $correo);
            if (count($partes) !== 2) {
                return "El correo debe tener un solo '@'.";
            }

            // Verificar que el dominio sea reconocido
            $dominio = $partes[1];
            if (!in_array($dominio, $dominiosReconocidos)) {
                return "El dominio '$dominio' no está en la lista reconocida.";
            }

            return true; // Si pasa todas las validaciones
        }

        // Validamos el correo
        $resultadoValidacion = validarCorreo($correo);
        if ($resultadoValidacion !== true) {
            $errorCorreo = $resultadoValidacion; // Guardamos el mensaje de error
        }

        // Si no hay errores, procesamos el registro
        if (empty($errorCorreo)) {
            echo "<p>Formulario enviado correctamente. El correo ingresado es: <strong>" . htmlspecialchars($correo) . "</strong></p>";
            exit; // Detenemos el script después de mostrar el mensaje
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Regístrate</title>
        <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/styles.css">
    </head>
    <body>
        <header>
            <img src="../assets/images/BookSwap-removebg-preview.png" alt="Logo" class="logo-img">
        </header>
        <main>
            <div class="content">
                <h1>Regístrate en BookSwap</h1>
                <form action="../process/register_process.php" method="POST" enctype="multipart/form-data">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Juan" required>
                    <br>

                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Pérez" required>
                    <br>
                    
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" placeholder="Correo@ejemplo.com" class="<?php echo !empty($errorCorreo) ? 'campo-error' : ''; ?>" required>
                    <?php if (!empty($errorCorreo)): ?>
                        <p class="mensaje-error"><?php echo $errorCorreo; ?></p>
                    <?php endif; ?>
                    <br>

                    <label for="username">Nombre de usuario</label>
                    <input type="text" id="username" name="username" placeholder="TuUsuario" required>
                    <br>
                    
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                    <br>
                    
                    <label for="nombre_social">Nombre Social (opcional)</label>
                    <input type="text" id="nombre_social" name="nombre_social" placeholder="Tu Nombre Social">
                    <br>

                    <?php
                    // Incluir la conexión a la base de datos
                    include('../../config/db.php');

                    // Realizar la consulta para obtener todas las comunas
                    $sql_comunas = "SELECT * FROM `Comuna`";
                    $result_comunas = $conn->query($sql_comunas);
                    ?>

                    <label for="comuna">Comuna</label>
                    <select name="comuna" required>
                        <option value="">Selecciona una comuna</option>
                        <?php
                        // Verificar si hay comunas en la base de datos
                        if ($result_comunas->num_rows > 0) {
                            // Iterar a través de las comunas y crear una opción para cada una
                            while ($row = $result_comunas->fetch_assoc()) {
                                // Usar 'Com_nom' como valor, ya que es único
                                echo '<option value="' . $row['Com_nom'] . '">' . $row['Com_nom'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No hay comunas disponibles</option>';
                        }
                        ?>
                    </select>
                    <br>

                    <label for="calificacion">Calificación</label>
                    <input type="number" name="calificacion" id="calificacion" placeholder="Calificación" min="1" max="5" step="1" required>
                    <br>

                    <label for="imagen">Imagen</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*">
                    <br>

                    <button type="submit">Registrar</button>
                </form>
            
                <?php
                // Cerrar conexión
                $conn->close();
                ?>
                <p>¿Ya tienes una cuenta? <a href="login.php"><i>Inicia sesión aquí</i></a></p>
            </div>
        </main>
    </body>
</html>
