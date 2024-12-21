<?php
// Inicializar variable para saber si el campo de correo es inválido
$email_error = false;

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $correo = $_POST['correo'] ?? '';

    // Validar el correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $email_error = true;  // Marcar como error si el correo es inválido
    }

    // Aquí puedes continuar con la lógica de validación y procesamiento del formulario
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
                    
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Pérez" required>
                    
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" value="<?php echo $correo; ?>" 
                    class="<?php echo $email_error ? 'invalid' : ''; ?>">
                    
                    <?php if ($email_error): ?>
                        <p style="color: red;">Por favor, ingresa un correo válido.</p>
                    <?php endif; ?>

                    <label for="username">Nombre de usuario</label>
                    <input type="text" id="username" name="username" placeholder="TuUsuario" required>
                    
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                    
                    <label for="nombre_social">Nombre Social (opcional)</label>
                    <input type="text" id="nombre_social" name="nombre_social" placeholder="Tu Nombre Social">

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

                    <label for="calificacion">Calificación</label>
                    <input type="number" name="calificacion" id="calificacion" placeholder="Calificación" min="1" max="5" step="1" required>

                    <label for="imagen">Imagen</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*">

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
