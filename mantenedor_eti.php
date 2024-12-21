<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "Patita05$", "bookswap");

// Manejar errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar operaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear']) && !empty($_POST['nombre'])) {
        $nombre = $conexion->real_escape_string($_POST['nombre']);
        
        // Verificar si la etiqueta ya existe
        $resultado = $conexion->query("SELECT * FROM etiqueta WHERE Etiq_nom = '$nombre'");
        if ($resultado->num_rows > 0) {
            // Si existe, verificar si está oculta
            $fila = $resultado->fetch_assoc();
            if ($fila['Oculto'] == 0) {
                // Si está oculta, actualizar su estado a visible
                $conexion->query("UPDATE etiqueta SET Oculto = 1 WHERE Etiq_nom = '$nombre'");
            } else {
                // Si ya está visible, mostrar un mensaje de error
                echo "La etiqueta ya existe y está visible.";
            }
        } else {
            // Si no existe, insertar nueva etiqueta con estado visible (Oculto = 1)
            $conexion->query("INSERT INTO etiqueta (Etiq_nom, Oculto) VALUES ('$nombre', 1)");
        }
    } elseif (isset($_POST['editar']) && !empty($_POST['nombre_original']) && !empty($_POST['nombre'])) {
        $nombre_original = $conexion->real_escape_string($_POST['nombre_original']);
        $nombre = $conexion->real_escape_string($_POST['nombre']);
        // Actualizar nombre de etiqueta
        $conexion->query("UPDATE etiqueta SET Etiq_nom = '$nombre' WHERE Etiq_nom = '$nombre_original'");
    } elseif (isset($_POST['eliminar']) && !empty($_POST['nombre'])) {
        $nombre = $conexion->real_escape_string($_POST['nombre']);
        // Cambiar estado a oculto (Oculto = 0) en lugar de eliminar
        $conexion->query("UPDATE etiqueta SET Oculto = 0 WHERE Etiq_nom = '$nombre'");
    }
}

// Obtener etiquetas visibles (Oculto = 1)
$resultado = $conexion->query("SELECT * FROM etiqueta WHERE Oculto = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Etiquetas</title>
    <link rel="stylesheet" href="styles.css">
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
        <h2>Mantenedor de Etiquetas</h2>
        <form action="mantenedor_eti.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre de la etiqueta" required>
            <button type="submit" name="crear">Crear</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['Etiq_nom']) ?></td>
                        <td>
                            <form action="mantenedor_eti.php" method="POST" style="display:inline;">
                                <input type="hidden" name="nombre_original" value="<?= htmlspecialchars($fila['Etiq_nom']) ?>">
                                <input type="text" name="nombre" value="<?= htmlspecialchars($fila['Etiq_nom']) ?>" required>
                                <button type="submit" name="editar">Editar</button>
                            </form>
                            <form action="mantenedor_eti.php" method="POST" style="display:inline;">
                                <input type="hidden" name="nombre" value="<?= htmlspecialchars($fila['Etiq_nom']) ?>">
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
