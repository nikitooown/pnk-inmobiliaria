<?php
include("config/setup.php");
session_start();

if(isset($_SESSION['usuario_sesion']))
{
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Bootstrap 5</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="css/mystyle.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</head>
<body>

  <header>
    <ul>
      <li class="logo">
        <a href="index.html" class="logo-link">
          <img src="img/logo.png" alt="Logo">
        </a>
      </li>
    </ul>
  </header>

<div class="container-fluid">
    <div class="row">
        
        <!-- Sidebar -->
        <div class="col-md-2 p-3 sidebar">
            <h4 class="text-white mb-4">Mi Panel</h4>

            <a href="#" onclick="cargarSeccion('inicio')"><i class="bi bi-house-door"></i> Inicio</a>
            <a href="#" onclick="cargarSeccion('usuarios')"><i class="bi bi-people"></i> Usuarios</a>
            <a href="#" onclick="cargarSeccion('reportes')"><i class="bi bi-bar-chart"></i> Reportes</a>
            <a href="#" onclick="cargarSeccion('configuracion')"><i class="bi bi-gear"></i> Configuración</a>
        </div>

        <!-- Contenido -->
        <div class="col-md-10 p-4">
            <div id="contenido-principal">
                <!-- Encabezado usuario -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Dashboard</h2>

                    <div class="user-box d-flex align-items-center gap-3">
                        <img src="img/<?php echo $_SESSION['foto_sesion'];?>" alt="Usuario" class="user-thumb">
                        <div>
                            <strong><?php echo $_SESSION['usuario_sesion'];?></strong><br>
                            <small class="text-muted"><?php echo $_SESSION['nombre_perfil'];?></small>
                        </div>
                        <?php // Solo el administrador puede editar usuarios ?>
                        <?php if ($_SESSION['nombre_perfil'] === 'Administrador'): ?>
                            <a href="backend/frm_usuarios.php" class="btn btn-pnk">Administrar usuarios</a>
                        <?php endif; ?>

                        <?php if ($_SESSION['nombre_perfil'] === 'Propietario'): ?>
                            <a href="mis-propiedades.php" class="btn btn-pnk">Mis Propiedades</a>
                        <?php elseif ($_SESSION['nombre_perfil'] === 'Gestor Inmobiliario'): ?>
                            <a href="mis-propiedades.php" class="btn btn-pnk">Gestionar Propiedades</a>
                        <?php elseif ($_SESSION['nombre_perfil'] === 'Administrador'): ?>
                            <a href="mis-propiedades.php" class="btn btn-pnk">Todas las Propiedades</a>
                        <?php endif; ?>

                        <a href="backend/logout.php" class="btn btn-pnk btn-sm">Cerrar sesión</a>
                    </div>
                </div>

                <div class="card" style="background-color: #dedbc1; color: #3c3c3c;">
                    <div class="card-header" style="background-color: #b0a78f; color: #3c3c3c; font-weight: bold;">
                        <?php if ($_SESSION['nombre_perfil'] === 'Administrador'): ?>
                            Últimos registros
                        <?php else: ?>
                            Bienvenido/a
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($_SESSION['nombre_perfil'] === 'Administrador'): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background-color: #b0a78f; color: #3c3c3c;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $por_pagina = 20;
                                        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                        $inicio = ($pagina - 1) * $por_pagina;
                                        
                                        $conexion = conectar();
                                        $total_usuarios = mysqli_num_rows(mysqli_query($conexion, "SELECT id FROM usuarios"));
                                        $total_paginas = ceil($total_usuarios / $por_pagina);
                                        
                                        $sql="SELECT * FROM usuarios ORDER BY id ASC LIMIT $inicio, $por_pagina";
                                        $result=mysqli_query($conexion,$sql);
                                        while($datos=mysqli_fetch_array($result))
                                        {
                                    ?>
                                        <tr>
                                            <td><?php echo $datos['id'];?></td>
                                            <td><?php echo htmlspecialchars($datos['nombre']);?></td>
                                            <td><?php echo htmlspecialchars($datos['email']);?></td>
                                            <td><?php if($datos['estado']=='1'){?>
                                                <span class="badge" style="background-color: #b0a78f; color: #3c3c3c;">Activo</span>
                                                <?php
                                                }else{
                                                    ?>
                                                    <span class="badge" style="background-color: #8a8574; color: #3c3c3c;">Inactivo</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                        mysqli_close($conexion);
                                    ?>  
                                    </tbody>
                                </table>
                                <?php if ($total_paginas > 1): ?>
                                <nav aria-label="Paginación de usuarios">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">Anterior</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                                <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($pagina < $total_paginas): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">Siguiente</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($_SESSION['nombre_perfil'] === 'Propietario'):
                            $conexion = conectar();
                            $id_usuario = (int) $_SESSION['id'];

                            $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM propiedades WHERE idpropietario = ?");
                            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
                            mysqli_stmt_execute($stmt);
                            $total_propiedades = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
                            mysqli_stmt_close($stmt);

                            $stmt2 = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM visitas v INNER JOIN propiedades p ON v.id_propiedad = p.id WHERE p.idpropietario = ?");
                            mysqli_stmt_bind_param($stmt2, "i", $id_usuario);
                            mysqli_stmt_execute($stmt2);
                            $total_visitas = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['total'];
                            mysqli_stmt_close($stmt2);
                            mysqli_close($conexion);
                        ?>
                            <h4 class="mb-3">¡Bienvenido/a, <?php echo htmlspecialchars($_SESSION['usuario_sesion']); ?>!</h4>
                            <p>Aquí puedes publicar tus propiedades, editar su información y revisar su estado de publicación.</p>
                            <div class="row mt-3 mb-3">
                                <div class="col-md-4">
                                    <div class="p-3" style="background-color:#f4f3ed; border-radius:10px;">
                                        <div class="text-muted small">Propiedades publicadas</div>
                                        <div style="font-size:1.8rem; font-weight:bold; color:#3c3c3c;"><?= $total_propiedades ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3" style="background-color:#f4f3ed; border-radius:10px;">
                                        <div class="text-muted small">Solicitudes de visita recibidas</div>
                                        <div style="font-size:1.8rem; font-weight:bold; color:#3c3c3c;"><?= $total_visitas ?></div>
                                    </div>
                                </div>
                            </div>
                            <p>
                                <a href="mis-propiedades.php" class="btn btn-pnk">Mis Propiedades</a>
                            </p>
                        <?php elseif ($_SESSION['nombre_perfil'] === 'Gestor Inmobiliario'):
                            $conexion = conectar();
                            $total_comunidad = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedades WHERE estado = 'Publicada'"))['total'];
                            mysqli_close($conexion);
                        ?>
                            <h4 class="mb-3">¡Bienvenido/a, <?php echo htmlspecialchars($_SESSION['usuario_sesion']); ?>!</h4>
                            <p>Como Gestor Inmobiliario, puedes publicar nuevas propiedades y revisar el catálogo completo de la comunidad PNK para ofrecerlas a posibles compradores.</p>
                            <div class="row mt-3 mb-3">
                                <div class="col-md-4">
                                    <div class="p-3" style="background-color:#f4f3ed; border-radius:10px;">
                                        <div class="text-muted small">Propiedades disponibles en la comunidad</div>
                                        <div style="font-size:1.8rem; font-weight:bold; color:#3c3c3c;"><?= $total_comunidad ?></div>
                                    </div>
                                </div>
                            </div>
                            <p>
                                <a href="mis-propiedades.php" class="btn btn-pnk">Gestionar Propiedades</a>
                            </p>
                        <?php else: ?>
                            <h4 class="mb-3">¡Bienvenido/a, <?php echo htmlspecialchars($_SESSION['usuario_sesion']); ?>!</h4>
                            <p>En esta sección podrás gestionar tus propiedades.</p>
                            <p style="margin-top: 15px;">
                                <a href="mis-propiedades.php" class="btn btn-pnk">Mis Propiedades</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="contenido-dinamico" style="display:none;"></div>

        </div>
    </div>
</div>

<script>
function cargarSeccion(seccion) {
    const principal = document.getElementById('contenido-principal');
    const dinamico = document.getElementById('contenido-dinamico');

    if (seccion === 'inicio') {
        principal.style.display = '';
        dinamico.style.display = 'none';
        dinamico.innerHTML = '';
        return;
    }

    let archivo = '';
    switch (seccion) {
        case 'usuarios':
            archivo = 'cargar_usuarios.php';
            break;
        case 'reportes':
            archivo = 'cargar_reportes.php';
            break;
        case 'configuracion':
            archivo = 'cargar_configuracion.php';
            break;
        default:
            principal.style.display = 'none';
            dinamico.style.display = '';
            dinamico.innerHTML = '<p>Sección no encontrada.</p>';
            return;
    }

    fetch(archivo)
        .then(response => response.text())
        .then(html => {
            principal.style.display = 'none';
            dinamico.style.display = '';
            dinamico.innerHTML = html;
        })
        .catch(error => {
            dinamico.innerHTML = '<p>Error al cargar la sección.</p>';
            console.error('Error:', error);
        });
}
</script>

<footer class="text-center py-4 mt-5 border-top" style="background-color: #b0a78f; color: #3c3c3c;">
  <p class="mb-0">© 2026 PNK Inmobiliaria - Todos los derechos reservados</p>
</footer>
</body>
</html>
<?php
}else{

    header("Location:backend/error.html");
}
?>