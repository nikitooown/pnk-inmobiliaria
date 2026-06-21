<?php
function conectar() {
    $servername = "localhost";
    $username   = "root"; // usuario por defecto en WAMP
    $password   = "";     // contraseña vacía por defecto
    $dbname     = "pnks";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}
?>