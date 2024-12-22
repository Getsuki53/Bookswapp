<?php

session_start();
include('../../config/db.php');


if (!isset($_SESSION['correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los datos del formulario
    $titulo = $_POST['Titulo'];
    $autores = $_POST['autor'];
    $editorial = $_POST['editorial'];
    $neweditorial = $_POST['neweditorial'];
    $estado = $_POST['estado'];
    $Usu_correo = $_SESSION['correo'];
    $valorEstimado = $_POST['valorEstimado'];
    $autoresl = explode(",", $autores);
    $nombres = [];
    $apellidos = []; 

    // Validar que se proporcione un autor (existente o nuevo, no ambos)
    if (empty($editorial) && empty($neweditorial)) {
        die("Debes seleccionar un autor existente o ingresar un nuevo autor.");
    }

    if (!empty($editorial) && !empty($neweditorial)) {
        die("No puedes seleccionar un autor existente y agregar uno nuevo al mismo tiempo.");
    }

    $sql_idiomas = "SELECT Idioma_nom FROM idioma WHERE Oculto = 1";   //Queremos que el usuario solo pueda seleccionar de los idiomas que ya existen
    $result_idiomas = $conn->query($sql_idiomas);

    if (!$result_idiomas)
    {
        die("Error en la consulta SQL: " . $conn->error);
    }
    else
    {
        echo "La consulta fue exitosa. Número de filas: " . $result_idiomas->num_rows;
    }

    //IMAGEN
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
}
?>
