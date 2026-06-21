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

<div class="container-fluid">
    <div class="row">
        
        <!-- Sidebar -->
        <div class="col-md-2 p-3 sidebar">
            <h4 class="text-white mb-4">Mi Panel</h4>

            <a href="#">Inicio</a>
            <a href="#">Usuarios</a>
            <a href="#">Reportes</a>
            <a href="#">Configuración</a>
        </div>

        <!-- Contenido -->
        <div class="col-md-10 p-4">
            
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
                        <a href="backend/frm_usuarios.php" class="btn btn-primary">Administrar usuarios</a>
                    <?php endif; ?>

                    <?php // Propietario y Gestor pueden ver sus propiedades ?>
                    <?php if ($_SESSION['nombre_perfil'] === 'Propietario' || $_SESSION['nombre_perfil'] === 'Gestor Inmobiliario' || $_SESSION['nombre_perfil'] === 'Administrador'): ?>
                        <a href="mis-propiedades.php" class="btn btn-success">Mis Propiedades</a>
                    <?php endif; ?>

                    <a href="backend/logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                </div>
            </div>

            <div class="table-container">
                <?php if ($_SESSION['nombre_perfil'] === 'Administrador'): ?>
                    <h4 class="mb-3">Últimos registros</h4>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql="select * from usuarios";
                                $result=mysqli_query(conectar(),$sql);
                                $contar=mysqli_num_rows($result);
                                while($datos=mysqli_fetch_array($result))
                                {
                            ?>
                                <tr>
                                    <td><?php echo $datos['id'];?></td>
                                    <td><?php echo $datos['nombre'];?></td>
                                    <td><?php echo $datos['email'];?></td>
                                    <td><?php if($datos['estado']=='1'){?>
                                        <span class="badge bg-success">Activo</span>
                                        <?php
                                        }else{
                                            ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>  
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>¡Bienvenido/a, <?php echo htmlspecialchars($_SESSION['usuario_sesion']); ?>!</h4>
                        <p>En esta sección podrás gestionar tus propiedades.</p>
                        <p style="margin-top: 15px;">
                            <a href="mis-propiedades.php" class="btn btn-primary">Mis Propiedades</a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
}else{

    header("Location:backend/error.html");
}
?>