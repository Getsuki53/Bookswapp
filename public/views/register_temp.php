<?php
    // Inicializamos variables
    $correo = "";
    $errorCorreo = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $correo = trim($_POST['correo']); // Capturamos el valor del correo

        // Función para validar el correo
        function validarCorreo($correo) {
            $dominiosReconocidos = [
                'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com',
                'edu.cl', 'gov.cl', 'mil.cl',
                'es', 'cl', 'mx', 'ar', 'uk', 'de',
                'tech', 'dev', 'store', 'ai'
            ];

            // Verificar que haya un solo '@'
            $partes = explode('@', $correo);
            if (count($partes) !== 2) {
                return "El correo debe tener un solo '@'.";
            }

            // Verificar que el dominio sea reconocido
            $dominio = $partes[1];
            if (!in_array($dominio, $dominiosReconocidos)) {
                return "El dominio '$dominio' no está en la lista reconocida.";
            }

            return true; // Si pasa todas las validaciones
        }

        // Validamos el correo
        $resultadoValidacion = validarCorreo($correo);
        if ($resultadoValidacion !== true) {
            $errorCorreo = $resultadoValidacion; // Guardamos el mensaje de error
        }

        // Si no hay errores, procesamos el registro
        if (empty($errorCorreo)) {
            echo "<p>Formulario enviado correctamente. El correo ingresado es: <strong>" . htmlspecialchars($correo) . "</strong></p>";
            exit; // Detenemos el script después de mostrar el mensaje
        }
    }
?>

