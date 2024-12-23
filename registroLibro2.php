<?php
session_start();
include('db.php');

if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Validación de la imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions) || mime_content_type($fileTmpPath) !== "image/jpeg") {
            die("Error: Solo se permiten archivos JPG, JPEG, PNG y GIF.");
        }

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $newFileName = uniqid('img_', true) . '.' . $fileExtension;
        $imagePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $imagePath)) {
            die("Error al mover la imagen.");
        }
    } else {
        die("Error al subir la imagen: " . $_FILES['image']['error']);
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Registro de editorial
        $sql_editorial = "INSERT IGNORE INTO Editorial (Edit_nom, Edit_idioma, Oculto) VALUES (?, ?, 1)";
        $stmt_editorial = $conn->prepare($sql_editorial);
        $stmt_editorial->bind_param("ss", $editorial, $idioma);
        $stmt_editorial->execute();

        // Registro de autor
        $sql_autor = "INSERT IGNORE INTO Autor (Aut_nom, Aut_apellido) VALUES (?, ?)";
        $stmt_autor = $conn->prepare($sql_autor);
        $stmt_autor->bind_param("ss", $nombre_autor, $apellido_autor);
        $stmt_autor->execute();
        
        // Obtener ID del autor
        if ($conn->affected_rows > 0) {
            $autor_id = $conn->insert_id;
        } else {
            $sql_get_autor_id = "SELECT Aut_id FROM Autor WHERE Aut_nom = ? AND Aut_apellido = ?";
            $stmt_get_autor_id = $conn->prepare($sql_get_autor_id);
            $stmt_get_autor_id->bind_param("ss", $nombre_autor, $apellido_autor);
            $stmt_get_autor_id->execute();
            $stmt_get_autor_id->bind_result($autor_id);
            $stmt_get_autor_id->fetch();
        }

        // Registro de libro
        $sql_libro = "INSERT INTO Libro (Lib_nom, Lib_nom_autor, Lib_ape_autor, Lib_editorial, Lib_idioma, Lib_etiqueta, Lib_estado, Lib_valorest, Lib_usu_correo, Lib_imagen, Oculto) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt_libro = $conn->prepare($sql_libro);
        $stmt_libro->bind_param("ssssssssss", $titulo, $nombre_autor, $apellido_autor, $editorial, $idioma, $etiqueta, $estado, $valorEstimado, $Usu_correo, $imagePath);
        $stmt_libro->execute();

        // Obtener ID del libro recién registrado
        $libro_id = $conn->insert_id;

        

        // Relación libro-autor
        $sql_libro_autor = "INSERT INTO Libro_Autor (Liba_Aut_id, Liba_Lib_cod) VALUES (?, ?)";
        $stmt_libro_autor = $conn->prepare($sql_libro_autor);
        $stmt_libro_autor->bind_param("ii", $autor_id, $libro_id);
        $stmt_libro_autor->execute();

        // Relación libro-editorial
        $sql_libro_editorial = "INSERT INTO Libro_Editorial (Libed_Edit_nom, Libed_Lib_cod) VALUES (?, ?)";
        $stmt_libro_editorial = $conn->prepare($sql_libro_editorial);
        $stmt_libro_editorial->bind_param("si", $editorial, $libro_id); // Nota: Puede ser necesario usar `$editorial_id` si usas IDs para la editorial.
        $stmt_libro_editorial->execute();

        // Relación libro-etiqueta
        $sql_libro_etiqueta = "INSERT INTO Libro_Etiqueta (Libet_Lib_cod, Libet_etiq_nom) VALUES (?, ?)";
        $stmt_libro_etiqueta = $conn->prepare($sql_libro_etiqueta);
        $stmt_libro_etiqueta->bind_param("is", $libro_id, $etiqueta);
        $stmt_libro_etiqueta->execute();
        
        // Confirmar transacción
        $conn->commit();
        header("Location: exito.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al registrar el libro: " . $e->getMessage();
    }
}
?>




<form action="registroLibro2.php" method="POST" enctype="multipart/form-data">
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