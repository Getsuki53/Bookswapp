<?php
// Inicia sesión y conecta a la base de datos
session_start();
include('db.php');


// Obtener la etiqueta buscada
if (isset($_GET['username'])) {
    $username = $conn->real_escape_string($_GET['username']);

    // Consultar libros que contengan la etiqueta
    $sql = "SELECT * FROM libro, lector WHERE lector.Lec_username='$username' AND libro.Lib_Usu_correo=lector.Lec_mail AND libro.Oculto=1";
    $result = $conn->query($sql);

    // Verificar si hay resultados
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
        echo "<p>No se encontraron libros con la etiqueta: " . $etiqueta . "</p>";
    }
} 

$conn->close();
?>

<form method="GET" action="buscarBibliotecaUsuario.php">
    <label for="username">Buscar por username:</label>
    <input type="text" id="etiqueta" name="username" placeholder="Ejemplo: DrKalaka" required>
    <button type="submit">Buscar</button>
</form>

