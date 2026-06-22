<?php
// No llamar session_start() aquí porque dashboard.php ya lo inició
include("config/setup.php");

// Validación: solo Administrador puede acceder
if ($_SESSION['nombre_perfil'] !== 'Administrador') {
    echo json_encode(['success' => false, 'mensaje' => 'No autorizado.']);
    exit();
}

// Función para obtener nombre del perfil por ID
function nombre_perfil($idperfil) {
    $nombres = [1 => 'Administrador', 2 => 'Propietario', 3 => 'Gestor Inmobiliario'];
    return $nombres[(int)$idperfil] ?? 'Desconocido';
}
?>

<div class="card" style="background-color: #dedbc1; color: #3c3c3c;">
    <div class="card-header" style="background-color: #b0a78f; color: #3c3c3c; font-weight: bold;">
        Reportes del Sistema
    </div>
    <div class="card-body">
        <h5 class="card-title">Estadísticas Generales</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3" style="background-color: #b0a78f; color: #3c3c3c;">
                    <div class="card-body">
                        <h6 class="card-title">Total Usuarios</h6>
                        <p class="card-text display-6">
                            <?php
                                $conexion = conectar();
                                $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM usuarios");
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $datos = mysqli_fetch_assoc($result);
                                echo $datos['total'];
                                mysqli_stmt_close($stmt);
                                mysqli_close($conexion);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3" style="background-color: #b0a78f; color: #3c3c3c;">
                    <div class="card-body">
                        <h6 class="card-title">Usuarios Activos</h6>
                        <p class="card-text display-6">
                            <?php
                                $conexion = conectar();
                                $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM usuarios WHERE estado = ?");
                                $estado_activo = 1;
                                mysqli_stmt_bind_param($stmt, "i", $estado_activo);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $datos = mysqli_fetch_assoc($result);
                                echo $datos['total'];
                                mysqli_stmt_close($stmt);
                                mysqli_close($conexion);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3" style="background-color: #b0a78f; color: #3c3c3c;">
                    <div class="card-body">
                        <h6 class="card-title">Usuarios Inactivos</h6>
                        <p class="card-text display-6">
                            <?php
                                $conexion = conectar();
                                $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM usuarios WHERE estado = ?");
                                $estado_inactivo = 0;
                                mysqli_stmt_bind_param($stmt, "i", $estado_inactivo);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $datos = mysqli_fetch_assoc($result);
                                echo $datos['total'];
                                mysqli_stmt_close($stmt);
                                mysqli_close($conexion);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>
        
        <h5 class="mt-4">Usuarios por Perfil</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background-color: #b0a78f; color: #3c3c3c;">
                    <tr>
                        <th>Perfil</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $conexion = conectar();
                    $stmt = mysqli_prepare($conexion, "SELECT idperfil, COUNT(id) as cantidad FROM usuarios GROUP BY idperfil");
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    while($datos = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo nombre_perfil($datos['idperfil']);?></td>
                        <td><?php echo $datos['cantidad'];?></td>
                    </tr>
                <?php } 
                    mysqli_stmt_close($stmt);
                    mysqli_close($conexion);
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>