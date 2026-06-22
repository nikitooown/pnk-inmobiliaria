<?php
// No llamar session_start() aquí porque dashboard.php ya lo inició
include("config/setup.php");

// Validación: solo Administrador puede acceder
if ($_SESSION['nombre_perfil'] !== 'Administrador') {
    echo json_encode(['success' => false, 'mensaje' => 'No autorizado.']);
    exit();
}
?>

<!-- Formulario -->
<div class="card mb-4" style="background-color: #dedbc1; color: #3c3c3c;">
    <div class="card-header" style="background-color: #b0a78f; color: #3c3c3c; font-weight: bold;">
        <span id="form-titulo">Formulario Usuario</span>
    </div>
    <div class="card-body">
        <form id="frm_usu" novalidate onsubmit="event.preventDefault();">
            <div class="row separacion">
                <div class="col-sm-3">RUT:</div>
                <div class="col-sm-9"><input type="text" class="form-control" id="frm_rut" name="frm_rut"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Nombres:</div>
                <div class="col-sm-9"><input type="text" class="form-control" id="frm_nombre" name="frm_nombre"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Apellidos:</div>
                <div class="col-sm-9"><input type="text" class="form-control" id="frm_apellido" name="frm_apellido"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Correo (Usuario):</div>
                <div class="col-sm-9"><input type="text" class="form-control" id="frmusuario" name="frmusuario"></div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Estado:</div>
                <div class="col-sm-9">
                    <select class="form-select" id="frm_estado" name="frm_estado">
                        <option value="">Seleccionar</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="row separacion">
                <div class="col-sm-3">Perfil:</div>
                <div class="col-sm-9">
                    <select class="form-select" id="frm_idperfil" name="frm_idperfil">
                        <option value="">Seleccionar</option>
                        <option value="1">Administrador</option>
                        <option value="2">Propietario</option>
                        <option value="3">Gestor Inmobiliario</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 alinear">
                    <button type="button" id="btn-guardar" class="btn btn-pnk" onclick="enviarAccion('insertar')">Guardar</button>&nbsp;
                    <button type="button" id="btn-modificar" class="btn btn-pnk" style="display:none;" onclick="enviarAccion('modificar')">Modificar</button>&nbsp;
                    <button type="button" id="btn-eliminar" class="btn btn-pnk" style="display:none;" onclick="confirmarEliminar(event, document.getElementById('id').value)">Eliminar</button>&nbsp;
                    <button type="button" id="btn-cancelar" class="btn btn-pnk" style="display:none;" onclick="cancelar()">Cancelar</button>
                </div>
                <input type="hidden" class="form-control" id="accion" name="accion">
                <input type="hidden" name="id" id="id" value="">
            </div>
        </form>
    </div>
</div>

<!-- Grilla de usuarios -->
<div class="card" style="background-color: #dedbc1; color: #3c3c3c;">
    <div class="card-header" style="background-color: #b0a78f; color: #3c3c3c; font-weight: bold;">
        Lista de Usuarios
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background-color: #b0a78f; color: #3c3c3c;">
                    <tr>
                        <th>ID</th>
                        <th>RUT</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $sql="SELECT * FROM usuarios";
                    $result=mysqli_query(conectar(),$sql);
                    while($datos=mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <td><?php echo $datos['id'];?></td>
                        <td><?php echo $datos['rut'];?></td>
                        <td><?php echo $datos['nombre'] . " " . $datos['apellido'];?></td>
                        <td><?php echo $datos['email'];?></td>
                        <td>
                            <?php if((int)$datos['estado'] === 1){ ?>
                                <span class="badge" style="background-color: #b0a78f; color: #3c3c3c;">Activo</span>
                            <?php } else { ?>
                                <span class="badge" style="background-color: #8a8574; color: #3c3c3c;">Inactivo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-pnk" 
                                    onclick="editarUsuario(
                                        '<?php echo $datos['id'];?>',
                                        '<?php echo $datos['rut'];?>',
                                        '<?php echo $datos['nombre'];?>',
                                        '<?php echo $datos['apellido'];?>',
                                        '<?php echo $datos['email'];?>',
                                        '<?php echo $datos['estado'];?>',
                                        '<?php echo $datos['idperfil'];?>'
                                    )">Editar</button>
                            <button type="button" class="btn btn-sm btn-pnk" 
                                    onclick="confirmarEliminar(event, '<?php echo $datos['id'];?>')">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>  
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function editarUsuario(id, rut, nombre, apellido, email, estado, idperfil) {
    document.getElementById("id").value = id;
    document.getElementById("frm_rut").value = rut;
    document.getElementById("frm_nombre").value = nombre;
    document.getElementById("frm_apellido").value = apellido;
    document.getElementById("frmusuario").value = email;
    document.getElementById("frm_estado").value = estado;
    document.getElementById("frm_idperfil").value = idperfil;

    // Cambiar título del formulario
    document.getElementById("form-titulo").textContent = "Editar Usuario";

    // Ocultar botón Guardar, mostrar Modificar / Eliminar / Cancelar
    document.getElementById("btn-guardar").style.display = "none";
    document.getElementById("btn-modificar").style.display = "inline-block";
    document.getElementById("btn-eliminar").style.display = "inline-block";
    document.getElementById("btn-cancelar").style.display = "inline-block";
}

function confirmarEliminar(event, id) {
    event.preventDefault();
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará el usuario. No se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarAccion('eliminar', id);
        }
    });
}

async function enviarAccion(accion, idUsuario = null) {
    const form = document.getElementById('frm_usu');
    const formData = new FormData(form);
    formData.set('accion', accion);
    if (idUsuario !== null) {
        formData.set('id', idUsuario);
    }

    try {
        const response = await fetch('backend/crud_usuarios.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonColor: '#3085d6'
            }).then(() => {
                cargarSeccion('usuarios');
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#d33'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor.',
            confirmButtonColor: '#d33'
        });
    }
}

function cancelar() {
    document.getElementById("id").value = "";
    document.getElementById("frm_rut").value = "";
    document.getElementById("frm_nombre").value = "";
    document.getElementById("frm_apellido").value = "";
    document.getElementById("frmusuario").value = "";
    document.getElementById("frm_estado").value = "";
    document.getElementById("frm_idperfil").value = "";
    document.getElementById("accion").value = "";

    // Restaurar título original
    document.getElementById("form-titulo").textContent = "Formulario Usuario";

    // Mostrar solo Guardar, ocultar Modificar / Eliminar / Cancelar
    document.getElementById("btn-guardar").style.display = "inline-block";
    document.getElementById("btn-modificar").style.display = "none";
    document.getElementById("btn-eliminar").style.display = "none";
    document.getElementById("btn-cancelar").style.display = "none";
}
</script>