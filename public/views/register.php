<?php
    // Inicializamos variables
    $nombre = $apellido = $correo = $username = $password = $nombre_social = $comuna = $calificacion = "";
    $errorCorreo = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Capturamos los datos enviados por el formulario
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo = trim($_POST['correo']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $nombre_social = trim($_POST['nombre_social']);
        $comuna = trim($_POST['comuna']);
        $calificacion = trim($_POST['calificacion']);

        // Validación del correo
        function validarCorreo($correo) {
            $dominiosReconocidos = [
                'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com',
                'edu.cl', 'gov.cl', 'mil.cl',
                'es', 'cl', 'mx', 'ar', 'uk', 'de',
                'tech', 'dev', 'store', 'ai'
            ];
            $partes = explode('@', $correo);
            if (count($partes) !== 2) {
                return "El correo debe tener un solo '@'.";
            }
            $dominio = $partes[1];
            if (!in_array($dominio, $dominiosReconocidos)) {
                return "El dominio '$dominio' no está en la lista reconocida.";
            }
            return true;
        }

        // Validaciones individuales
        if (empty($correo)) {
            $errorCorreo = "El correo es obligatorio.";
        } else {
            $resultadoValidacion = validarCorreo($correo);
            if ($resultadoValidacion !== true) {
                $errorCorreo = $resultadoValidacion;
            }
        }

        // Aquí podrías agregar validaciones adicionales para otros campos si es necesario

        // Si no hay errores, procesamos el registro
        if (empty($errorCorreo)) {
            echo "<p style='color: green;'>Registro exitoso. Bienvenido, " . htmlspecialchars($nombre) . " " . htmlspecialchars($apellido) . ".</p>";
            exit;
        }
    }
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro - BookSwap</title>
        <link rel="stylesheet" href="../assets/css/registro.css">
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
        <div class="container">
            <div class="form-container">
                <div class="register-box">
                    <h2>Regístrate en BookSwap</h2>
                    <!-- Contenido adicional -->
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="column">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Juan" value="<?php echo htmlspecialchars($nombre); ?>" required>
                        </div>
                        <div class="column">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" placeholder="Pérez" value="<?php echo htmlspecialchars($apellido); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <label for="correo">Correo</label>
                            <input type="email" id="correo" name="correo" placeholder="Correo@ejemplo.com" value="<?php echo htmlspecialchars($correo); ?>" class="<?php echo !empty($errorCorreo) ? 'campo-error' : ''; ?>" required>
                            <?php if (!empty($errorCorreo)): ?>
                                <p class="mensaje-error"><?php echo $errorCorreo; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="column">
                            <label for="username">Nombre de usuario</label>
                            <input type="text" id="username" name="username" placeholder="TuUsuario" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="column">
                            <label for="nombre_social">Nombre Social (opcional)</label>
                            <input type="text" id="nombre_social" name="nombre_social" placeholder="Tu Nombre Social" value="<?php echo htmlspecialchars($nombre_social); ?>" >
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <label for="comuna">Comuna</label>
                            <select name="comuna" id="comuna" required>
                                <option value="">Selecciona una comuna</option>
                                <?php
                                include('../../config/db.php');
                                $sql_comunas = "SELECT * FROM `Comuna`";
                                $result_comunas = $conn->query($sql_comunas);
                                if ($result_comunas->num_rows > 0) {
                                    while ($row = $result_comunas->fetch_assoc()) {
                                        echo '<option value="' . $row['Com_nom'] . '">' . $row['Com_nom'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay comunas disponibles</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="column">
                            <label for="calificacion">Calificación</label>
                            <input type="number" name="calificacion" id="calificacion" placeholder="Calificación" min="1" max="5" step="1" value="<?php echo htmlspecialchars($calificacion); ?>" required>
                        </div>

                        <div class="row">
                            <div class="column">
                                <label for="imagen">Imagen</label>
                                <input type="file" name="imagen" id="imagen" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column full-width">
                            <button type="submit" class="btn-submit">Registrar</button>
                        </div>
                    </div>
                </form>
                <div class="form-footer">
                    ¿Ya tienes una cuenta? <a href="login.php"><i>Inicia sesión aquí</i></a>
                </div>
            </div>
        </div>
    </body>
</html>
