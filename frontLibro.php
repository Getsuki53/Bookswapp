<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Libro</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .cabecera {
            background-color: #b71c1c;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        cabecera .titulo {
            font-size: 24px;
            font-weight: bold;
        }
        .cabecera .iconos {
            display: flex;
            gap: 15px;
        }
        .cabecera .iconos img {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .logo{
            display: flex;
        }

        .logo img{
            width: 50px;
            height: 50px;
        }

        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.3);
        }
        .formulario {
            background-color: #b71c1c;
            border-radius: 10px;
            padding: 20px;
            color: white;
            width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .formulario h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .formulario .imagen {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150px;
            background-color: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
            color: gray;
            font-size: 14px;
        }
        .formulario label {
            display: block;
            margin: 10px 0 5px;
        }
        .formulario input, .formulario select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
        }
        .formulario button {
            width: 100%;
            padding: 10px;
            background-color: #f44336;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .formulario button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="cabecera">Registro de Libros</div>
    <div class="cabecera">
        <img src="icono-izquierda.png" alt="Icono izquierda">
        <div class="titulo">Registro de Libros</div>
        <div class="iconos">
            <img src="icono1.png" alt="Icono 1">
            <img src="icono2.png" alt="Icono 2">
            <img src="icono3.png" alt="Icono 3">
        </div>
    </div>

    <?php
    session_start();
    include('db.php');

    if (!isset($_SESSION['usu_correo'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = $_POST['Titulo'];
        $nombre_autor = $_POST['nombre_autor'];
        $apellido_autor = $_POST['apellido_autor'];
        $editorial = $_POST['editorial'];
        $idioma = $_POST['idioma'];
        $etiqueta = $_POST['etiqueta'];
        $estado = $_POST['estado'];
        $valorEstimado = $_POST['valorEstimado'];
        $Usu_correo = $_SESSION['usu_correo'];

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

        $conn->begin_transaction();

        try {
            $sql_editorial = "INSERT IGNORE INTO Editorial (Edit_nom, Edit_idioma, Oculto) VALUES (?, ?, 1)";
            $stmt_editorial = $conn->prepare($sql_editorial);
            $stmt_editorial->bind_param("ss", $editorial, $idioma);
            $stmt_editorial->execute();

            $sql_autor = "INSERT IGNORE INTO Autor (Aut_nom, Aut_apellido) VALUES (?, ?)";
            $stmt_autor = $conn->prepare($sql_autor);
            $stmt_autor->bind_param("ss", $nombre_autor, $apellido_autor);
            $stmt_autor->execute();

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

            $sql_libro = "INSERT INTO Libro (Lib_nom, Lib_nom_autor, Lib_ape_autor, Lib_editorial, Lib_idioma, Lib_etiqueta, Lib_estado, Lib_valorest, Lib_usu_correo, Lib_imagen, Oculto) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt_libro = $conn->prepare($sql_libro);
            $stmt_libro->bind_param("ssssssssss", $titulo, $nombre_autor, $apellido_autor, $editorial, $idioma, $etiqueta, $estado, $valorEstimado, $Usu_correo, $imagePath);
            $stmt_libro->execute();

            $libro_id = $conn->insert_id;

            $sql_libro_autor = "INSERT INTO Libro_Autor (Liba_Aut_id, Liba_Lib_cod) VALUES (?, ?)";
            $stmt_libro_autor = $conn->prepare($sql_libro_autor);
            $stmt_libro_autor->bind_param("ii", $autor_id, $libro_id);
            $stmt_libro_autor->execute();

            $sql_libro_editorial = "INSERT INTO Libro_Editorial (Libed_Edit_nom, Libed_Lib_cod) VALUES (?, ?)";
            $stmt_libro_editorial = $conn->prepare($sql_libro_editorial);
            $stmt_libro_editorial->bind_param("si", $editorial, $libro_id);
            $stmt_libro_editorial->execute();

            $sql_libro_etiqueta = "INSERT INTO Libro_Etiqueta (Libet_Lib_cod, Libet_etiq_nom) VALUES (?, ?)";
            $stmt_libro_etiqueta = $conn->prepare($sql_libro_etiqueta);
            $stmt_libro_etiqueta->bind_param("is", $libro_id, $etiqueta);
            $stmt_libro_etiqueta->execute();

            $conn->commit();
            header("Location: exito.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error al registrar el libro: " . $e->getMessage();
        }
    }
    ?>

    <div class="container">
        <form class="formulario" method="POST" enctype="multipart/form-data">
            <h1>Registro de Libro</h1>
            <div class="imagen">
                <input type="file" name="image" accept="image/*" required>
            </div>
            <label for="Titulo">Título</label>
            <input type="text" id="Titulo" name="Titulo" placeholder="HARRY POTTER Y EL CÁLIZ DE FUEGO" required>

            <label for="nombre_autor">Nombre del Autor</label>
            <input type="text" id="nombre_autor" name="nombre_autor" placeholder="J.K" required>

            <label for="apellido_autor">Apellido del Autor</label>
            <input type="text" id="apellido_autor" name="apellido_autor" placeholder="Rowling" required>

            <label for="editorial">Editorial</label>
            <input type="text" id="editorial" name="editorial" placeholder="SALAMANDRA" required>

            <label for="valorEstimado">Precio Estimado</label>
            <input type="number" id="valorEstimado" name="valorEstimado" placeholder="20.000" required>

            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>

            <label for="etiqueta">Etiquetas</label>
            <input type="text" id="etiqueta" name="etiqueta" placeholder="Fantasía, Magia, Aventura" required>

            <label for="idioma">Idioma</label>
            <input type="text" id="idioma" name="idioma" placeholder="Español" required>

            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>