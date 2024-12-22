<?php
// filepath: /c:/xampp/htdocs/Bookswap/chat.php

// Iniciar sesión y conectar a la base de datos
session_start();
include('../../../../configuracion/db.php'); // Archivo de conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usu_correo']))
{
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

$chat_inter_id = $_GET['inter_id']; // ID de intercambio pasado como parámetro en la URL
$usu_correo = $_SESSION['usu_correo']; // Correo del usuario autenticado

// Obtener el chat_id correspondiente al chat_inter_id
$sql_chat = "SELECT chat_id FROM Chat WHERE chat_inter_id = ?";
$stmt_chat = $conn->prepare($sql_chat);
$stmt_chat->bind_param("i", $chat_inter_id);
$stmt_chat->execute();
$result_chat = $stmt_chat->get_result();
$chat = $result_chat->fetch_assoc();

if ($chat)
    $chat_id = $chat['chat_id'];
else
{
    // Crear un nuevo chat si no existe
    $sql_create_chat = "INSERT INTO Chat (chat_inter_id) VALUES (?)";
    $stmt_create_chat = $conn->prepare($sql_create_chat);
    $stmt_create_chat->bind_param("i", $chat_inter_id);
    $stmt_create_chat->execute();
    $chat_id = $stmt_create_chat->insert_id;
}

// Manejar el envío de mensajes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje']))
{
    $mensaje = $_POST['mensaje'];

    // Insertar el mensaje en la base de datos
    $sql = "INSERT INTO Mensaje (Men_mensaje, Men_fecha, Men_usu_id, Men_chat_id, Oculto) VALUES (?, NOW(), ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $mensaje, $usu_correo, $chat_id);
    $stmt->execute();
}

// Obtener los mensajes del chat
$sql = "SELECT * FROM Mensaje WHERE Men_chat_id = ? AND Oculto = 0 ORDER BY Men_fecha ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$result = $stmt->get_result();
$mensajes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chat</title>
        <link rel="stylesheet" href="../../../../recursos/css/chat.css">
    </head>
    <body>
        <div class="container">
            <h2>Chat</h2>
            <div class="chat-box">
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="chat-message">
                        <div class="user"><?php echo htmlspecialchars($mensaje['Men_usu_id']); ?></div>
                        <div class="date"><?php echo htmlspecialchars($mensaje['Men_fecha']); ?></div>
                        <div class="message"><?php echo htmlspecialchars($mensaje['Men_mensaje']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" class="form-group">
                <input type="text" name="mensaje" placeholder="Escribe tu mensaje..." required>
                <button type="submit">Enviar</button>
            </form>
        </div>
    </body>
</html>
