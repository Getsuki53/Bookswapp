<?php

session_start();
include('db.php');

if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los datos del formulario
    $titulo = $_POST['Titulo'];
    $nombre_autor = $_POST['nombre_autor'];
    $apellido_autor = $_POST['apellido_autor'];
    $editorial = $_POST['editorial'];
    $idioma = $_POST['idioma'];
    $etiqueta = $_POST['etiqueta'];
    $estado = $_POST['estado'];
    $valorEstimado = $_POST['valorEstimado'];
    $Usu_correo = $_SESSION['usu_correo'];

    //CONTEO DE EDITORIALES
    $sql_check = "SELECT COUNT(*) FROM Editorial WHERE Edit_nom = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $editorial);
    $stmt_check->execute();
    $stmt_check->store_result(); // Almacena el resultado para usarlo posteriormente
    $stmt_check->bind_result($result_check);
    $stmt_check->fetch(); 
    //CONTEO DE AUTOR 
    sql_check_autor = "SELECT COUNT(*) FROM autor WHERE Aut_nom = ? and Aut_ape = ?";
    $stmt_check_autor = $conn->prepare($sql_check_autor);
    $stmt_check_autor->bind_param("ss", $nombre_autor, $apellido_autor);
    $stmt_check_autor->execute();
    $stmt_check_autor->store_result(); // Almacena el resultado para usarlo posteriormente
    $stmt_check_autor->bind_result($result_check_autor);
    $stmt_check_autor->fetch();
    
    
    //REGISTRO DE IMAGEN
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];

        // Información del archivo
        $fileName = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $fileError = $file['error'];

        // Define el directorio de destino
        $uploadDir = 'uploads/'; // Asegúrate de que este directorio exista y tenga permisos de escritura
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generar un nombre único para la imagen
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Error: Solo se permiten archivos JPG, JPEG, PNG y GIF.");
        }

        $newFileName = uniqid('img_', true) . '.' . $fileExtension;
        $imagePath = $uploadDir . $newFileName;

        // Mover la imagen al directorio final
        if (move_uploaded_file($fileTmpPath, $imagePath)) {
            echo "Imagen subida correctamente.";
        } else {
            die("Error al mover la imagen.");
        }
    } else {
        die("Error al subir la imagen: " . $_FILES['image']['error']);
    }


    // REGISTRO DE EDITORIAL
    if ($result_check == 0) {
        $sql_editorial = "INSERT INTO Editorial(Edit_nom, Edit_idioma, Oculto) VALUES ('$editorial', '$idioma', 1)";
        if ($conn->query($sql_editorial) === TRUE) {
            //REGISTRO DE LIBRO
            $sql_libro = "INSERT INTO Libro(Lib_nom, Lib_nom_autor, Lib_ape_autor, Lib_editorial, Lib_idioma, Lib_etiqueta, Lib_estado, Lib_valorest, Lib_usu_correo, Lib_imagen, Oculto) 
            VALUES ('$titulo', '$nombre_autor', '$apellido_autor', '$editorial', '$idioma', '$etiqueta', '$estado', '$valorEstimado', '$Usu_correo', '$file', 1)";
            if ($conn->query($sql_libro) === TRUE) {
                header("Location: exito.php");
                exit();
            } else {
                echo "Error al registrar el libro: " . $conn->error;
            }
        } 
       } else {
       // echo "Entrando en el bloque else...<br>";
        $sql_libro = "INSERT INTO Libro(Lib_nom, Lib_nom_autor, Lib_ape_autor, Lib_editorial, Lib_idioma, Lib_etiqueta, Lib_estado, Lib_valorest, Lib_usu_correo, Lib_imagen, Oculto) 
        VALUES ('$titulo', '$nombre_autor', '$apellido_autor', '$editorial', '$idioma', '$etiqueta', '$estado', '$valorEstimado', '$Usu_correo', '$file', 1)";
        if ($conn->query($sql_libro) === TRUE) {
            header("Location: exito.php");
            exit();
        } else {
            echo "Error al registrar el libro: " . $conn->error;  }
    }

}
?>




<form action="registroLibro.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="Titulo" placeholder="Título del libro" required>
    <input type="text" name="nombre_autor" placeholder="Nombre del autor" required>
    <input type="text" name="apellido_autor" placeholder="Apellido del autor" required>
    <input type="text" name="editorial" placeholder="Editorial" required>
    <input type="text" name="idioma" placeholder="Idioma" required>
    <input type="text" name="etiqueta" placeholder="Etiqueta" required>
    <input type="text" name="estado" placeholder="Estado" required>
    <input type="text" name="valorEstimado" placeholder="Valor estimado" required>
    <label for="image">Imagen del libro:</label>
    <input type="file" name="image" id="image" accept="image/*" required>
    <button type="submit">Enviar</button>
</form>