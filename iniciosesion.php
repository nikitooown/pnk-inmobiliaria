<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PNK Inmobiliaria</title>
    <link rel="stylesheet" href="css/mystyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <header>
    <ul>
      <li class="logo">
        <a href="index.html"><img src="img/logo.png" alt="Logo PNK"></a>
      </li>
    </ul>
    <ul>
        <li><a href="registro.php">Registrate</a></li>
        <li><a href="iniciosesion.php">Inicio Sesión</a></li>
        <li><a href="contacto.php">Contacto</a></li>
    </ul>
  </header>

  <?php if (isset($_GET['error'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var errorMsg = '';
        <?php if ($_GET['error'] == 1): ?>
            errorMsg = 'Debes completar todos los campos.';
        <?php elseif ($_GET['error'] == 2): ?>
            errorMsg = 'El correo ingresado no está registrado en el sistema.';
        <?php elseif ($_GET['error'] == 3): ?>
            errorMsg = 'La contraseña ingresada es incorrecta.';
        <?php elseif ($_GET['error'] == 4): ?>
            errorMsg = 'Tu cuenta está inactiva. Contacta al administrador.';
        <?php endif; ?>
        Swal.fire({
            icon: 'error',
            title: 'Error de inicio de sesión',
            text: errorMsg,
            confirmButtonColor: '#dc3545'
        });
    });
  </script>
  <?php endif; ?>

    <form action="backend/iniciar_sesion.php" method="POST" id="formLogin">

        <h1>Iniciar Sesión</h1>
        <hr>

        <label for="email"><b>Correo electrónico</b></label>
        <input type="email" placeholder="ejemplo@correo.com" name="email" id="email" required>

        <label for="password"><b>Contraseña</b></label>
        <input type="password" placeholder="Ingresa tu contraseña" name="password" id="password" required>

        <label>
            <input type="checkbox" checked="checked" name="remember"> Recordar sesión
        </label>

        <div class="clearfix">
            <button type="submit" class="signupbtn">Iniciar Sesión</button>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            ¿No tienes cuenta? <a href="registro.php" style="color: #b0a78f; text-decoration: none;">Regístrate aquí</a><br>
            <a href="#" id="link-olvide-password" style="color: #b0a78f; text-decoration: none;">¿Olvidaste tu contraseña?</a>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var link = document.getElementById('link-olvide-password');
        if (link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Recuperación de contraseña',
                    text: 'Por ahora, contacta al administrador del sistema para restablecer tu contraseña.',
                });
            });
        }
    });
    </script>

<footer class="bg-pnk text-center py-4 mt-5 border-top">
  <p class="mb-0" style="color:#3c3c3c;">© 2026 PNK Inmobiliaria - Todos los derechos reservados</p>
</footer>
</body>

</html>
