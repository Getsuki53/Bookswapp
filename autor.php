<?php
// Inicia sesión y conecta a la base de datos
session_start();
include('db.php');

// Verificar si el usuario está autenticado 
if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nombre = $_POST['nombre_autor'];
    $apellido = $_POST['apellido_autor'];

    $sql_check = "SELECT COUNT(*) FROM Editorial WHERE Aut_nom = 'nombre_autor' and Aut_apellido = 'apellido_autor'";    
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $editorial);
    $stmt_check->execute();
    $stmt_check->store_result(); // Almacena el resultado para usarlo posteriormente
    $stmt_check->bind_result($result_check);
    $stmt_check->fetch(); 

    if($result_check == 0){
        $sql = "INSERT INTO autor (Aut_nom, Aut_ape) VALUES (?, ?)";
        if ($conn->query($sql_idioma) === TRUE) {
            header("Location: exito.php");
            exit();
        } else {
            echo "Error al registrar el autor: " . $conn->error;
        }
    }

}

?>
<form action="autor.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="nombre_autor" placeholder="Nombre del autor" required>
    <input type="text" name="apellido_autor" placeholder="Apellido del autor" required>
    <button type="submit">Enviar</button>
</form>