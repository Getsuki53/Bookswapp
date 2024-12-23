<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');

if (!isset($_SESSION['usu_correo'])) {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los datos del formulario
    $titulo = trim($_POST['titulo']);
    $nombre_autor = trim($_POST['nombre_autor']);
    $apellido_autor = trim($_POST['apellido_autor']);
    $editorial = trim($_POST['editorial']);
    $idioma = trim($_POST['idioma']);
    $etiqueta = trim($_POST['etiqueta']);
    $estado = intval($_POST['estado']);
    $valorEstimado = intval($_POST['valorEstimado']);
    $usuario_correo = $_SESSION['Usu_correo'];
   // $usuario_correo = trim($_POST['usuario_correo']);
    
    // Manejo de la imagen cargada
    /*
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen_data = file_get_contents($_FILES['imagen']['tmp_name']);
    } else {
        echo "Error al cargar la imagen.";
        exit();
    }*/

    // Inicio de transacción
    $conn->begin_transaction();

    try {
        // Inserta en la tabla Autor si no existe
        $sql_autor = "INSERT IGNORE INTO Autor (Aut_nom, Aut_apellido) VALUES (?, ?)";
        $stmt_autor = $conn->prepare($sql_autor);
        $stmt_autor->bind_param("ss", $nombre_autor, $apellido_autor);
        $stmt_autor->execute();

        $sql_editorial = "INSERT IGNORE INTO editorial(Edit_nom, Edit_idioma, Oculto) VALUES (?, ?)";
        $stmt_editorial = $conn->prepare($sql_editorial);
        $stmt_editorial->bind_param("ssi", $editorial, $idioma, $oculto);
        $stmt_editorial->bind_param("ssi", $editorial, $idioma, $oculto);
        $stmt_editorial->execute();

        // Obtiene el ID del autor
        $sql_autor_id = "SELECT Aut_id FROM Autor WHERE Aut_nom = ? AND Aut_apellido = ?";
        $stmt_autor_id = $conn->prepare($sql_autor_id);
        $stmt_autor_id->bind_param("ss", $nombre_autor, $apellido_autor);
        $stmt_autor_id->execute();
        $result_autor = $stmt_autor_id->get_result();
        $autor_id = $result_autor->fetch_assoc()['Aut_id'];

        // Inserta en la tabla Libro
        $sql_libro = "INSERT INTO Libro (Lib_nom, Lib_nom_autor, Lib_ape_autor, Lib_editorial, Lib_idioma, Lib_etiqueta, Lib_estado, Lib_valorest, Lib_Usu_correo, Lib_imagen, Oculto) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_libro = $conn->prepare($sql_libro);
        $oculto = 1;
        $stmt_libro->bind_param("ssssssiiisb", $titulo, $nombre_autor, $apellido_autor, $editorial, $idioma, $etiqueta, $estado, $valorEstimado, $usuario_correo, $oculto, $imagen_data);
        $stmt_libro->execute();

        // Obtiene el ID del libro
        $libro_id = $conn->insert_id;

        // Inserta en la tabla Libro_Autor
        $sql_libro_autor = "INSERT INTO Libro_Autor (Liba_Aut_id, Liba_Lib_cod) VALUES (?, ?)";
        $stmt_libro_autor = $conn->prepare($sql_libro_autor);
        $stmt_libro_autor->bind_param("ii", $autor_id, $libro_id);
        $stmt_libro_autor->execute();

        // Confirma la transacción
        $conn->commit();

        // Redirige a la página principal
        header("Location: exito.php");
        exit();
    } catch (Exception $e) {
        // En caso de error, revierte la transacción
        $conn->rollback();
        header("Location: error.php");
        echo "Error al registrar el libro: " . $e->getMessage();
    }
}
?>


<form action="libro.php" method="POST" enctype="multipart/form-data">
    <div>
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>
    </div>
    <div>
        <label for="nombre_autor">Nombre del Autor:</label>
        <input type="text" id="nombre_autor" name="nombre_autor" required>
    </div>
    <div>
        <label for="apellido_autor">Apellido del Autor:</label>
        <input type="text" id="apellido_autor" name="apellido_autor" required>
    </div>
    <div>
        <label for="editorial">Editorial:</label>
        <input type="text" id="editorial" name="editorial" required>
    </div>
    <div>
        <label for="idioma">Idioma:</label>
        <input type="text" id="idioma" name="idioma" required>
    </div>
    <div>
        <label for="etiqueta">Etiqueta:</label>
        <input type="text" id="etiqueta" name="etiqueta" required>
    </div>
    <div>
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="1">1 - Nuevo</option>
            <option value="2">2 - Muy bueno</option>
            <option value="3">3 - Bueno</option>
            <option value="4">4 - Regular</option>
            <option value="5">5 - Dañado</option>
        </select>
    </div>
    <div>
        <label for="valorEstimado">Valor Estimado:</label>
        <input type="number" id="valorEstimado" name="valorEstimado" required>
    </div>
    /* <div>
        <label for="imagen">Cargar Imagen:</label>
        <input type="file" id="imagen" name="imagen" accept="image/*" required>
    </div> */
    <div>
        <button type="submit">Registrar Libro</button>
    </div>
</form>