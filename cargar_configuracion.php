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
        Configuración del Sistema
    </div>
    <div class="card-body">
        <h5 class="card-title">Parámetros Generales</h5>
        <form onsubmit="event.preventDefault();">
            <div class="row separacion">
                <div class="col-sm-3">Nombre del Sitio:</div>
                <div class="col-sm-9"><input type="text" class="form-control" value="PNK Inmobiliaria"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Email de Contacto:</div>
                <div class="col-sm-9"><input type="email" class="form-control" value="contacto@pnkinmobiliaria.cl"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Teléfono:</div>
                <div class="col-sm-9"><input type="text" class="form-control" value="+56 9 1234 5678"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Dirección:</div>
                <div class="col-sm-9"><input type="text" class="form-control" value="Av. Principal 123, La Serena"></div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 alinear">
                    <button type="button" class="btn btn-pnk">Guardar Cambios</button>
                </div>
            </div>
        </form>

        <hr>
        
        <h5 class="mt-4">Información del Sistema</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <tbody>
                    <tr>
                        <td><strong>Versión</strong></td>
                        <td>1.0.0</td>
                    </tr>
                    <tr>
                        <td><strong>Última Actualización</strong></td>
                        <td><?php echo date('d/m/Y'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Entorno</strong></td>
                        <td>Desarrollo</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>