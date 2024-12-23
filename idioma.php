<?php

session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idioma = $_POST['idioma'];

    $sql_idioma = "INSERT INTO Idioma(Idio_nom, Oculto) VALUES ('$idioma', 1)";
    if ($conn->query($sql_idioma) === TRUE) {
        header("Location: exito.php");
        exit();
    } else {
        echo "Error al registrar el idioma: " . $conn->error;
    }
    
}

?>


<form action="idioma.php" method="POST" enctype="multipart/form-data">
    <label for="idioma">Nombre del Idioma:</label>
    <input type="text" id="idioma" name="idioma" required>

    <button type="submit">Crear Idioma</button>