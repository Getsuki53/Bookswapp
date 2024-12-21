<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "bookswap");

// Manejar errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar operaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear']) && !empty($_POST['correo'])) {
        $correo = $conexion->real_escape_string($_POST['correo']);
        
        // Verificar si el moderador ya existe
        $resultado = $conexion->query("SELECT * FROM moderador WHERE Mod_mail = '$correo'");
        if ($resultado->num_rows > 0) {
            // Si existe, verificar si está oculto
            $fila = $resultado->fetch_assoc();
            if ($fila['Oculto'] == 0) {
                // Si está oculto, actualizar su estado a visible
                $conexion->query("UPDATE moderador SET Oculto = 1 WHERE Mod_mail = '$correo'");
            } else {
                // Si ya está visible, mostrar un mensaje de error
                echo "El moderador ya existe y está visible.";
            }
        } else {
            // Si no existe, insertar nuevo moderador con estado visible (Oculto = 1)
            $conexion->query("INSERT INTO moderador (Mod_mail, Oculto) VALUES ('$correo', 1)");
        }
    } elseif (isset($_POST['editar']) && !empty($_POST['correo_original']) && !empty($_POST['correo'])) {
        $correo_original = $conexion->real_escape_string($_POST['correo_original']);
        $correo = $conexion->real_escape_string($_POST['correo']);
        // Actualizar correo de moderador
        $conexion->query("UPDATE moderador SET Mod_mail = '$correo' WHERE Mod_mail = '$correo_original'");
    } elseif (isset($_POST['eliminar']) && !empty($_POST['correo'])) {
        $correo = $conexion->real_escape_string($_POST['correo']);
        // Cambiar estado a oculto (Oculto = 0) en lugar de eliminar
        $conexion->query("UPDATE moderador SET Oculto = 0 WHERE Mod_mail = '$correo'");
    }
}

// Obtener moderadores visibles (Oculto = 1)
$resultado = $conexion->query("SELECT * FROM moderador WHERE Oculto = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Moderadores</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="images/BookSwap-removebg-preview.png" alt="BookSwap Logo" class="logo">
        </div>
        <nav>
            <button onclick="window.location.href='mantenedor_principal.php'">Volver al Mantenedor Principal</button>
        </nav>
    </div>
    <div class="main-content">
        <h2>Mantenedor de Moderadores</h2>
        <form action="mantenedor_moderadores.php" method="POST">
            <input type="email" name="correo" placeholder="Correo del moderador" required>
            <button type="submit" name="crear">Crear</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['Mod_mail']) ?></td>
                        <td>
                            <form action="mantenedor_moderadores.php" method="POST" style="display:inline;">
                                <input type="hidden" name="correo_original" value="<?= htmlspecialchars($fila['Mod_mail']) ?>">
                                <input type="email" name="correo" value="<?= htmlspecialchars($fila['Mod_mail']) ?>" required>
                                <button type="submit" name="editar">Editar</button>
                            </form>
                            <form action="mantenedor_moderadores.php" method="POST" style="display:inline;">
                                <input type="hidden" name="correo" value="<?= htmlspecialchars($fila['Mod_mail']) ?>">
                                <button type="submit" name="eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
