<?php
// filepath: /c:/xampp/htdocs/Bookswap/homee.php

// Iniciar sesión y conectar a la base de datos
session_start();
include('db.php'); // Archivo de conexión a la base de datos
include('buscarLibro.php'); // Incluir el archivo de búsqueda de libros

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

$usu_correo = $_SESSION['usu_correo']; // Correo del usuario autenticado

// Obtener las notificaciones no leídas
$sql_notificaciones = "SELECT * FROM Notificaciones WHERE Not_usu_id = ? AND Not_leido = 0";
$stmt_notificaciones = $conn->prepare($sql_notificaciones);
$stmt_notificaciones->bind_param("s", $usu_correo);
$stmt_notificaciones->execute();
$result_notificaciones = $stmt_notificaciones->get_result();
$notificaciones = $result_notificaciones->fetch_all(MYSQLI_ASSOC);

// Manejar solicitud de intercambio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lib_cod'])) {
    $lib_cod = $_POST['lib_cod'];

    // Verificar si el libro pertenece al usuario conectado
    $sql_verificar_propietario = "SELECT Lec_mail FROM libro WHERE Lib_cod = ?";
    $stmt_verificar_propietario = $conn->prepare($sql_verificar_propietario);
    $stmt_verificar_propietario->bind_param("i", $lib_cod);
    $stmt_verificar_propietario->execute();
    $result_verificar_propietario = $stmt_verificar_propietario->get_result();
    $row_propietario = $result_verificar_propietario->fetch_assoc();

    if ($row_propietario['Lec_mail'] == $usu_correo) {
        // El libro pertenece al usuario conectado
        echo "<script>alert('No puedes solicitar tu propio libro.');</script>";
    } else {
        // Verificar si el usuario ya tiene un intercambio relacionado
        $sql_verificar = "SELECT Lecin_intercambio_id FROM lector_intercambio WHERE Lecin_usu_mail = ? AND Lecin_intercambio_id IN (SELECT Inter_id FROM intercambio WHERE Lib_cod = ?)";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("si", $usu_correo, $lib_cod);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {
            // El libro ya fue solicitado por este usuario
            echo "<script>alert('El libro ya fue solicitado por este usuario.');</script>";
        } else {
            // Insertar un nuevo registro en la tabla intercambio
            $sql_intercambio = "INSERT INTO intercambio (Lib_cod) VALUES (?)";
            $stmt_intercambio = $conn->prepare($sql_intercambio);
            $stmt_intercambio->bind_param("i", $lib_cod);
            $stmt_intercambio->execute();
            $inter_id = $stmt_intercambio->insert_id;

            // Crear un nuevo registro en la tabla lector_intercambio
            $sql_lector_intercambio = "INSERT INTO lector_intercambio (Lecin_usu_mail, Lecin_intercambio_id) VALUES (?, ?)";
            $stmt_lector_intercambio = $conn->prepare($sql_lector_intercambio);
            $stmt_lector_intercambio->bind_param("si", $usu_correo, $inter_id);
            $stmt_lector_intercambio->execute();

            // Insertar un nuevo registro en la tabla intercambio_estado
            $sql_estado = "INSERT INTO intercambio_estado (Ines_id, ines_nom) VALUES (?, 'Solicitado')";
            $stmt_estado = $conn->prepare($sql_estado);
            $stmt_estado->bind_param("i", $inter_id);
            $stmt_estado->execute();

            // Crear una notificación para el propietario del libro
            $sql_notificacion = "INSERT INTO Notificaciones (Not_usu_id, Not_mensaje, Not_inter_id) VALUES (?, ?, ?)";
            $mensaje = "Alguien ha solicitado tu libro.";
            $stmt_notificacion = $conn->prepare($sql_notificacion);
            $stmt_notificacion->bind_param("ssi", $row_propietario['Lec_mail'], $mensaje, $inter_id);
            $stmt_notificacion->execute();

            // Mostrar mensaje de confirmación
            echo "<script>alert('Intercambio solicitado exitosamente.');</script>";
        }
    }
}

// Manejar búsqueda de libros
$titulo = $_GET['titulo'] ?? '';
$libros = buscarLibro($conn, $titulo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSwap</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">BookSwap</div>
        <div class="icons">
            <img src="mail_icon.png" alt="Mail">
            <img src="user_icon.png" alt="User">
            <button id="notification-button">Notificaciones (<?php echo count($notificaciones); ?>)</button>
        </div>
    </header>

    <div id="notification-panel" style="display: none;">
        <?php foreach ($notificaciones as $notificacion): ?>
            <div class="notification">
                <p><?php echo htmlspecialchars($notificacion['Not_mensaje']); ?></p>
                <form method="POST" action="solicitar_libro.php">
                    <input type="hidden" name="inter_id" value="<?php echo $notificacion['Not_inter_id']; ?>">
                    <button type="submit">Solicitar libro</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="search-bar">
        <form method="GET" action="homee.php">
            <input type="text" name="titulo" placeholder="Buscar...">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <div class="container">
        <aside class="sidebar">
            <h2>RESULTADOS DE BÚSQUEDA</h2>
            <div class="tags">
                <label><input type="checkbox" checked> Romance</label>
                <label><input type="checkbox" checked> Ficción</label>
                <label><input type="checkbox"> Terror</label>
                <label><input type="checkbox" checked> Fantasía</label>
                <label><input type="checkbox"> Histórico</label>
                <label><input type="checkbox"> Ciencia ficción</label>
            </div>
            <div class="buttons">
                <button>Buscar por libro</button>
                <button>Buscar por usuario</button>
            </div>
            <p>¿Quieres guardar un libro para después? Márcalo con un ❤️ para guardarlo en tu lista de deseos.</p>
        </aside>

        <section class="main-content">
            <?php if (count($libros) > 0): ?>
                <?php foreach ($libros as $row): ?>
                    <div class='card'>
                        <?php if (!empty($row['Lib_imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($row['Lib_imagen']); ?>" alt="Portada del libro">
                        <?php else: ?>
                            <img src="default_book.png" alt="Imagen no disponible">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($row['Lib_nom']); ?></h3>
                        <p class='book-uploader'>Subido por <?php echo htmlspecialchars($row['Lec_mail']); ?></p>
                        <form method='POST' action='homee.php' class='form'>
                            <input type='hidden' name='lib_cod' value='<?php echo $row['Lib_cod']; ?>'>
                            <button type='submit' class='btn'>Solicitar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay libros disponibles.</p>
            <?php endif; ?>
        </section>
    </div>

    <script>
        document.getElementById('notification-button').addEventListener('click', function() {
            var panel = document.getElementById('notification-panel');
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        });
    </script>
</body>
</html>