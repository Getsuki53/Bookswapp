<?php
// Función para crear un idioma
function crearIdioma($conexion, $nombre) {
    $nombre = $conexion->real_escape_string($nombre);

    // Verificar si el idioma ya existe
    $resultado = $conexion->query("SELECT * FROM idioma WHERE Idio_nom = '$nombre'");
    if ($resultado->num_rows > 0) {
        // Si existe, verificar si está oculto
        $fila = $resultado->fetch_assoc();
        if ($fila['Oculto'] == 0) {
            // Si está oculto, actualizar su estado a visible
            $conexion->query("UPDATE idioma SET Oculto = 1 WHERE Idio_nom = '$nombre'");
        } else {
            // Si ya está visible, mostrar un mensaje de error
            echo "El idioma ya existe y está visible.";
        }
    } else {
        // Si no existe, insertar nuevo idioma con estado visible (Oculto = 1)
        $conexion->query("INSERT INTO idioma (Idio_nom, Oculto) VALUES ('$nombre', 1)");
    }
}

// Función para editar un idioma
function editarIdioma($conexion, $nombre_original, $nombre) {
    $nombre_original = $conexion->real_escape_string($nombre_original);
    $nombre = $conexion->real_escape_string($nombre);
    
    // Actualizar nombre del idioma
    $conexion->query("UPDATE idioma SET Idio_nom = '$nombre' WHERE Idio_nom = '$nombre_original'");
}

// Función para eliminar un idioma (marcarlo como oculto)
function eliminarIdioma($conexion, $nombre) {
    $nombre = $conexion->real_escape_string($nombre);
    
    // Cambiar estado a oculto (Oculto = 0) en lugar de eliminar
    $conexion->query("UPDATE idioma SET Oculto = 0 WHERE Idio_nom = '$nombre'");
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "Patita05$", "bookswap");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar operaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear']) && !empty($_POST['nombre'])) {
        crearIdioma($conexion, $_POST['nombre']);
    } elseif (isset($_POST['editar']) && !empty($_POST['nombre_original']) && !empty($_POST['nombre'])) {
        editarIdioma($conexion, $_POST['nombre_original'], $_POST['nombre']);
    } elseif (isset($_POST['eliminar']) && !empty($_POST['nombre'])) {
        eliminarIdioma($conexion, $_POST['nombre']);
    }
}

// Obtener idiomas visibles (Oculto = 1)
$resultado = $conexion->query("SELECT * FROM idioma WHERE Oculto = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Idiomas</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="mantenedores.css">
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
        <header class="main-header">
            <h2>Mantenedor de Idiomas</h2>
        </header>
        <!-- Contenido del mantenedor de idiomas -->
        <form action="mantenedor_idiomas.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre del idioma" required>
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
                        <td><?= htmlspecialchars($fila['Idio_nom']) ?></td>
                        <td>
                            <form action="mantenedor_idiomas.php" method="POST" style="display:inline;">
                                <input type="hidden" name="nombre_original" value="<?= htmlspecialchars($fila['Idio_nom']) ?>">
                                <input type="text" name="nombre" value="<?= htmlspecialchars($fila['Idio_nom']) ?>" required>
                                <button type="submit" name="editar">Editar</button>
                            </form>
                            <form action="mantenedor_idiomas.php" method="POST" style="display:inline;">
                                <input type="hidden" name="nombre" value="<?= htmlspecialchars($fila['Idio_nom']) ?>">
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
