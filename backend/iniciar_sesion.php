<?php
session_start();
require_once __DIR__ . "/../config/setup.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Validación básica: campos vacíos
    if (empty($email) || empty($password)) {
        header("Location: ../iniciosesion.php?error=1");
        exit();
    }

    $conn = conectar();

    // Buscar usuario por email usando prepared statement
    $sql = "SELECT * FROM usuarios WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        // Email no registrado
        header("Location: ../iniciosesion.php?error=2");
        exit();
    }

    $datos = mysqli_fetch_array($result);

    // Validar contraseña con password_verify
    if (!password_verify($password, $datos['clave'])) {
        header("Location: ../iniciosesion.php?error=3");
        exit();
    }

    // Validar estado (TINYINT: 1 = activo, 0 = inactivo)
    if ((int)$datos['estado'] !== 1) {
        header("Location: ../iniciosesion.php?error=4");
        exit();
    }

    // Si todo está OK → iniciar sesión
    $_SESSION['usuario_sesion'] = $datos['nombre'];
    $_SESSION['foto_sesion']    = $datos['foto'];
    $nombres_perfil = [1 => 'Administrador', 2 => 'Propietario', 3 => 'Gestor Inmobiliario'];
    $_SESSION['nombre_perfil']  = $nombres_perfil[$datos['idperfil']] ?? 'Desconocido';
    $_SESSION['id']            = $datos['id'];
    $_SESSION['idperfil']      = $datos['idperfil'];

    header("Location: ../dashboard.php");
    exit();
}
?>