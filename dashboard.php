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

    <ul>
      <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
      <li><a href="mis-propiedades.php" class="nav-link">Mis Propiedades</a></li>
      <li><a href="backend/logout.php" class="btn btn-pnk">Cerrar Sesión</a></li>
    </ul>
  </header>

<div class="container-fluid">
    <div class="row">
        
        <!-- Sidebar -->
        <div class="col-md-2 p-3 sidebar">
            <h4 class="text-white mb-4">Mi Panel</h4>

            <a href="#" onclick="cargarSeccion('inicio')">Inicio</a>
            <a href="#" onclick="cargarSeccion('usuarios')">Usuarios</a>
            <a href="#" onclick="cargarSeccion('reportes')">Reportes</a>
            <a href="#" onclick="cargarSeccion('configuracion')">Configuración</a>
        </div>

        <!-- Contenido -->
        <div class="col-md-10 p-4">
            <div id="contenido-dinamico"></div>
            
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

                    <?php // Propietario y Gestor pueden ver sus propiedades ?>
                    <?php if ($_SESSION['nombre_perfil'] === 'Propietario' || $_SESSION['nombre_perfil'] === 'Gestor Inmobiliario' || $_SESSION['nombre_perfil'] === 'Administrador'): ?>
                        <a href="mis-propiedades.php" class="btn btn-pnk">Mis Propiedades</a>
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
    </div>
</div>

<script>
function cargarSeccion(seccion) {
    const contenedor = document.getElementById('contenido-dinamico');
    let archivo = '';
    
    switch(seccion) {
        case 'inicio':
            // Mostrar contenido estático del dashboard
            contenedor.innerHTML = `
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
                        <?php else: ?>
                            <h4 class="mb-3">¡Bienvenido/a, <?php echo htmlspecialchars($_SESSION['usuario_sesion']); ?>!</h4>
                            <p>En esta sección podrás gestionar tus propiedades.</p>
                            <p style="margin-top: 15px;">
                                <a href="mis-propiedades.php" class="btn btn-pnk">Mis Propiedades</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            `;
            return;
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
            contenedor.innerHTML = '<p>Sección no encontrada.</p>';
            return;
    }
    
    fetch(archivo)
        .then(response => response.text())
        .then(html => {
            contenedor.innerHTML = html;
        })
        .catch(error => {
            contenedor.innerHTML = '<p>Error al cargar la sección.</p>';
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