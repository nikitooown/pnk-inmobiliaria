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

    <link href="css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRI4lkxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="css/mystyle.css" rel="stylesheet">
    <script src="js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


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
                    <?php // solo el administrador puede editar usuarios ?>
                    <?php if ($_SESSION['nombre_perfil'] === 'Administrador'): ?>
                    <h2>Gestión de Usuarios</h2>
                    <a href="backend/frm_usuarios.php" class="btn btn-primary">Administrar usuarios</a>
                    <?php endif; ?>

                    <a href="backend/logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
                </div>
            </div>

            <div class="table-container">
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