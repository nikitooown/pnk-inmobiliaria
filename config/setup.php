<?php
function conectar() {
    $servername = $_ENV['DB_HOST'] ?? "localhost";
    $username   = $_ENV['DB_USER'] ?? "pnk_user";
    $password   = $_ENV['DB_PASSWORD'] ?? "pass_segura";
    $dbname     = $_ENV['DB_NAME'] ?? "pnks";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}