<?php
// Inicia sesión y conecta a la base de datos
session_start();
include('../../../../configuracion/db.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) 
    die("Error de conexión: " . $conn->connect_error);

// Obtener la etiqueta buscada
if (isset($_GET['etiqueta']))
{
    $etiqueta = $conn->real_escape_string($_GET['etiqueta']);

    // Consultar libros que contengan la etiqueta
    $sql = "SELECT * FROM libro WHERE '$etiqueta' = Lib_etiqueta";
    $result = $conn->query($sql);

    // Verificar si hay resultados
    if ($result->num_rows > 0)
    {
        // Mostrar los datos de cada libro
        while($row = $result->fetch_assoc())
        {
            echo "<div class='card'>";
            // Verificar si la imagen está presente
            if (!empty($row['Lib_imagen']))
                echo '<img src="' . $row['Lib_imagen'] . '" alt="Imagen del libro" class="book-img" />';
            else
                echo '<img src="placeholder.jpg" alt="Imagen no disponible" class="book-img" />';
            echo "<h3 class='book-title'>" . $row['Lib_nom'] . "</h3>";
            echo "<p class='book-uploader'>Subido por " . $row['Lib_Usu_correo'] . "</p>";
            echo "<form method='POST' action='homee.php' class='form'>";
            echo "<input type='hidden' name='lib_cod' value='" . $row['Lib_cod'] . "'>";
            echo "<button type='submit' class='btn'>Solicitar</button>";
            echo "</form>";
            echo "</div>";
        }
    }
    else
        echo "<p>No se encontraron libros con la etiqueta: " . $etiqueta . "</p>";
} 

$conn->close();
?>

<form method="GET" action="buscar_etiqueta.php">
    <label for="etiqueta">Buscar por etiqueta:</label>
    <input type="text" id="etiqueta" name="etiqueta" placeholder="Ejemplo: terror" required>
    <button type="submit">Buscar</button>
</form>
