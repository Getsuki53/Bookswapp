<?php
// filepath: /c:/xampp/htdocs/Bookswap/buscarLibro.php

function buscarLibro($conn, $titulo = '') {
    if (!empty($titulo)) {
        $titulo = $conn->real_escape_string($titulo);

        // Consultar libros que contengan el título
        $sql_buscar = "SELECT libro.*, lector.Lec_mail FROM libro JOIN lector ON libro.Lib_usu_correo = lector.Lec_mail WHERE libro.Lib_nom LIKE ? AND libro.Oculto = 1";
        $stmt_buscar = $conn->prepare($sql_buscar);
        $titulo_param = "%$titulo%";
        $stmt_buscar->bind_param("s", $titulo_param);
        $stmt_buscar->execute();
        $result_buscar = $stmt_buscar->get_result();
        return $result_buscar->fetch_all(MYSQLI_ASSOC);
    } else {
        // Consulta para obtener todos los libros
        $sql = "SELECT libro.*, lector.Lec_mail FROM libro JOIN lector ON libro.Lib_usu_correo = lector.Lec_mail WHERE libro.Oculto = 1";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>