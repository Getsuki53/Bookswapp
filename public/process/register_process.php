<?php
include('../../config/db.php');

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $username = $_POST['username'] ?? '';
    $comuna = $_POST['comuna'] ?? '';
    $contrasena = $_POST['password'] ?? '';
    $calificacion = $_POST['calificacion'] ?? '';
    $imagen = $_FILES['imagen'] ?? '';

    /*  // Validar que los campos no estén vacíos */
    /* if (empty($nombre) || empty($apellido) || empty($correo) || empty($username) || empty($comuna) || empty($contrasena) || empty($imagen)) { */
    /*     die("Los campos son obligatorios"); */
    /* } */
    /*  */
    /* // Validar que el correo tenga un formato correcto */
    /* if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { */
    /*     die("El correo ingresado no es válido."); */
    /* } */
    /*  */
    /* // Validar que la calificación sea un número */
    /* if (!is_numeric($calificacion)) { */
    /*     die("La calificación debe ser un número"); */
    /* } */
/*  */
    // Validar la imagen
    if (isset($imagen) && $imagen['error'] == 0) {
        // Carpeta de destino para las imágenes
        $carpetaDestino = "../uploads/";
        
        // Crear la carpeta si no existe
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        // Renombrar la imagen para evitar colisiones
        $nombreImagen = time() . "_" . basename($imagen['name']);
        $rutaImagen = $carpetaDestino . $nombreImagen;

        // Mover la imagen cargada a la carpeta destino
        if (!move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
            die("Error al subir la imagen.");
        }
    } else {
        die("Por favor, selecciona una imagen válida.");
    }

    // Verificar si 'Lector' ya existe en la tabla T_usuario
    $sql_check_usuario = "SELECT * FROM T_usuario WHERE Tus_Tipo_usu = 'Lector'";
    $result_check = $conn->query($sql_check_usuario);

    if ($result_check->num_rows == 0) {
        // Si 'Lector' no existe, insertarlo
        $sql_insert_usuario = "INSERT INTO T_usuario (Tus_Tipo_usu, Oculto) VALUES ('Lector', 0)";
        if ($conn->query($sql_insert_usuario) !== TRUE) {
            die("Error al insertar el tipo de usuario 'Lector' en T_usuario: " . $conn->error);
        }
    }

    // Encriptar la contraseña antes de guardarla
    // $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar en la tabla Usuario (añadir el valor para 'Oculto')
    $sql_usuario = "INSERT INTO Usuario (Usu_mail, Usu_pass, Usu_T_usuario, Oculto)
                    VALUES ('$correo', '$contrasena', 'Lector', 1)";
                    // VALUES ('$correo', '$contrasena_hash', 'Lector', 1)";

    if ($conn->query($sql_usuario) === TRUE) {
        // Ahora que el usuario está insertado, insertamos en la tabla Lector (añadir el valor para 'Oculto')
        $sql_lector = "INSERT INTO Lector (Lec_mail, Lec_username, Lec_nom, Lec_apellido, Lec_Comuna, Lec_cal, Lec_img, Oculto)
                       VALUES ('$correo', '$username', '$nombre', '$apellido', '$comuna', '$calificacion', '$nombreImagen', 0)";

        if ($conn->query($sql_lector) === TRUE) {
            // Si todo fue bien, redirigimos al usuario
            echo "Usuario registrado exitosamente.";
            header("Location: ../views/login.php");
            exit();
        } else {
            // Si hay error al insertar en Lector, se elimina el usuario recién creado
            $conn->query("DELETE FROM Usuario WHERE Usu_mail = '$correo'");
            echo "Error al registrar en Lector: " . $conn->error;
        }
    } else {
        echo "Error al registrar el usuario en Usuario: " . $conn->error;
    }
}

// Cerrar conexión 
$conn->close();
?>
