<?php
// Incluir la conexión a la base de datos
include('../../config/db.php');

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validar que los campos no estén vacíos
    if (empty($correo) || empty($password)) {
        die("Por favor, completa todos los campos.");
    }

    // Consultar el correo en la tabla Usuario
    $sql = "SELECT Usu_pass FROM Usuario WHERE Usu_mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el correo existe en la base de datos
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Comparar las contraseñas (usa password_verify si las contraseñas están encriptadas)
        if ($password === $hashed_password /* password_verify($password, $hashed_password) */) {
            // Inicio de sesión exitoso
            session_start();
            $_SESSION['correo'] = $correo;

            // Redirigir al home.php
            header("Location: ../views/home.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "El correo no está registrado.";
    }

    $stmt->close();
} else {
    echo "Método de solicitud no válido.";
}

// Cerrar conexión
$conn->close();
?>
