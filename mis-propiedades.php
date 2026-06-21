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
                    <?php if ($_SESSION['nombre_perfil'] === 'Gestor Inmobiliario'): ?>
                        <p class="text-muted" style="font-size: 0.9rem;"><em>Estás viendo el catálogo completo de la comunidad PNK</em></p>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
                    <a href="backend/logout.php" class="btn btn-pnk">Cerrar sesión</a>
                </div>
            </div>

            <!-- Alertas -->
            <div id="alertasPropiedad"></div>

            <!-- Formulario de Creación -->
            <div class="card mb-5">
                <div class="card-header bg-pnk">
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
                            <div class="col-md-4 mb-3">
                                <label for="precio" class="form-label">Precio (CLP) *</label>
                                <input type="number" class="form-control" id="precio" name="precio" required min="0" step="1000">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="uf" class="form-label">Precio en UF</label>
                                <input type="number" class="form-control" id="uf" name="uf" min="0" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="m2_terreno" class="form-label">Área Total del Terreno (m²)</label>
                                <input type="number" class="form-control" id="m2_terreno" name="m2_terreno" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="m2_construido" class="form-label">Área Construida (m²)</label>
                                <input type="number" class="form-control" id="m2_construido" name="m2_construido" min="0">
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

                        <!-- Equipamiento -->
                        <div class="row mb-3">
                            <label class="form-label">Equipamiento</label>
                            <div class="col-md-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="bodega" name="bodega" value="1">
                                    <label class="form-check-label" for="bodega">Bodega</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="estacionamiento" name="estacionamiento" value="1">
                                    <label class="form-check-label" for="estacionamiento">Estacionamiento</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="logia" name="logia" value="1">
                                    <label class="form-check-label" for="logia">Logia</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="cocina_amoblada" name="cocina_amoblada" value="1">
                                    <label class="form-check-label" for="cocina_amoblada">Cocina amoblada</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="antejardin" name="antejardin" value="1">
                                    <label class="form-check-label" for="antejardin">Antejardín</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="patio_trasero" name="patio_trasero" value="1">
                                    <label class="form-check-label" for="patio_trasero">Patio trasero</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="piscina" name="piscina" value="1">
                                    <label class="form-check-label" for="piscina">Piscina</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagenes" class="form-label">Imágenes de la Propiedad (1-10) *</label>
                            <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*" required>
                            <small class="text-muted">Formatos permitidos: JPG, JPEG, PNG, WEBP</small>
                        </div>

                        <button type="submit" class="btn btn-pnk">Crear Propiedad</button>
                    </form>
                </div>
            </div>

            <!-- Lista de Propiedades -->
            <div class="card">
                <div class="card-header bg-pnk">
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
            <form id="formEditarPropiedad" enctype="multipart/form-data">
                <div class="modal-body">
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
                        <div class="col-md-4 mb-3">
                            <label for="editPrecio" class="form-label">Precio (CLP)</label>
                            <input type="number" class="form-control" id="editPrecio" name="editPrecio" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editUf" class="form-label">Precio en UF</label>
                            <input type="number" class="form-control" id="editUf" name="editUf" min="0" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editM2Terreno" class="form-label">Área Terreno (m²)</label>
                            <input type="number" class="form-control" id="editM2Terreno" name="editM2Terreno" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editM2Construido" class="form-label">Área Construida (m²)</label>
                            <input type="number" class="form-control" id="editM2Construido" name="editM2Construido" min="0">
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

                    <!-- Equipamiento -->
                    <div class="row mb-3">
                        <label class="form-label">Equipamiento</label>
                        <div class="col-md-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editBodega" name="editBodega" value="1">
                                <label class="form-check-label" for="editBodega">Bodega</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editEstacionamiento" name="editEstacionamiento" value="1">
                                <label class="form-check-label" for="editEstacionamiento">Estacionamiento</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editLogia" name="editLogia" value="1">
                                <label class="form-check-label" for="editLogia">Logia</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editCocinaAmoblada" name="editCocinaAmoblada" value="1">
                                <label class="form-check-label" for="editCocinaAmoblada">Cocina amoblada</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editAntejardin" name="editAntejardin" value="1">
                                <label class="form-check-label" for="editAntejardin">Antejardín</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editPatioTrasero" name="editPatioTrasero" value="1">
                                <label class="form-check-label" for="editPatioTrasero">Patio trasero</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editPiscina" name="editPiscina" value="1">
                                <label class="form-check-label" for="editPiscina">Piscina</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editEstado" class="form-label">Estado</label>
                        <select class="form-select" id="editEstado" name="editEstado">
                            <option value="Publicada">Publicada</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Vendida">Vendida</option>
                            <option value="Arrendada">Arrendada</option>
                            <option value="Inactiva">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-pnk" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-pnk">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="validar.js"></script>
<script>
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

<footer class="bg-pnk text-center py-4 mt-5 border-top">
  <p class="mb-0" style="color:#3c3c3c;">© 2026 PNK Inmobiliaria - Todos los derechos reservados</p>
</footer>
</body>
</html>