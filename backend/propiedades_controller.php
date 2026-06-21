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
    case 'editar':
        editar();
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
    $sector      = trim($_POST['sector'] ?? '');
    $direccion   = trim($_POST['direccion'] ?? '');
    $precio      = (float) ($_POST['precio'] ?? 0);
    $uf          = (float) ($_POST['uf'] ?? 0);
    $m2_terreno  = (int) ($_POST['m2_terreno'] ?? 0);
    $m2_construido = (int) ($_POST['m2_construido'] ?? 0);
    $habitaciones = (int) ($_POST['habitaciones'] ?? 0);
    $banos       = (int) ($_POST['banos'] ?? 0);
    $bodega          = isset($_POST['bodega']) ? 1 : 0;
    $estacionamiento = isset($_POST['estacionamiento']) ? 1 : 0;
    $logia           = isset($_POST['logia']) ? 1 : 0;
    $cocina_amoblada = isset($_POST['cocina_amoblada']) ? 1 : 0;
    $antejardin      = isset($_POST['antejardin']) ? 1 : 0;
    $patio_trasero   = isset($_POST['patio_trasero']) ? 1 : 0;
    $piscina         = isset($_POST['piscina']) ? 1 : 0;

    if (empty($titulo) || empty($tipo)) {
        echo json_encode(["success" => false, "message" => "Título y tipo son obligatorios."]);
        exit();
    }

    // --- Insertar propiedad ---
    try {
        $sql = "INSERT INTO propiedades (idpropietario, titulo, descripcion, tipo, provincia, comuna, sector, direccion, precio, uf, m2_terreno, m2_construido, habitaciones, banos, bodega, estacionamiento, logia, cocina_amoblada, antejardin, patio_trasero, piscina, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Publicada')";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "issssssddiiiiiiiiiii", 
            $id_propietario, $titulo, $descripcion, $tipo, $provincia, $comuna, $sector, $direccion, 
            $precio, $uf, $m2_terreno, $m2_construido, $habitaciones, $banos, 
            $bodega, $estacionamiento, $logia, $cocina_amoblada, $antejardin, $patio_trasero, $piscina);
        mysqli_stmt_execute($stmt);
        $id_propiedad = mysqli_insert_id($conexion);
        mysqli_stmt_close($stmt);

        if (!$id_propiedad) {
            echo json_encode(["success" => false, "message" => "No se pudo obtener el ID de la propiedad."]);
            exit();
        }
    } catch (\Throwable $e) {
        error_log('Error al crear propiedad: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
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

            try {
                $sql_img = "INSERT INTO fotografias (id_propiedad, ruta, nombre_original, es_principal)
                            VALUES (?, ?, ?, ?)";
                $stmt_img = mysqli_prepare($conexion, $sql_img);
                mysqli_stmt_bind_param($stmt_img, "issi", $id_propiedad, $ruta_bd, $nombres[$i], $es_principal);
                mysqli_stmt_execute($stmt_img);
                mysqli_stmt_close($stmt_img);
            } catch (\Throwable $e) {
                error_log('Error al insertar fotografía: ' . $e->getMessage());
                // Continuar con la siguiente foto si hay error en una
                continue;
            }
        }
    }

    // Cerrar conexión
    mysqli_close($conexion);

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

    // Control de Acceso Estricto: Si es Propietario, solo ve sus propiedades
    $es_propietario = ($_SESSION['nombre_perfil'] ?? '') === 'Propietario';

    if ($es_propietario) {
        $sql = "SELECT p.*, 
                       (SELECT f.ruta FROM fotografias f WHERE f.id_propiedad = p.id AND f.es_principal = 1 LIMIT 1) AS foto_principal
                FROM propiedades p
                WHERE p.idpropietario = ?
                ORDER BY p.fecha_publicacion DESC";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    } else {
        $sql = "SELECT p.*, 
                       (SELECT f.ruta FROM fotografias f WHERE f.id_propiedad = p.id AND f.es_principal = 1 LIMIT 1) AS foto_principal
                FROM propiedades p
                ORDER BY p.fecha_publicacion DESC";
        $stmt = mysqli_prepare($conexion, $sql);
    }

    try {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $propiedades = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['precio'] = (float) $row['precio'];
            $row['uf'] = (float) ($row['uf'] ?? 0);
            $row['habitaciones'] = (int) $row['habitaciones'];
            $row['banos'] = (int) $row['banos'];
            $row['m2_terreno'] = (int) ($row['m2_terreno'] ?? 0);
            $row['m2_construido'] = (int) ($row['m2_construido'] ?? 0);
            $propiedades[] = $row;
        }

        mysqli_stmt_close($stmt);

        echo json_encode(["success" => true, "data" => $propiedades]);
    } catch (\Throwable $e) {
        error_log('Error al listar propiedades: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
    }
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

    try {
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
        $propiedad['uf'] = (float) ($propiedad['uf'] ?? 0);
        $propiedad['habitaciones'] = (int) $propiedad['habitaciones'];
        $propiedad['banos'] = (int) $propiedad['banos'];
        $propiedad['m2_terreno'] = (int) ($propiedad['m2_terreno'] ?? 0);
        $propiedad['m2_construido'] = (int) ($propiedad['m2_construido'] ?? 0);

        echo json_encode(["success" => true, "data" => $propiedad]);
    } catch (\Throwable $e) {
        error_log('Error al obtener propiedad: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
    }
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

    try {
        // Validar que la propiedad pertenece al usuario o que es administrador
        $sql_verificar = "SELECT idpropietario FROM propiedades WHERE id = ?";
        $stmt_verificar = mysqli_prepare($conexion, $sql_verificar);
        mysqli_stmt_bind_param($stmt_verificar, "i", $id);
        mysqli_stmt_execute($stmt_verificar);
        $result_verificar = mysqli_stmt_get_result($stmt_verificar);
        $propiedad = mysqli_fetch_assoc($result_verificar);
        mysqli_stmt_close($stmt_verificar);

        if (!$propiedad) {
            echo json_encode(["success" => false, "message" => "La propiedad no existe."]);
            exit();
        }

        // Verificar permiso: debe ser el propietario o administrador
        if ($propiedad['idpropietario'] != $_SESSION['id'] && $_SESSION['idperfil'] != 1) {
            echo json_encode(["success" => false, "message" => "No tienes permiso para eliminar esta propiedad."]);
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
            echo json_encode(["success" => false, "message" => "Error al eliminar la propiedad."]);
        }

        mysqli_stmt_close($stmt);
    } catch (\Throwable $e) {
        error_log('Error al eliminar propiedad: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
    }
}

// ============================================================
// FUNCIÓN: EDITAR PROPIEDAD
// ============================================================
function editar()
{
    $conexion = conectar();
    $id = (int) ($_POST['editId'] ?? $_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID no válido."]);
        exit();
    }

    try {
        // Validar que la propiedad pertenece al usuario o que es administrador
        $sql_verificar = "SELECT idpropietario FROM propiedades WHERE id = ?";
        $stmt_verificar = mysqli_prepare($conexion, $sql_verificar);
        mysqli_stmt_bind_param($stmt_verificar, "i", $id);
        mysqli_stmt_execute($stmt_verificar);
        $result_verificar = mysqli_stmt_get_result($stmt_verificar);
        $propiedad = mysqli_fetch_assoc($result_verificar);
        mysqli_stmt_close($stmt_verificar);

        if (!$propiedad) {
            echo json_encode(["success" => false, "message" => "La propiedad no existe."]);
            exit();
        }

        // Verificar permiso: debe ser el propietario o administrador
        if ($propiedad['idpropietario'] != $_SESSION['id'] && $_SESSION['idperfil'] != 1) {
            echo json_encode(["success" => false, "message" => "No tienes permiso para editar esta propiedad."]);
            exit();
        }

        // Obtener datos del formulario (usar prefijo 'edit' como vienen del modal)
        $titulo       = trim($_POST['editTitulo'] ?? '');
        $descripcion  = trim($_POST['editDescripcion'] ?? '');
        $tipo         = trim($_POST['editTipo'] ?? '');
        $provincia    = trim($_POST['editProvincia'] ?? '');
        $comuna       = trim($_POST['editComuna'] ?? '');
        $sector       = trim($_POST['editSector'] ?? '');
        $direccion    = trim($_POST['editDireccion'] ?? '');
        $precio       = (float) ($_POST['editPrecio'] ?? 0);
        $uf           = (float) ($_POST['editUf'] ?? 0);
        $m2_terreno   = (int) ($_POST['editM2Terreno'] ?? 0);
        $m2_construido = (int) ($_POST['editM2Construido'] ?? 0);
        $habitaciones = (int) ($_POST['editHabitaciones'] ?? 0);
        $banos        = (int) ($_POST['editBanos'] ?? 0);
        $bodega          = isset($_POST['editBodega']) ? 1 : 0;
        $estacionamiento = isset($_POST['editEstacionamiento']) ? 1 : 0;
        $logia           = isset($_POST['editLogia']) ? 1 : 0;
        $cocina_amoblada = isset($_POST['editCocinaAmoblada']) ? 1 : 0;
        $antejardin      = isset($_POST['editAntejardin']) ? 1 : 0;
        $patio_trasero   = isset($_POST['editPatioTrasero']) ? 1 : 0;
        $piscina         = isset($_POST['editPiscina']) ? 1 : 0;
        $estado       = trim($_POST['editEstado'] ?? 'Publicada');

        if (empty($titulo) || empty($tipo)) {
            echo json_encode(["success" => false, "message" => "Título y tipo son obligatorios."]);
            exit();
        }

        // Actualizar propiedad
        $sql = "UPDATE propiedades 
                SET titulo=?, descripcion=?, tipo=?, provincia=?, comuna=?, sector=?, direccion=?, 
                    precio=?, uf=?, m2_terreno=?, m2_construido=?, habitaciones=?, banos=?,
                    bodega=?, estacionamiento=?, logia=?, cocina_amoblada=?, antejardin=?, patio_trasero=?, piscina=?, estado=?
                WHERE id=?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssddiiiiiiiiiiisi", 
            $titulo, $descripcion, $tipo, $provincia, $comuna, $sector, $direccion, 
            $precio, $uf, $m2_terreno, $m2_construido, $habitaciones, $banos,
            $bodega, $estacionamiento, $logia, $cocina_amoblada, $antejardin, $patio_trasero, $piscina, $estado, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo json_encode([
            "success" => true,
            "message" => "Propiedad actualizada exitosamente."
        ]);
    } catch (\Throwable $e) {
        error_log('Error al editar propiedad: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
    }
}