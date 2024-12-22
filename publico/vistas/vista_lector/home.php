<?php
// Iniciar sesión y conectar a la base de datos
include('../../config/db.php'); // Archivo de conexión a la base de datos
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['correo']))
{
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: ../control_acceso/login.php");
    exit();
}

// Manejar solicitud de intercambio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lib_cod']))
{
    $lib_cod = $_POST['lib_cod'];
    $usu_correo = $_SESSION['correo']; // Asumiendo que el correo del usuario está almacenado en la sesión

    // Verificar si el libro pertenece al usuario conectado
    $sql_verificar_propietario = "SELECT Lib_usu_mail FROM libro WHERE Lib_cod = ?";
    $stmt_verificar_propietario = $conn->prepare($sql_verificar_propietario);
    $stmt_verificar_propietario->bind_param("i", $lib_cod);
    $stmt_verificar_propietario->execute();
    $result_verificar_propietario = $stmt_verificar_propietario->get_result();
    $row_propietario = $result_verificar_propietario->fetch_assoc();

    if ($row_propietario['Lib_usu_mail'] == $usu_correo) 
        echo "<script>alert('No puedes solicitar tu propio libro.');</script>"; // El libro pertenece al usuario conectado
    else
    {
        // Verificar si el usuario ya tiene un intercambio relacionado
        $sql_verificar = "SELECT Lecin_intercambio_id FROM lector_intercambio WHERE Lecin_usu_mail = ? AND Lecin_intercambio_id IN (SELECT Inter_id FROM intercambio WHERE Lib_cod = ?)";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("si", $usu_correo, $lib_cod);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0)
            echo "<script>alert('El libro ya fue solicitado por este usuario.');</script>"; // El libro ya fue solicitado por este usuario
        else
        {
            // Insertar un nuevo registro en la tabla intercambio
            $sql_intercambio = "INSERT INTO intercambio (Lib_cod) VALUES (?)";
            $stmt_intercambio = $conn->prepare($sql_intercambio);
            $stmt_intercambio->bind_param("i", $lib_cod);
            $stmt_intercambio->execute();
            $inter_id = $conn->insert_id;

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
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BookSwap</title>
    </head>
    <body>
        <header>
            <link rel="stylesheet" href="../assets/css/home.css">
            <div class="logo">
                <a href="../../index.php">
                    <img src="../../recursos/imagenes/BookSwap-removebg-preview.png" alt="Logo de BookSwap" class="logo-img">
                </a>
            </div>
            <div class="icons">
                <img src="../../recursos/imagenes/mail_icon.png" alt="Mail">
                <img src="../../recursos/imagenes/user_icon.png" alt="User">
            </div>
        </header>

        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
        </div>

        <div class="container">
            <aside class="sidebar">
                <h2>RESULTADOS DE BÚSQUEDA</h2>
                <div class="tags">
                    <label><input type="checkbox"> Romance</label>
                    <label><input type="checkbox"> Ficción</label>
                    <label><input type="checkbox"> Terror</label>
                    <label><input type="checkbox"> Fantasía</label>
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
                <?php
                // Consulta para obtener los libros
                $sql = "SELECT Libro.*, Lector.Lec_mail FROM Libro JOIN Lector ON Libro.Lib_usu_mail = Lector.Lec_mail";
                $result = $conn->query($sql);

                if ($result->num_rows > 0)
                {
                    // Mostrar los datos de cada libro
                    while($row = $result->fetch_assoc())
                    {
                        echo "<div class='card'>";
                        // Verificar si la imagen está presente
                        if (!empty($row['Lib_imagen']))
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['Lib_imagen']) . '" alt="Imagen del libro" class="book-img" />';
                        else
                            echo '<img src="placeholder.jpg" alt="Imagen no disponible" class="book-img" />';
                        echo "<h3 class='book-title'>" . $row['Lib_nom'] . "</h3>";
                        echo "<p class='book-uploader'>Subido por " . $row['Lec_mail'] . "</p>";
                        echo "<form method='POST' action='homee.php' class='form'>";
                        echo "<input type='hidden' name='lib_cod' value='" . $row['Lib_cod'] . "'>";
                        echo "<button type='submit' class='btn'>Solicitar</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                }
                else
                    echo "<p>No hay libros disponibles.</p>";
                ?>
            </section>
        </div>
    </body>
</html>
