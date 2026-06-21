<?php
include("config/setup.php");
session_start();

// Validar que el usuario está autenticado
if (!isset($_SESSION['usuario_sesion'])) {
    header("Location: iniciosesion.php");
    exit();
}

// Validar que el rol tiene acceso
$roles_permitidos = ['Propietario', 'Gestor Inmobiliario', 'Administrador'];
if (!in_array($_SESSION['nombre_perfil'], $roles_permitidos)) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Propiedades - PNK Inmobiliaria</title>
    <link rel="stylesheet" href="css/mystyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-12">
            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Mis Propiedades</h1>
                    <p class="text-muted">Bienvenido/a, <?php echo htmlspecialchars($_SESSION['usuario_sesion']); ?> (<?php echo $_SESSION['nombre_perfil']; ?>)</p>
                </div>
                <div>
                    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
                    <a href="backend/logout.php" class="btn btn-danger">Cerrar sesión</a>
                </div>
            </div>

            <!-- Alertas -->
            <div id="alertasPropiedad"></div>

            <!-- Formulario de Creación -->
            <div class="card mb-5">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Crear Nueva Propiedad</h5>
                </div>
                <div class="card-body">
                    <form id="formCrearPropiedad" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo de Propiedad *</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Departamento">Departamento</option>
                                    <option value="Terreno">Terreno</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="provincia" class="form-label">Provincia</label>
                                <input type="text" class="form-control" id="provincia" name="provincia">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="comuna" class="form-label">Comuna</label>
                                <input type="text" class="form-control" id="comuna" name="comuna">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sector" class="form-label">Sector</label>
                                <input type="text" class="form-control" id="sector" name="sector">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <input type="number" class="form-control" id="precio" name="precio" required min="0" step="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="m2" class="form-label">Superficie (m²)</label>
                                <input type="number" class="form-control" id="m2" name="m2" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="habitaciones" class="form-label">Habitaciones</label>
                                <input type="number" class="form-control" id="habitaciones" name="habitaciones" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="banos" class="form-label">Baños</label>
                                <input type="number" class="form-control" id="banos" name="banos" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagenes" class="form-label">Imágenes de la Propiedad (1-10) *</label>
                            <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*" required>
                            <small class="text-muted">Formatos permitidos: JPG, JPEG, PNG, WEBP</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Crear Propiedad</button>
                    </form>
                </div>
            </div>

            <!-- Lista de Propiedades -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Tus Propiedades Publicadas</h5>
                </div>
                <div class="card-body">
                    <div id="listaPropiedades" class="list-group">
                        <p class="text-muted">Cargando propiedades...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Propiedad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPropiedad" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="editId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="editTitulo" name="editTitulo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editTipo" class="form-label">Tipo</label>
                            <select class="form-select" id="editTipo" name="editTipo">
                                <option value="Casa">Casa</option>
                                <option value="Departamento">Departamento</option>
                                <option value="Terreno">Terreno</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcion" name="editDescripcion" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="editProvincia" class="form-label">Provincia</label>
                            <input type="text" class="form-control" id="editProvincia" name="editProvincia">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editComuna" class="form-label">Comuna</label>
                            <input type="text" class="form-control" id="editComuna" name="editComuna">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editSector" class="form-label">Sector</label>
                            <input type="text" class="form-control" id="editSector" name="editSector">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editDireccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="editDireccion" name="editDireccion">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editPrecio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="editPrecio" name="editPrecio" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editSuperficie" class="form-label">Superficie (m²)</label>
                            <input type="number" class="form-control" id="editSuperficie" name="editSuperficie" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editHabitaciones" class="form-label">Habitaciones</label>
                            <input type="number" class="form-control" id="editHabitaciones" name="editHabitaciones" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBanos" class="form-label">Baños</label>
                            <input type="number" class="form-control" id="editBanos" name="editBanos" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editEstado" class="form-label">Estado</label>
                        <select class="form-select" id="editEstado" name="editEstado">
                            <option value="Activa">Activa</option>
                            <option value="Inactiva">Inactiva</option>
                            <option value="Vendida">Vendida</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="validar.js"></script>
<script>
    // Función para guardar la edición
    function guardarEdicion() {
        const formData = new FormData(document.getElementById('formEditarPropiedad'));
        formData.append('accion', 'editar');

        fetch('backend/propiedades_controller.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', 'Propiedad actualizada correctamente.', 'success');
                    cargarPropiedades();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
                } else {
                    Swal.fire('Error', data.message || 'Error al actualizar la propiedad.', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Error de conexión.', 'error'));
    }

    // Función para eliminar con SweetAlert2
    window.confirmarEliminar = async function(id) {
        const result = await Swal.fire({
            title: '¿Eliminar propiedad?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        });

        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append('accion', 'eliminar');
        formData.append('id', id);

        const res = await fetch('backend/propiedades_controller.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            document.getElementById(`prop-${id}`)?.remove();
            Swal.fire('Éxito', data.message, 'success');
        } else {
            Swal.fire('Error', data.message || 'Error al eliminar.', 'error');
        }
    };
</script>

</body>
</html>
