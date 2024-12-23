<?php
// Iniciar sesión y conectar a la base de datos
session_start();
include('db.php'); // Archivo de conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

// Manejar solicitud de intercambio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lib_cod'])) {
    $lib_cod = $_POST['lib_cod'];
    $usu_correo = $_SESSION['usu_correo']; // Asumiendo que el correo del usuario está almacenado en la sesión

// Verificar si el libro pertenece al usuario conectado
$sql_verificar_propietario = "SELECT Lib_usu_correo FROM libro WHERE Lib_cod = ?";
$stmt_verificar_propietario = $conn->prepare($sql_verificar_propietario);
$stmt_verificar_propietario->bind_param("i", $lib_cod);
$stmt_verificar_propietario->execute();
$result_verificar_propietario = $stmt_verificar_propietario->get_result();
$row_propietario = $result_verificar_propietario->fetch_assoc();

if ($row_propietario['Lib_usu_correo'] == $usu_correo) {
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
    <title>BookSwap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fbeee6;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #a83232;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        header .icons {
            display: flex;
            gap: 1rem;
        }

        header .icons img {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .search-bar {
            display: flex;
            justify-content: center;
            margin: 1rem 0;
        }

        .search-bar input {
            width: 80%;
            padding: 0.5rem;
            border: 2px solid #a83232;
            border-radius: 5px;
        }

        .container {
            display: flex;
            padding: 1rem;
            gap: 1rem;
        }

        .sidebar {
            flex: 1;
            background-color: #f8d4d1;
            padding: 1rem;
            border-radius: 10px;
        }

        .sidebar h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .sidebar .tags {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar .tags input {
            margin-right: 0.5rem;
        }

        .sidebar .buttons {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar .buttons button {
            background-color: #a83232;
            color: white;
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .main-content {
            flex: 3;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background-color: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        .card img {
            width: 100%;
            height: 200px; /* Altura fija para todas las imágenes */
            border-radius: 10px;
            object-fit: cover;
        }

        .card h3 {
            margin: 0.5rem 0;
            font-size: 1.1rem;
            color: #333;
        }

        .card p {
            color: #666;
            font-size: 0.9rem;
        }

        .card button {
            background-color: #a83232;
            color: white;
            padding: 0.7rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .card .heart {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">BookSwap</div>
        <div class="icons">
            <img src="mail_icon.png" alt="Mail">
            <img src="user_icon.png" alt="User">
        </div>
    </header>

    <div class="search-bar">
        <input type="text" placeholder="Buscar...">
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
            <?php
            // Consulta para obtener los libros
            $sql = "SELECT libro.*, lector.Lec_mail FROM libro JOIN lector ON libro.Lib_usu_correo = lector.Lec_mail WHERE libro.Oculto = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Mostrar los datos de cada libro
                while($row = $result->fetch_assoc()) {
                    echo "<div class='card'>";
                    // Verificar si la imagen está presente
                    if (!empty($row['Lib_imagen'])) {
                        echo '<img src="' . $row['Lib_imagen'] . '" alt="Imagen del libro" class="book-img" />';
                    } else {
                        echo '<img src="placeholder.jpg" alt="Imagen no disponible" class="book-img" />';
                    }
                    echo "<h3 class='book-title'>" . $row['Lib_nom'] . "</h3>";
                    echo "<p class='book-uploader'>Subido por " . $row['Lec_mail'] . "</p>";
                    echo "<form method='POST' action='homee.php' class='form'>";
                    echo "<input type='hidden' name='lib_cod' value='" . $row['Lib_cod'] . "'>";
                    echo "<button type='submit' class='btn'>Solicitar</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hay libros disponibles.</p>";
            }
            ?>
        </section>
    </div>
</body>
</html>