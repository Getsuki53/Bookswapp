<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro de Libro</title>
    </head>

    <body>
        <div class="cabecera">Registro de Libro</div>
        <?php
        session_start();
        include('../../../../configuracion/db.php');

        if (!isset($_SESSION['usu_correo']))
        {
            header("Location: ../../control_acceso/login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $titulo = $_POST['Titulo'];
            $nombre_autor = $_POST['nombre_autor'];
            $apellido_autor = $_POST['apellido_autor'];
            $editorial = $_POST['editorial'];
            $idioma = $_POST['idioma'];
            $etiqueta = $_POST['etiqueta'];
            $estado = $_POST['estado'];
            $valorEstimado = $_POST['valorEstimado'];
            $Usu_correo = $_SESSION['usu_correo'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK)
            {
                $file = $_FILES['image'];
                $fileName = $file['name'];
                $fileTmpPath = $file['tmp_name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions))
                    die("Error: Solo se permiten archivos JPG, JPEG, PNG y GIF.");

                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $newFileName = uniqid('img_', true) . '.' . $fileExtension;
                $imagePath = $uploadDir . $newFileName;

                if (!move_uploaded_file($fileTmpPath, $imagePath))
                    die("Error al mover la imagen.");
            }

            $conn->begin_transaction();

            try
            {
                $sql_editorial = "INSERT IGNORE INTO Editorial (Edit_nom, Edit_idioma, Oculto) VALUES (?, ?, 1)";
                $stmt_editorial = $conn->prepare($sql_editorial);
                $stmt_editorial->bind_param("ss", $editorial, $idioma);
                $stmt_editorial->execute();

                $sql_autor = "INSERT IGNORE INTO Autor (Aut_nom, Aut_apellido) VALUES (?, ?)";
                $stmt_autor = $conn->prepare($sql_autor);
                $stmt_autor->bind_param("ss", $nombre_autor, $apellido_autor);
                $stmt_autor->execute();

                if ($conn->affected_rows > 0)
                    $autor_id = $conn->insert_id;
                else
                {
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
            }
            catch (Exception $e)
            {
                $conn->rollback();
                echo "Error al registrar el libro: " . $e->getMessage();
            }
        }
        ?>
        <div class="container">
            <form class="formulario" method="POST" enctype="multipart/form-data">
                <div class="imagen-container">
                    <label for="image">Sube la imagen de tu libro aquí</label>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" alt="Vista previa de la imagen">
                </div>
                <div class="formulario-content">
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
                </div>
            </form>
        </div>

        <script>
            function previewImage(event)
            {
                const reader = new FileReader();
                reader.onload = function ()
                {
                    const preview = document.getElementById('preview');
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    </body>
</html>
