<?php
include ("../config/setup.php");

// Eliminar registros corruptos donde nombre es NULL o vacío
$sql = "DELETE FROM usuarios WHERE nombre IS NULL OR nombre = '' OR nombre = ' '";
$result = mysqli_query(conectar(), $sql);

$affected = mysqli_affected_rows(conectar());
echo json_encode(['success' => true, 'mensaje' => "Se eliminaron $affected registros corruptos."]);
?>