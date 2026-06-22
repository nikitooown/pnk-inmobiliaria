<?php
session_start();
include __DIR__ . "/../config/setup.php";
include __DIR__ . "/../config/session_config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut             = $_POST['rut'];
    $nombre          = $_POST['nombre'];
    $apellido        = $_POST['apellido'];
    $fecha_nacimiento= $_POST['fecha_nacimiento'];
    $genero          = $_POST['genero'];
    $telefono        = $_POST['telefono'];
    $email           = $_POST['email'];
    $clave           = $_POST['pswd'];

    $numero_bienes_raices = $_POST['numero_bienes_raices'] ?? NULL;
    if ($numero_bienes_raices !== NULL) {
        $numero_bienes_raices = trim($numero_bienes_raices);
        if ($numero_bienes_raices === '') {
            $numero_bienes_raices = NULL;
        }
    }

    // Detectar qué formulario se envió
    if (isset($_POST['registrar_propietario'])) {
        $idperfil = 2; // Propietario
        $certificado = NULL;
    } elseif (isset($_POST['registrar_gestor'])) {
        $numero_bienes_raices = NULL; // No corresponde a gestor
        $idperfil = 3; // Gestor Inmobiliario
        
        // Manejo de certificado para Gestores
        $certificado = NULL;
        
        // Crear carpeta de certificados si no existe
        $carpeta_certificados = __DIR__ . "/../uploads/certificados/";
        if (!is_dir($carpeta_certificados)) {
            mkdir($carpeta_certificados, 0777, true);
        }
        
        // Verificar si se subió un archivo de certificado
        if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] !== UPLOAD_ERR_NO_FILE) {
            $archivo = $_FILES['certificado'];
            
            // Validar que no hay error en la subida
            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                $error_msg = "Error al subir el archivo.";
                if ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE) {
                    $error_msg = "El archivo es demasiado grande (máximo 5MB).";
                }
                // Guardar error en sesión para redirigir
                $_SESSION['error_certificado'] = $error_msg;
                header("Location: ../registro.php?error=cert_1");
                exit();
            }
            
            // Obtener información del archivo
            $nombre_archivo = $archivo['name'];
            $tmp_name = $archivo['tmp_name'];
            $tamaño = $archivo['size'];
            
            // Validar tamaño máximo (5MB = 5242880 bytes)
            if ($tamaño > 5242880) {
                $_SESSION['error_certificado'] = "El archivo es demasiado grande (máximo 5MB).";
                header("Location: ../registro.php?error=cert_2");
                exit();
            }
            
            // Validar extensión
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            
            if (!in_array($extension, $extensiones_permitidas)) {
                $_SESSION['error_certificado'] = "Formato no permitido. Solo se aceptan: PDF, JPG, JPEG, PNG.";
                header("Location: ../registro.php?error=cert_3");
                exit();
            }
            
            // Generar nombre único para el archivo
            $nombre_unico = uniqid() . "." . $extension;
            $ruta_destino = $carpeta_certificados . $nombre_unico;
            
            // Mover archivo subido
            if (move_uploaded_file($tmp_name, $ruta_destino)) {
                // Guardar ruta relativa en la base de datos
                $certificado = "uploads/certificados/" . $nombre_unico;
            } else {
                $_SESSION['error_certificado'] = "Error al guardar el archivo.";
                header("Location: ../registro.php?error=cert_4");
                exit();
            }
        }
    } else {
        die("Error: formulario no reconocido.");
    }

    $conexion = conectar();

    // Hash de la contraseña
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Estado inicial: 0 = inactivo (requiere activación por administrador)
    $estado = 0;

    // Insertar usuario como inactivo usando prepared statement
    $sql = "INSERT INTO usuarios 
            (rut, nombre, apellido, fecha_nacimiento, genero, telefono, email, clave, estado, fecha_hora, foto, idperfil, certificado, numero_bienes_raices) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'default.png', ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);
    // Tipos: s=string, i=int
    // 12 placeholders: rut, nombre, apellido, fecha_nacimiento, genero, telefono, email, clave_hash, estado, idperfil, certificado, numero_bienes_raices
    mysqli_stmt_bind_param($stmt, "ssssssssiiss", $rut, $nombre, $apellido, $fecha_nacimiento, $genero, $telefono, $email, $clave_hash, $estado, $idperfil, $certificado, $numero_bienes_raices);

    try {
        mysqli_stmt_execute($stmt);
        $_SESSION['success_registro'] = true;
        header("Location: ../iniciosesion.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $errorCode = $e->getCode();
        $errorMsg = 'Error al registrarte. Por favor intenta más tarde.';
        
        if ($errorCode == 1062) {
            // Error de duplicado: RUT o email ya existen
            if (stripos($e->getMessage(), 'rut') !== false) {
                $errorMsg = 'El RUT ingresado ya está registrado.';
            } else {
                $errorMsg = 'El email ingresado ya está registrado.';
            }
        }
        
        error_log('Error en registro de usuario: ' . $e->getMessage());
        
        $_SESSION['error_registro'] = $errorMsg;
        header("Location: ../registro.php?error=db_error");
        exit();
    } finally {
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
    }
}
?>
