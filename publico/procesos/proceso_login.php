<?php
// Incluir la conexión a la base de datos
include('../../config/db.php');

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo'] ?? ''); // Eliminar espacios en blanco
    $password = trim($_POST['password'] ?? ''); // Eliminar espacios en blanco

    // Validar que los campos no estén vacíos
    if (empty($correo) || empty($password)) {
        die("Por favor, completa todos los campos.");
    }

    // Mostrar los datos ingresados para verificar
    // echo "Correo ingresado: $correo<br>";
    // echo "Contraseña ingresada: $password<br>";

    // Consultar el correo en la tabla Usuario
    $sql = "SELECT Usu_pass FROM Usuario WHERE Usu_mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el correo existe en la base de datos
    if ($stmt->num_rows > 0)
    {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // echo "Contraseña en la base de datos: $hashed_password<br>";

        // Comparar las contraseñas (sin cifrado por ahora)
        if ($password === $hashed_password)
        {
            // Inicio de sesión exitoso
            session_start();
            $_SESSION['correo'] = $correo;

            // Redirigir al home.php
            header("Location: ../vistas/home.php");
            exit();
        }
        else
            echo "Contraseña incorrecta.";
    }
    else
        echo "El correo no está registrado.";

    $stmt->close();
}
else
    echo "Método de solicitud no válido.";

// Cerrar conexión
$conn->close();
?>
