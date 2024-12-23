<?php

session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] ==  'POST') {

    $editorial = $_POST['editorial'];
    $idioma = $_POST['idioma'];
    $stmt_check = "SELECT COUNT(*) FROM Idioma WHERE Idio_nom = '$idioma'";
    $result_check = $conn->query($stmt_check);
    if ($result_check > 0) {
        $sql_editorial = "INSERT INTO Editorial(Edit_nom, Edit_idioma, Oculto) VALUES ('$editorial', '$idioma', 1)";
        if ($conn->query($sql_editorial) === TRUE) {
            header("Location: exito.php");
            exit();
        } else {
            echo "Error al registrar la editorial: " . $conn->error;
        }
    } else {
        echo "El idioma ingresado no existe.";
    }
    
}


?>

<form action="editorial.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="editorial" placeholder="Editorial" required>
    <input type="text" name="idioma" placeholder="Idioma" required>
    <button type="submit">Enviar</button>
</form>