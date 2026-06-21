<?php
session_start();
include ("../config/setup.php");

// Validación: solo Administrador puede acceder
if ($_SESSION['nombre_perfil'] !== 'Administrador') {
    header("Location: error.html");
    exit();
}

switch($_POST['accion']){
    case "guardar": insertar(); break;
    case "modificar": modificar(); break;
    case "eliminar": eliminar(); break;
    case "cancelar": cancelar(); break;
}

function insertar() {
    $conexion = conectar();

    $rut       = $_POST['frm_rut'];
    $nombre    = $_POST['frm_nombre'];
    $apellido  = $_POST['frm_apellido'];
    $usuario   = $_POST['frmusuario'];
    $estado    = $_POST['frm_estado'];
    $idperfil  = $_POST['frm_idperfil'];

    // Asignar contraseña encriptada con hash del RUT
    $clave_hash = password_hash($rut, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (rut, nombre, apellido, email, clave, estado, fecha_hora, idperfil) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $rut, $nombre, $apellido, $usuario, $clave_hash, $estado, $idperfil);
    mysqli_stmt_execute($stmt) or die("Error en inserción: " . mysqli_error($conexion));
    mysqli_stmt_close($stmt);
    
    // SweetAlert2 feedback en lugar de header directo
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Usuario guardado',
            text: 'El usuario se ha registrado correctamente. Contraseña inicial: RUT del usuario.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'frm_usuarios.php';
        });
    </script>";
    exit();
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

    $sql = "UPDATE usuarios 
            SET rut=?, nombre=?, apellido=?, email=?, estado=?, idperfil=?
            WHERE id=?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssssii", $rut, $nombre, $apellido, $usuario, $estado, $idperfil, $id);
    mysqli_stmt_execute($stmt) or die("Error en modificación: " . mysqli_error($conexion));
    mysqli_stmt_close($stmt);
    
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Usuario modificado',
            text: 'Los datos del usuario se actualizaron correctamente.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'frm_usuarios.php';
        });
    </script>";
    exit();
}

function eliminar() {
    $conexion = conectar();
    $id = $_POST['id'];

    $sql = "DELETE FROM usuarios WHERE id=?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt) or die("Error en eliminación: " . mysqli_error($conexion));
    mysqli_stmt_close($stmt);
    
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Usuario eliminado',
            text: 'El usuario fue eliminado del sistema.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'frm_usuarios.php';
        });
    </script>";
    exit();
}

function cancelar() {   
    header("Location: frm_usuarios.php");
}
?>