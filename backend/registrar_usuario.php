<?php
include __DIR__ . "/../config/setup.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut             = $_POST['rut'];
    $nombre          = $_POST['nombre'];
    $apellido        = $_POST['apellido'];
    $fecha_nacimiento= $_POST['fecha_nacimiento'];
    $genero          = $_POST['genero'];
    $telefono        = $_POST['telefono'];
    $email           = $_POST['email'];
    $clave           = $_POST['pswd'];

    // Detectar qué formulario se envió
    if (isset($_POST['registrar_propietario'])) {
        $idperfil = 2; // Propietario
    } elseif (isset($_POST['registrar_gestor'])) {
        $idperfil = 3; // Gestor Inmobiliario
    } else {
        die("Error: formulario no reconocido.");
    }

    $conexion = conectar();

    // Hash de la contraseña
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Insertar usuario como inactivo usando prepared statement
    $sql = "INSERT INTO usuarios 
            (rut, nombre, apellido, fecha_nacimiento, genero, telefono, email, clave, estado, fecha_hora, foto, idperfil) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'inactivo', NOW(), 'default.png', ?)";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssi", $rut, $nombre, $apellido, $fecha_nacimiento, $genero, $telefono, $email, $clave_hash, $idperfil);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../iniciosesion.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
}
?>