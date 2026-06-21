<?php
function conectar() {
    $servername = "localhost";
    $username   = "pnk_admin";     
    $password   = "Admin123!";       
    $dbname     = "pnks";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}
?>