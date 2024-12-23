<?php
// filepath: /c:/xampp/htdocs/Bookswap/solicitar_libro.php

// Iniciar sesión y conectar a la base de datos
session_start();
include('db.php'); // Archivo de conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

$usu_correo = $_SESSION['usu_correo']; // Correo del usuario autenticado
$inter_id = $_POST['inter_id']; // ID de intercambio pasado como parámetro en el formulario

// Obtener el correo del usuario que solicitó el libro en primer lugar
$sql_solicitante = "SELECT Lecin_usu_mail FROM lector_intercambio WHERE Lecin_intercambio_id = ?";
$stmt_solicitante = $conn->prepare($sql_solicitante);
$stmt_solicitante->bind_param("i", $inter_id);
$stmt_solicitante->execute();
$result_solicitante = $stmt_solicitante->get_result();
$row_solicitante = $result_solicitante->fetch_assoc();
$solicitante_correo = $row_solicitante['Lecin_usu_mail'];

// Obtener los libros del usuario que solicitó el libro
$sql_libros = "SELECT * FROM libro WHERE Lib_usu_correo = ?";
$stmt_libros = $conn->prepare($sql_libros);
$stmt_libros->bind_param("s", $solicitante_correo);
$stmt_libros->execute();
$result_libros = $stmt_libros->get_result();
$libros = $result_libros->fetch_all(MYSQLI_ASSOC);

// Manejar solicitud de intercambio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lib_cod'])) {
    $lib_cod = $_POST['lib_cod'];

    // Verificar si el libro pertenece al usuario conectado
    $sql_verificar_propietario = "SELECT Lib_Usu_correo FROM libro WHERE Lib_cod = ?";
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
            $new_inter_id = $conn->insert_id;

            // Crear un nuevo registro en la tabla lector_intercambio
            $sql_lector_intercambio = "INSERT INTO lector_intercambio (Lecin_usu_mail, Lecin_intercambio_id) VALUES (?, ?)";
            $stmt_lector_intercambio = $conn->prepare($sql_lector_intercambio);
            $stmt_lector_intercambio->bind_param("si", $usu_correo, $new_inter_id);
            $stmt_lector_intercambio->execute();

            // Insertar un nuevo registro en la tabla intercambio_estado
            $sql_estado = "INSERT INTO intercambio_estado (Ines_id, ines_nom) VALUES (?, 'Solicitado')";
            $stmt_estado = $conn->prepare($sql_estado);
            $stmt_estado->bind_param("i", $new_inter_id);
            $stmt_estado->execute();

            // Actualizar el estado del intercambio original a "En proceso"
            $sql_actualizar_estado = "UPDATE intercambio_estado SET ines_nom = 'En proceso' WHERE Ines_id = ?";
            $stmt_actualizar_estado = $conn->prepare($sql_actualizar_estado);
            $stmt_actualizar_estado->bind_param("i", $inter_id);
            $stmt_actualizar_estado->execute();

            // Mostrar mensaje de confirmación
            echo "<script>alert('Intercambio solicitado exitosamente.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Libro - BookSwap</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">BookSwap</div>
        <div class="icons">
            <img src="mail_icon.png" alt="Mail">
            <img src="user_icon.png" alt="User">
        </div>
    </header>

    <div class="container">
        <h2>Libros del usuario que solicitó tu libro</h2>
        <section class="main-content">
            <?php if (count($libros) > 0): ?>
                <?php foreach ($libros as $libro): ?>
                    <div class='card'>
                        <?php if (!empty($libro['Lib_imagen'])): ?>
                            <img src="<?php echo $libro['Lib_imagen']; ?>" alt="Imagen del libro" class="book-img" />
                        <?php else: ?>
                            <img src="placeholder.jpg" alt="Imagen no disponible" class="book-img" />
                        <?php endif; ?>
                        <h3 class='book-title'><?php echo htmlspecialchars($libro['Lib_nom']); ?></h3>
                        <p class='book-uploader'>Subido por <?php echo htmlspecialchars($solicitante_correo); ?></p>
                        <form method='POST' action='solicitar_libro.php' class='form'>
                            <input type='hidden' name='lib_cod' value='<?php echo $libro['Lib_cod']; ?>'>
                            <input type='hidden' name='inter_id' value='<?php echo $inter_id; ?>'>
                            <button type='submit' class='btn'>Solicitar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay libros disponibles.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>