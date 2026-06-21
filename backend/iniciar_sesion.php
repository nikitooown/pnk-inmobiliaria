<?php
session_start();
include ("../config/setup.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Validación básica: campos vacíos
    if (empty($email) || empty($password)) {
        header("Location: ../iniciosesion.php?error=1");
        exit();
    }

    $conexion = conectar();

    // Buscar usuario por email usando prepared statement
    $sql = "SELECT * FROM usuarios WHERE email=?";
    $stmt = mysqli_prepare($conexion, $sql);
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
    $_SESSION['nombre_perfil']  = nombre_perfil($datos['idperfil']);
    $_SESSION['id']            = $datos['id'];
    $_SESSION['idperfil']      = $datos['idperfil'];

    header("Location: ../dashboard.php");
    exit();
}
?>