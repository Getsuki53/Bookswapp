<?php
session_start(); // Iniciar la sesión

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "bookswap");

// Manejar errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Servir imagen si se solicita
if (isset($_GET['accion']) && $_GET['accion'] === 'imagen') {
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

    if ($tipo === 'libro') {
        // Consultar la imagen del libro en la base de datos
        $query = "SELECT Lib_imagen FROM libro WHERE Lib_cod = ?";
    } elseif ($tipo === 'usuario') {
        // Consultar la imagen del usuario en la base de datos
        $query = "SELECT Lec_img FROM lector WHERE Lec_mail = ?";
    } else {
        // Tipo no válido, mostrar una imagen por defecto
        header("Content-Type: image/png");
        readfile("default.png");
        exit;
    }

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($imagen);
    $stmt->fetch();

    if ($imagen) {
        header("Content-Type: image/jpeg"); // Cambiar según el formato de las imágenes
        echo $imagen;
    } else {
        // Mostrar una imagen por defecto si no se encuentra
        header("Content-Type: image/png");
        readfile("default.png"); 
    }

    // Finalizar el script para no seguir ejecutando
    $stmt->close();
    $conexion->close();
    exit;
}

// Consultar información del usuario
if (isset($_SESSION['usuario'])) {
    $lec_mail = $_SESSION['usuario'];
} else {
    // Redirigir al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit;
}

$query_lector = "SELECT * FROM lector WHERE Lec_mail = '$lec_mail'";
$resultado_lector = $conexion->query($query_lector);
$lector = $resultado_lector->fetch_assoc();

// Consultar libros de la biblioteca del usuario
$query_biblioteca = "SELECT Lib_cod, Lib_nom FROM libro WHERE Lib_Usu_correo = '$lec_mail' AND Oculto = 1";
$resultado_biblioteca = $conexion->query($query_biblioteca);

// Consultar libros de la lista de deseos del usuario
$query_lista_deseos = "SELECT libro.Lib_cod, libro.Lib_nom 
FROM lista_deseos 
JOIN libro ON lista_deseos.Listd_Lib_cod = libro.Lib_cod 
WHERE lista_deseos.Listd_usu_id = '$lec_mail' AND libro.Oculto = 1";
$resultado_lista_deseos = $conexion->query($query_lista_deseos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="images/BookSwap-removebg-preview.png" alt="Logo BookSwap">
    </div>
    <div class="iconos">
        <a href="notificaciones.php"><i class="fas fa-bell"></i></a>
        <a href="buscar.php"><i class="fas fa-search"></i></a>
        <a href="mensajes.php"><i class="fas fa-envelope"></i></a>
    </div>
</header>
<main>
    <div class="perfil">
        <img src="perfil.php?accion=imagen&tipo=usuario&id=<?= htmlspecialchars($lector['Lec_mail']) ?>" alt="Avatar Usuario" class="avatar">
        <div class="reportar">
            <i class="fas fa-exclamation-circle" onclick="toggleReportar()"></i>
            <div id="reportar-opcion" class="reportar-opcion">
                <a href="reportar.php?usuario=<?= htmlspecialchars($lector['Lec_mail']) ?>">Reportar Usuario</a>
            </div>
        </div>
        <h2 class="nombre-usuario">
            <?= htmlspecialchars($lector['Lec_nom']) . " " . htmlspecialchars($lector['Lec_apellido']) ?>
            <a href="editar_perfil.php" class="editar"><i class="fas fa-pencil-alt"></i></a>
        </h2>
        <p>@<?= htmlspecialchars($lector['Lec_username']) ?></p>
        <p><?= htmlspecialchars($lector['Lec_comuna']) ?></p>
        <div class="calificacion">
            <?php
            $calificacion = intval($lector['Lec_cal']);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $calificacion) {
                    echo '<i class="fas fa-star"></i>';
                } else {
                    echo '<i class="far fa-star"></i>';
                }
            }
            ?>
        </div>
    </div>
    <div class="contenedor">
        <div class="biblioteca">
            <h3>BIBLIOTECA</h3>
            <div class="libros">
                <?php while ($libro = $resultado_biblioteca->fetch_assoc()): ?>
                    <div class="libro">
                        <img src="perfil.php?accion=imagen&tipo=libro&id=<?= htmlspecialchars($libro['Lib_cod']) ?>" alt="<?= htmlspecialchars($libro['Lib_nom']) ?>">
                        <p><?= htmlspecialchars($libro['Lib_nom']) ?></p>
                        <a href="info_libro.php?libro=<?= htmlspecialchars($libro['Lib_nom']) ?>" class="btn">Ver más información</a>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="flecha izquierda" onclick="moverLibros('izquierda', 'biblioteca')">❮</div>
            <div class="flecha derecha" onclick="moverLibros('derecha', 'biblioteca')">❯</div>
        </div>
        <div class="lista-deseos">
            <h3>LISTA DE DESEOS</h3>
            <div class="libros">
                <?php while ($libro = $resultado_lista_deseos->fetch_assoc()): ?>
                    <div class="libro">
                        <img src="perfil.php?accion=imagen&tipo=libro&id=<?= htmlspecialchars($libro['Lib_cod']) ?>" alt="<?= htmlspecialchars($libro['Lib_nom']) ?>">
                        <p><?= htmlspecialchars($libro['Lib_nom']) ?></p>
                        <a href="info_libro.php?libro=<?= htmlspecialchars($libro['Lib_nom']) ?>" class="btn">Ver más información</a>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="flecha izquierda" onclick="moverLibros('izquierda', 'lista-deseos')">❮</div>
            <div class="flecha derecha" onclick="moverLibros('derecha', 'lista-deseos')">❯</div>
        </div>
    </div>
</main>
<script>
function toggleReportar() {
    var x = document.getElementById("reportar-opcion");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}

function moverLibros(direccion, clase) {
    const contenedor = document.querySelector(`.${clase} .libros`);
    if (direccion === 'derecha') {
        contenedor.scrollBy({ left: 200, behavior: 'smooth' });
    } else {
        contenedor.scrollBy({ left: -200, behavior: 'smooth' });
    }
}
</script>
</body>
</html>
