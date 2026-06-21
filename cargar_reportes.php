<?php
session_start();
include("config/setup.php");

// Validación: solo Administrador puede acceder
if ($_SESSION['nombre_perfil'] !== 'Administrador') {
    echo json_encode(['success' => false, 'mensaje' => 'No autorizado.']);
    exit();
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
                                $sql = "SELECT COUNT(*) as total FROM usuarios";
                                $result = mysqli_query(conectar(), $sql);
                                $datos = mysqli_fetch_array($result);
                                echo $datos['total'];
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
                                $sql = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1";
                                $result = mysqli_query(conectar(), $sql);
                                $datos = mysqli_fetch_array($result);
                                echo $datos['total'];
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
                                $sql = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 0";
                                $result = mysqli_query(conectar(), $sql);
                                $datos = mysqli_fetch_array($result);
                                echo $datos['total'];
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
                    $sql = "SELECT idperfil, COUNT(id) as cantidad FROM usuarios GROUP BY idperfil";
                    $result = mysqli_query(conectar(), $sql);
                    while($datos = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <td><?php echo nombre_perfil($datos['idperfil']);?></td>
                        <td><?php echo $datos['cantidad'];?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>