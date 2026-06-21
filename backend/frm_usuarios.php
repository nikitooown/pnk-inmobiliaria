<?php
session_start();
include ("../config/setup.php");

// Validación: solo Administrador puede acceder
if ($_SESSION['nombre_perfil'] !== 'Administrador') {
    header("Location: error.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Usuarios</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/mystyle.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validarform(valor) {
            document.getElementById("accion").value = valor;
            document.getElementById("frm_usu").submit();
        }

        function editarUsuario(id, rut, nombre, apellido, email, estado, idperfil) {
            document.getElementById("id").value = id;
            document.getElementById("frm_rut").value = rut;
            document.getElementById("frm_nombre").value = nombre;
            document.getElementById("frm_apellido").value = apellido;
            document.getElementById("frmusuario").value = email;
            document.getElementById("frm_estado").value = estado;
            document.getElementById("frm_idperfil").value = idperfil;
            document.getElementById("accion").value = "modificar";
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
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'crud_usuarios.php';
                    form.innerHTML = '<input type="hidden" name="id" value="' + id + '"><input type="hidden" name="accion" value="eliminar">';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</head>
<body>
    <div id="frm_usuario">
        <div class="card">
            <div class="card-header">Formulario Usuario</div>
            <div class="card-body">
                <form action="crud_usuarios.php" method="post" name="frm_usu" id="frm_usu">
                   
                    <div class="row separacion">
                        <div class="col-sm-3">R.U.T:</div>
                        <div class="col-sm-3"><input type="text" class="form-control" id="frm_rut" name="frm_rut"></div>
                        <div class="col-sm-3">Nombres:</div>
                        <div class="col-sm-3"><input type="text" class="form-control" id="frm_nombre" name="frm_nombre"></div>
                    </div>
                    <div class="row separacion">
                        <div class="col-sm-3">Apellidos:</div>
                        <div class="col-sm-3"><input type="text" class="form-control" id="frm_apellido" name="frm_apellido"></div>
                        <div class="col-sm-3">Estado:</div>
                        <div class="col-sm-3">
                            <select class="form-select form-select-sm mt-3" id="frm_estado" name="frm_estado">
                                <option value="">Seleccionar</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row separacion">
                        <div class="col-sm-3">Perfil:</div>
                        <div class="col-sm-3">
                            <select class="form-select" id="frm_idperfil" name="frm_idperfil">
                                <option value="">Seleccionar</option>
                                <option value="1">Administrador</option>
                                <option value="2">Propietario</option>
                                <option value="3">Gestor Inmobiliario</option>
                            </select>
                        </div>
                    </div>
                    <div class="row separacion">
                        <div class="col-sm-3">Correo (Usuario):</div>
                        <div class="col-sm-3"><input type="text" class="form-control" id="frmusuario" name="frmusuario"></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12 alinear">
                            <button type="button" class="btn btn-primary" onclick="validarform(this.value)" value="guardar">Guardar</button>&nbsp;
                            <button type="button" class="btn btn-success" onclick="validarform(this.value)" value="modificar">Modificar</button>&nbsp;
                            <button type="button" class="btn btn-danger" onclick="validarform(this.value)" value="eliminar">Eliminar</button>&nbsp;
                            <button type="button" class="btn btn-secondary" onclick="validarform(this.value)" value="cancelar">Cancelar</button>
                        </div>
                        <input type="hidden" class="form-control" id="accion" name="accion">
                        <input type="hidden" name="id" id="id" value="">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    <div id="grilla_usuario">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
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
                                <span class="badge bg-success">Activo</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    onclick="editarUsuario(
                                        '<?php echo $datos['id'];?>',
                                        '<?php echo $datos['rut'];?>',
                                        '<?php echo $datos['nombre'];?>',
                                        '<?php echo $datos['apellido'];?>',
                                        '<?php echo $datos['email'];?>',
                                        '<?php echo $datos['estado'];?>',
                                        '<?php echo $datos['idperfil'];?>'
                                    )">Editar</button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="confirmarEliminar(event, '<?php echo $datos['id'];?>')">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>  
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>