<?php
header('Content-Type: application/json');
include_once("../config/setup.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$id_propiedad = (int) ($_POST['id_propiedad'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if ($id_propiedad <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de propiedad no válido.']);
    exit();
}

if (empty($nombre) || empty($telefono)) {
    echo json_encode(['success' => false, 'message' => 'Nombre y teléfono son obligatorios.']);
    exit();
}

// Validar formato de teléfono chileno
$telefonoLimpio = preg_replace('/\s+/g', '', $telefono);
if (!preg_match('/^\+?56?9\d{8}$/', $telefonoLimpio)) {
    echo json_encode(['success' => false, 'message' => 'Formato de teléfono inválido. Use: +569XXXXXXXX']);
    exit();
}

$conexion = conectar();

$sql = "INSERT INTO visitas (id_propiedad, nombre, telefono) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "iss", $id_propiedad, $nombre, $telefono);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Solicitud de visita registrada con éxito. Un gestor se pondrá en contacto contigo a la brevedad.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la solicitud. Intenta nuevamente.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);