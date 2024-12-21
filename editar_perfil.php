<?php
session_start(); // Iniciar la sesión

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "bookswap");

// Manejar errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultar información del usuario
if (isset($_SESSION['usuario'])) {
    $lec_mail = $_SESSION['usuario'];
} else {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit;
}

$query_lector = "SELECT * FROM lector WHERE Lec_mail = '$lec_mail'";
$resultado_lector = $conexion->query($query_lector);
$lector = $resultado_lector->fetch_assoc();

// Consultar comunas
$query_comunas = "SELECT * FROM Comuna";
$resultado_comunas = $conexion->query($query_comunas);

// Actualizar información del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $comuna = $_POST['comuna'];
    $username = $_POST['username'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
        $stmt = $conexion->prepare("UPDATE lector SET Lec_nom = ?, Lec_apellido = ?, Lec_comuna = ?, Lec_username = ?, Lec_img = ? WHERE Lec_mail = ?");
        $stmt->bind_param("ssssss", $nombre, $apellido, $comuna, $username, $imagen, $lec_mail);
    } else {
        $stmt = $conexion->prepare("UPDATE lector SET Lec_nom = ?, Lec_apellido = ?, Lec_comuna = ?, Lec_username = ? WHERE Lec_mail = ?");
        $stmt->bind_param("sssss", $nombre, $apellido, $comuna, $username, $lec_mail);
    }

    if ($stmt->execute()) {
        header("Location: perfil.php");
        exit;
    } else {
        echo "Error al actualizar el perfil: " . $stmt->error;
    }

    $stmt->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="images/BookSwap-removebg-preview.png" alt="Logo BookSwap">
    </div>
    <div class="iconos">
        <a href="notificaciones.php"><i class="fas fa-bell"></i></a>
        <a href="buscar.php"><i class="fas fa-search"></i></a>
        <a href="mensajes.php"><i class="fas fa-envelope"></i></a>
    </div>
</header>
<main>
    <div class="editar-perfil">
        <h2>Editar Perfil</h2>
        <form action="editar_perfil.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($lector['Lec_nom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($lector['Lec_apellido']) ?>" required>
            </div>
            <div class="form-group">
                <label for="comuna">Comuna:</label>
                <select name="comuna" id="comuna" required>
                    <option value="">Selecciona una comuna</option>
                    <?php
                    if ($resultado_comunas->num_rows > 0) {
                        while ($row = $resultado_comunas->fetch_assoc()) {
                            $selected = ($row['Com_nom'] == $lector['Lec_comuna']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($row['Com_nom']) . '" ' . $selected . '>' . htmlspecialchars($row['Com_nom']) . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay comunas disponibles</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($lector['Lec_username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="imagen">Foto de Perfil:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
            </div>
            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
    </div>
</main>
</body>
</html>