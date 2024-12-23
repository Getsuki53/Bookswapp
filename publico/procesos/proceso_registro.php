<?php
include('../../configuracion/db.php');

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $comuna = $_POST['comuna'] ?? '';
    $username = $_POST['username'] ?? '';
    $comuna = $_POST['comuna'] ?? '';
    $contrasena = $_POST['password'] ?? '';
    $imagen = $_FILES['imagen'] ?? '';

    $nombreImagen = null;

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
        if (!move_uploaded_file($imagen['tmp_name'], $rutaImagen))
            die("Error al subir la imagen.");
    }

    // Verificar si 'Lector' ya existe en la tabla T_usuario
    $stmt_check_usuario = $conn->prepare("SELECT * FROM T_usuario WHERE Tus_Tipo_usu = ?");
    $tipoUsuario = 'Lector';
    $stmt_check_usuario->bind_param('s', $tipoUsuario);
    $stmt_check_usuario->execute();
    $result_check = $stmt_check_usuario->get_result();

    if ($result_check->num_rows == 0)
    {
        $stmt_insert_usuario = $conn->prepare("INSERT INTO T_usuario (Tus_Tipo_usu, Oculto) VALUES (?, 1)");
        $stmt_insert_usuario->bind_param('s', $tipoUsuario);
        if (!$stmt_insert_usuario->execute())
            die("Error al insertar el tipo de usuario 'Lector': " . $stmt_insert_usuario->error);
    }

    // Insertar el usuario en la tabla Usuario
    $stmt_usuario = $conn->prepare("INSERT INTO Usuario (Usu_mail, Usu_pass, Usu_T_usuario, Oculto) VALUES (?, ?, ?, 1)");
    $stmt_usuario->bind_param('sss', $correo, $contrasena, $tipoUsuario);
    if ($stmt_usuario->execute())
    {
        $stmt_lector = $conn->prepare("INSERT INTO Lector (Lec_mail, Lec_username, Lec_nom, Lec_apellido, Lec_comuna, Lec_img, Lec_cal, Oculto) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $calificacion = 0; // Calificación inicial para nuevos usuarios
        $stmt_lector->bind_param('ssssssi', $correo, $username, $nombre, $apellido, $comuna, $nombreImagen, $calificacion);

        if ($stmt_lector->execute())
        {
            echo "Usuario registrado exitosamente.";
            header("Location: ../vistas/control_acceso/login.php");
            exit();
        }
        else
        {
            $conn->query("DELETE FROM Usuario WHERE Usu_mail = '$correo'");
            echo "Error al registrar en Lector: " . $stmt_lector->error;
        }
    }
    else
        echo "Error al registrar el usuario en Usuario: " . $stmt_usuario->error;

   /*  // Verificar si 'Lector' ya existe en la tabla T_usuario */
    /* $sql_check_usuario = "SELECT * FROM T_usuario WHERE Tus_Tipo_usu = 'Lector'"; */
    /* $result_check = $conn->query($sql_check_usuario); */
    /*  */
    /* if ($result_check->num_rows == 0) { */
    /*     // Si 'Lector' no existe, insertarlo */
    /*     $sql_insert_usuario = "INSERT INTO T_usuario (Tus_Tipo_usu, Oculto) VALUES ('Lector', 1)"; */
    /*     if ($conn->query($sql_insert_usuario) !== TRUE) */
    /*         die("Error al insertar el tipo de usuario 'Lector' en T_usuario: " . $conn->error); */
    /* } */
    /*  */
    /* // Encriptar la contraseña antes de guardarla */
    /* // $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT); */
    /*  */
    /* // Insertar en la tabla Usuario (añadir el valor para 'Oculto') */
    /* $sql_usuario = "INSERT INTO Usuario (Usu_mail, Usu_pass, Usu_T_usuario, Oculto) */
    /*                 VALUES ('$correo', '$contrasena', 'Lector', 1)"; */
    /*                 // VALUES ('$correo', '$contrasena_hash', 'Lector', 1)"; */
    /*  */
    /* if ($conn->query($sql_usuario) === TRUE) */
    /* { */
    /*     if (empty($image)) */
    /*     { */
    /*         // Ahora que el usuario está insertado, insertamos en la tabla Lector (añadir el valor para 'Oculto') */
    /*         $sql_lector = "INSERT INTO Lector (Lec_mail, Lec_username, Lec_nom, Lec_apellido, Lec_comuna, Lec_img, Lec_cal, Oculto) */
    /*                        VALUES ('$correo', '$username', '$nombre', '$apellido', '$comuna', '$image', 0, 1)"; */
    /*     } */
    /*     else */
    /*     { */
    /*          // Ahora que el usuario está insertado, insertamos en la tabla Lector (añadir el valor para 'Oculto') */
    /*         $sql_lector = "INSERT INTO Lector (Lec_mail, Lec_username, Lec_nom, Lec_apellido, Lec_comuna, Lec_cal, Oculto) */
    /*                        VALUES ('$correo', '$username', '$nombre', '$apellido', '$comuna', 0, 1)"; */
    /*  */
    /*     } */
    /*     if ($conn->query($sql_lector) === TRUE) */
    /*     { */
    /*             echo "Usuario registrado exitosamente."; */
    /*             header("Location: ../vistas/control_acceso/login.php"); */
    /*             exit(); */
    /*     } */
    /*     else */
    /*     { */
    /*             // Si hay error al insertar en Lector, se elimina el usuario recién creado */
    /*             $conn->query("DELETE FROM Usuario WHERE Usu_mail = '$correo'"); */
    /*             echo "Error al registrar en Lector: " . $conn->error; */
    /*     }  */
    /* } */
    /* else */
    /*     echo "Error al registrar el usuario en Usuario: " . $conn->error; */
}

// Cerrar conexión 
$conn->close();
?>
