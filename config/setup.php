<?php
// Cargar variables de entorno desde archivo .env si existe
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
        }
    }
}

function nombre_perfil($id) {
    $nombres = [
        1 => 'Administrador',
        2 => 'Propietario',
        3 => 'Gestor Inmobiliario',
    ];
    return $nombres[$id] ?? 'Desconocido';
}

function conectar() {
    $servername = $_ENV['DB_HOST'] ?? "localhost";
    $username   = $_ENV['DB_USER'] ?? "root";
    $password   = $_ENV['DB_PASSWORD'] ?? "";
    $dbname     = $_ENV['DB_NAME'] ?? "pnks";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}
?>
