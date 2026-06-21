<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/../config/setup.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre  = trim($_POST['nombre'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // Validar que los campos no estén vacíos
    $errores = [];
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errores[] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    }
    if (empty($mensaje)) {
        $errores[] = "El mensaje es obligatorio.";
    } elseif (strlen($mensaje) > 2000) {
        $errores[] = "El mensaje no puede exceder los 2000 caracteres.";
    }

    if (!empty($errores)) {
        echo json_encode(["success" => false, "message" => implode(" ", $errores)]);
        exit();
    }

    try {
        $conexion = conectar();

        // Insertar mensaje en tabla mensajes_contacto usando prepared statement
        $sql = "INSERT INTO mensajes_contacto (nombre, email, mensaje, fecha_envio) 
                VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $mensaje);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            echo json_encode([
                "success" => true,
                "message" => "Mensaje enviado correctamente. Un gestor se pondrá en contacto contigo."
            ]);
            exit();
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            echo json_encode(["success" => false, "message" => "Error al enviar el mensaje. Intenta nuevamente."]);
            exit();
        }
    } catch (\Throwable $e) {
        error_log('Error en enviar_contacto: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
        exit();
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit();
}
?>
