<?php
// Función para crear un tipo de usuario
function crearTipoUsuario($conexion, $nombre) {
    $nombre = $conexion->real_escape_string($nombre);

    // Verificar si el tipo de usuario ya existe
    $resultado = $conexion->query("SELECT * FROM t_usuario WHERE Tus_Tipo_usu = '$nombre'");
    if ($resultado->num_rows > 0) {
        // Si existe, verificar si está oculto
        $fila = $resultado->fetch_assoc();
        if ($fila['Oculto'] == 0) {
            // Si está oculto, actualizar su estado a visible
            $conexion->query("UPDATE t_usuario SET Oculto = 1 WHERE Tus_Tipo_usu = '$nombre'");
        } else {
            // Si ya está visible, mostrar un mensaje de error
            echo "El tipo de usuario ya existe y está visible.";
        }
    } else {
        // Si no existe, insertar nuevo tipo de usuario con estado visible (Oculto = 1)
        $conexion->query("INSERT INTO t_usuario (Tus_Tipo_usu, Oculto) VALUES ('$nombre', 1)");
    }
}

// Función para editar un tipo de usuario
function editarTipoUsuario($conexion, $nombre_original, $nombre) {
    $nombre_original = $conexion->real_escape_string($nombre_original);
    $nombre = $conexion->real_escape_string($nombre);
    
    // Actualizar nombre de tipo de usuario
    $conexion->query("UPDATE t_usuario SET Tus_Tipo_usu = '$nombre' WHERE Tus_Tipo_usu = '$nombre_original'");
}

// Función para eliminar un tipo de usuario (marcarlo como oculto)
function eliminarTipoUsuario($conexion, $nombre) {
    $nombre = $conexion->real_escape_string($nombre);
    
    // Cambiar estado a oculto (Oculto = 0) en lugar de eliminar
    $conexion->query("UPDATE t_usuario SET Oculto = 0 WHERE Tus_Tipo_usu = '$nombre'");
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "Patita05$", "bookswap");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar operaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear']) && !empty($_POST['nombre'])) {
        crearTipoUsuario($conexion, $_POST['nombre']);
    } elseif (isset($_POST['editar']) && !empty($_POST['nombre_original']) && !empty($_POST['nombre'])) {
        editarTipoUsuario($conexion, $_POST['nombre_original'], $_POST['nombre']);
    } elseif (isset($_POST['eliminar']) && !empty($_POST['nombre'])) {
        eliminarTipoUsuario($conexion, $_POST['nombre']);
    }
}

// Obtener tipos de usuario visibles (Oculto = 1)
$resultado = $conexion->query("SELECT * FROM t_usuario WHERE Oculto = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Tipos de Usuarios</title>
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
            <h2>Mantenedor de Tipos de Usuarios</h2>
        </header>
        <!-- Contenido del mantenedor de tipos de usuarios -->
        <form action="mantenedor_tusuarios.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre del tipo de usuario" required>
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
                        <td><?= htmlspecialchars($fila['Tus_Tipo_usu']) ?></td>
                        <td>
                            <form action="mantenedor_tusuarios.php" method="POST" style="display:inline;">
                                <input type="hidden" name="nombre_original" value="<?= htmlspecialchars($fila['Tus_Tipo_usu']) ?>">
                                <input type="text" name="nombre" value="<?= htmlspecialchars($fila['Tus_Tipo_usu']) ?>" required>
                                <button type="submit" name="editar">Editar</button>
                            </form>
                            <form action="mantenedor_tusuarios.php" method="POST" style="display:inline;">
                                <input type="hidden" name="nombre" value="<?= htmlspecialchars($fila['Tus_Tipo_usu']) ?>">
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
