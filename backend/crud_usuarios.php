<?php
session_start();
include ("../config/setup.php");

// Validación: solo Administrador puede acceder
if ($_SESSION['nombre_perfil'] !== 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit();
}

// Solo aceptar POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

switch($_POST['accion']){
    case "insertar": echo json_encode(insertar()); exit();
    case "modificar": echo json_encode(modificar()); exit();
    case "eliminar": echo json_encode(eliminar()); exit();
    case "cancelar": echo json_encode(['success' => true, 'message' => 'Operación cancelada.']); exit();
    default: echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']); exit();
}

function insertar() {
    $conexion = conectar();

    $rut       = $_POST['frm_rut'] ?? '';
    $nombre    = $_POST['frm_nombre'] ?? '';
    $apellido  = $_POST['frm_apellido'] ?? '';
    $usuario   = $_POST['frmusuario'] ?? '';
    $estado    = $_POST['frm_estado'] ?? '';
    $idperfil  = $_POST['frm_idperfil'] ?? '';

    // Validación estricta de campos obligatorios
    if (empty($rut) || empty($nombre) || empty($usuario)) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios vacíos']);
        exit;
    }

    // Asignar contraseña encriptada con hash del RUT
    $clave_hash = password_hash($rut, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (rut, nombre, apellido, email, clave, estado, fecha_hora, idperfil) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $rut, $nombre, $apellido, $usuario, $clave_hash, $estado, $idperfil);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        return ['success' => true, 'message' => 'Usuario guardado correctamente. Contraseña inicial: RUT del usuario.'];
    } catch (mysqli_sql_exception $e) {
        $errorCode = $e->getCode();
        $errorMessage = 'Error al crear el usuario.';
        
        if ($errorCode == 1062) {
            $errorMessage = 'Ya existe un usuario con ese RUT o correo electrónico.';
        }
        
        error_log('Error en insertar usuario: ' . $e->getMessage());
        mysqli_close($conexion);
        return ['success' => false, 'message' => $errorMessage];
    }
}

function modificar() {
    $conexion = conectar();

    $id       = $_POST['id'];
    $rut      = $_POST['frm_rut'];
    $nombre   = $_POST['frm_nombre'];
    $apellido = $_POST['frm_apellido'];
    $usuario  = $_POST['frmusuario'];
    $estado   = $_POST['frm_estado'];
    $idperfil = $_POST['frm_idperfil'];

    try {
        $sql = "UPDATE usuarios 
                SET rut=?, nombre=?, apellido=?, email=?, estado=?, idperfil=?
                WHERE id=?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sssssii", $rut, $nombre, $apellido, $usuario, $estado, $idperfil, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        return ['success' => true, 'message' => 'Usuario modificado correctamente.'];
    } catch (mysqli_sql_exception $e) {
        $errorCode = $e->getCode();
        $errorMessage = 'Error al modificar el usuario.';
        
        if ($errorCode == 1062) {
            $errorMessage = 'Ya existe un usuario con ese RUT o correo electrónico.';
        }
        
        error_log('Error en modificar usuario: ' . $e->getMessage());
        mysqli_close($conexion);
        return ['success' => false, 'message' => $errorMessage];
    }
}

function eliminar() {
    $conexion = conectar();
    $id = $_POST['id'];

    try {
        $sql = "DELETE FROM usuarios WHERE id=?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        return ['success' => true, 'message' => 'Usuario eliminado correctamente.'];
    } catch (mysqli_sql_exception $e) {
        $errorMessage = 'Error al eliminar el usuario. Intenta nuevamente.';
        error_log('Error en eliminar usuario: ' . $e->getMessage());
        mysqli_close($conexion);
        return ['success' => false, 'message' => $errorMessage];
    }
}
?>