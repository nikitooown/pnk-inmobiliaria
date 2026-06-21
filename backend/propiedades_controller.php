<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include_once("../config/setup.php");

// Solo usuarios autenticados pueden gestionar propiedades
if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "Acceso denegado. Debes iniciar sesión."]);
    exit();
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'crear':
        crear();
        break;
    case 'listar':
        listar();
        break;
    case 'obtener':
        obtener();
        break;
    case 'eliminar':
        eliminar();
        break;
    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
        break;
}

// ============================================================
// FUNCIÓN: CREAR PROPIEDAD
// ============================================================
function crear()
{
    $conexion = conectar();
    $id_propietario = (int) $_SESSION['id'];

    // --- Validar cantidad de imágenes (1 a 10) ---
    if (!isset($_FILES['imagenes'])) {
        echo json_encode(["success" => false, "message" => "Debes subir al menos 1 imagen."]);
        exit();
    }

    $archivos = $_FILES['imagenes'];
    $total = is_array($archivos['name']) ? count($archivos['name']) : 1;

    if ($total < 1 || $total > 10) {
        echo json_encode(["success" => false, "message" => "La propiedad debe tener entre 1 y 10 imágenes."]);
        exit();
    }

    // --- Validar extensiones ---
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (is_array($archivos['name'])) {
        $nombres = $archivos['name'];
        $tmp_names = $archivos['tmp_name'];
        $errores = $archivos['error'];
    } else {
        $nombres = [$archivos['name']];
        $tmp_names = [$archivos['tmp_name']];
        $errores = [$archivos['error']];
    }

    for ($i = 0; $i < $total; $i++) {
        $ext = strtolower(pathinfo($nombres[$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $extensiones_permitidas)) {
            echo json_encode([
                "success" => false,
                "message" => "Formato no válido en el archivo '" . $nombres[$i] . "'. Solo se permiten: JPG, JPEG, PNG, WEBP."
            ]);
            exit();
        }
        if ($errores[$i] !== UPLOAD_ERR_OK) {
            echo json_encode([
                "success" => false,
                "message" => "Error al subir el archivo '" . $nombres[$i] . "'. Código: " . $errores[$i]
            ]);
            exit();
        }
    }

    // --- Recibir datos del formulario ---
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo        = trim($_POST['tipo'] ?? '');
    $provincia   = trim($_POST['provincia'] ?? '');
    $comuna      = trim($_POST['comuna'] ?? '');
    $direccion   = trim($_POST['direccion'] ?? '');
    $precio      = (float) ($_POST['precio'] ?? 0);
    $habitaciones = (int) ($_POST['habitaciones'] ?? 0);
    $banos       = (int) ($_POST['banos'] ?? 0);
    $m2          = (int) ($_POST['m2'] ?? 0);

    if (empty($titulo) || empty($tipo)) {
        echo json_encode(["success" => false, "message" => "Título y tipo son obligatorios."]);
        exit();
    }

    // --- Insertar propiedad ---
    $sql = "INSERT INTO propiedades (id_propietario, titulo, descripcion, tipo, provincia, comuna, direccion, precio, habitaciones, banos, m2, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Activa')";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "issssssiiii", $id_propietario, $titulo, $descripcion, $tipo, $provincia, $comuna, $direccion, $precio, $habitaciones, $banos, $m2);
    mysqli_stmt_execute($stmt);
    $id_propiedad = mysqli_insert_id($conexion);
    mysqli_stmt_close($stmt);

    if (!$id_propiedad) {
        echo json_encode(["success" => false, "message" => "Error al crear la propiedad: " . mysqli_error($conexion)]);
        exit();
    }

    // --- Subir imágenes ---
    $directorio = "../img/propiedades/" . $id_propiedad . "/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $primera = true; // La primera imagen será la principal

    for ($i = 0; $i < $total; $i++) {
        $ext = strtolower(pathinfo($nombres[$i], PATHINFO_EXTENSION));
        $nombre_unico = uniqid() . "." . $ext;
        $ruta_fisica = $directorio . $nombre_unico;

        if (move_uploaded_file($tmp_names[$i], $ruta_fisica)) {
            $ruta_bd = "img/propiedades/" . $id_propiedad . "/" . $nombre_unico;
            $es_principal = $primera ? 1 : 0;
            $primera = false;

            $sql_img = "INSERT INTO fotografias (id_propiedad, ruta, nombre_original, es_principal)
                        VALUES (?, ?, ?, ?)";
            $stmt_img = mysqli_prepare($conexion, $sql_img);
            mysqli_stmt_bind_param($stmt_img, "issi", $id_propiedad, $ruta_bd, $nombres[$i], $es_principal);
            mysqli_stmt_execute($stmt_img);
            mysqli_stmt_close($stmt_img);
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "Propiedad creada exitosamente con $total imágenes.",
        "id_propiedad" => $id_propiedad
    ]);
}

// ============================================================
// FUNCIÓN: LISTAR PROPIEDADES (con foto principal)
// ============================================================
function listar()
{
    $conexion = conectar();

    $sql = "SELECT p.*, 
                   (SELECT f.ruta FROM fotografias f WHERE f.id_propiedad = p.id AND f.es_principal = 1 LIMIT 1) AS foto_principal
            FROM propiedades p
            WHERE p.estado = 'Activa'
            ORDER BY p.fecha_creacion DESC";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $propiedades = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['precio'] = (float) $row['precio'];
        $row['habitaciones'] = (int) $row['habitaciones'];
        $row['banos'] = (int) $row['banos'];
        $row['m2'] = (int) $row['m2'];
        $propiedades[] = $row;
    }

    mysqli_stmt_close($stmt);

    echo json_encode(["success" => true, "data" => $propiedades]);
}

// ============================================================
// FUNCIÓN: OBTENER PROPIEDAD POR ID
// ============================================================
function obtener()
{
    $conexion = conectar();
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID no válido."]);
        exit();
    }

    // Obtener datos de la propiedad
    $sql = "SELECT * FROM propiedades WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $propiedad = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$propiedad) {
        echo json_encode(["success" => false, "message" => "Propiedad no encontrada."]);
        exit();
    }

    // Obtener fotografías
    $sql_fotos = "SELECT id, ruta, es_principal FROM fotografias WHERE id_propiedad = ? ORDER BY es_principal DESC, id ASC";
    $stmt_fotos = mysqli_prepare($conexion, $sql_fotos);
    mysqli_stmt_bind_param($stmt_fotos, "i", $id);
    mysqli_stmt_execute($stmt_fotos);
    $result_fotos = mysqli_stmt_get_result($stmt_fotos);

    $fotografias = [];
    while ($foto = mysqli_fetch_assoc($result_fotos)) {
        $foto['es_principal'] = (int) $foto['es_principal'];
        $fotografias[] = $foto;
    }
    mysqli_stmt_close($stmt_fotos);

    $propiedad['fotografias'] = $fotografias;
    $propiedad['precio'] = (float) $propiedad['precio'];
    $propiedad['habitaciones'] = (int) $propiedad['habitaciones'];
    $propiedad['banos'] = (int) $propiedad['banos'];
    $propiedad['m2'] = (int) $propiedad['m2'];

    echo json_encode(["success" => true, "data" => $propiedad]);
}

// ============================================================
// FUNCIÓN: ELIMINAR PROPIEDAD
// ============================================================
function eliminar()
{
    $conexion = conectar();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID no válido."]);
        exit();
    }

    // Eliminar fotos del servidor
    $sql_fotos = "SELECT ruta FROM fotografias WHERE id_propiedad = ?";
    $stmt_fotos = mysqli_prepare($conexion, $sql_fotos);
    mysqli_stmt_bind_param($stmt_fotos, "i", $id);
    mysqli_stmt_execute($stmt_fotos);
    $result_fotos = mysqli_stmt_get_result($stmt_fotos);

    while ($foto = mysqli_fetch_assoc($result_fotos)) {
        $ruta_completa = "../" . $foto['ruta'];
        if (file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }
    }
    mysqli_stmt_close($stmt_fotos);

    // Eliminar carpeta de imágenes si existe
    $directorio = "../img/propiedades/" . $id . "/";
    if (is_dir($directorio)) {
        array_map('unlink', glob($directorio . "*"));
        rmdir($directorio);
    }

    // Eliminar registros de fotografías (cascade lo haría, pero por seguridad lo hacemos explícito)
    $sql_del_fotos = "DELETE FROM fotografias WHERE id_propiedad = ?";
    $stmt_del_fotos = mysqli_prepare($conexion, $sql_del_fotos);
    mysqli_stmt_bind_param($stmt_del_fotos, "i", $id);
    mysqli_stmt_execute($stmt_del_fotos);
    mysqli_stmt_close($stmt_del_fotos);

    // Eliminar propiedad
    $sql = "DELETE FROM propiedades WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_affected_rows($conexion) > 0) {
        echo json_encode(["success" => true, "message" => "Propiedad eliminada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontró la propiedad o no tienes permisos."]);
    }

    mysqli_stmt_close($stmt);
}