<?php
session_start();
require_once __DIR__ . "/../config/setup.php";
require_once __DIR__ . "/../config/session_config.php";

// Detectar si es petición AJAX (fetch)
$es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Validación básica: campos vacíos
    if (empty($email) || empty($password)) {
        if ($es_ajax) {
            echo json_encode(['success' => false, 'message' => 'Debes completar todos los campos.']);
        } else {
            header("Location: ../iniciosesion.php?error=1");
        }
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
        if ($es_ajax) {
            echo json_encode(['success' => false, 'message' => 'El correo ingresado no está registrado en el sistema.']);
        } else {
            header("Location: ../iniciosesion.php?error=2");
        }
        exit();
    }

    $datos = mysqli_fetch_array($result);

    // Validar contraseña con password_verify
    if (!password_verify($password, $datos['clave'])) {
        if ($es_ajax) {
            echo json_encode(['success' => false, 'message' => 'La contraseña ingresada es incorrecta.']);
        } else {
            header("Location: ../iniciosesion.php?error=3");
        }
        exit();
    }

    // Validar estado (TINYINT: 1 = activo, 0 = inactivo)
    if ((int)$datos['estado'] !== 1) {
        if ($es_ajax) {
            echo json_encode(['success' => false, 'message' => 'Tu cuenta está inactiva. Contacta al administrador.']);
        } else {
            header("Location: ../iniciosesion.php?error=4");
        }
        exit();
    }

    // Si todo está OK → iniciar sesión
    $_SESSION['usuario_sesion'] = $datos['nombre'];
    $_SESSION['foto_sesion']    = $datos['foto'];
    $nombres_perfil = [1 => 'Administrador', 2 => 'Propietario', 3 => 'Gestor Inmobiliario'];
    $_SESSION['nombre_perfil']  = $nombres_perfil[$datos['idperfil']] ?? 'Desconocido';
    $_SESSION['id']            = $datos['id'];
    $_SESSION['idperfil']      = $datos['idperfil'];

    if ($es_ajax) {
        echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso', 'redirect' => '../dashboard.php', 'nombre' => $datos['nombre']]);
    } else {
        header("Location: ../dashboard.php");
    }
    exit();
}
?>