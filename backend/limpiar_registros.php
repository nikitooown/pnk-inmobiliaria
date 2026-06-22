<?php
include ("../config/setup.php");

// Eliminar registros corruptos donde nombre es NULL o vacío
$conexion = conectar();
$sql = "DELETE FROM usuarios WHERE nombre IS NULL OR nombre = '' OR nombre = ' '";

if (mysqli_query($conexion, $sql)) {
    $affected = mysqli_affected_rows($conexion);
    mysqli_close($conexion);
    echo json_encode(['success' => true, 'mensaje' => "Se eliminaron $affected registros corruptos."]);
} else {
    error_log('Error en limpiar_registros: ' . mysqli_error($conexion));
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'mensaje' => 'Error al limpiar registros.']);
}
?>
