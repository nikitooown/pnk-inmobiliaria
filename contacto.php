<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contacto - PNK Inmobiliaria</title>
  <link rel="stylesheet" href="css/mystyle.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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

  <form id="formContacto" style="max-width: 600px;">
    <h1>Contacto</h1>
    <p>Estamos para ayudarte. Contáctanos a través del siguiente formulario.</p>
    <hr>

    <label for="nombre"><b>Nombre</b></label>
    <input type="text" placeholder="Tu nombre" name="nombre" id="nombre" required>

    <label for="email"><b>Correo electrónico</b></label>
    <input type="email" placeholder="tu@correo.com" name="email" id="email" required>

    <label for="mensaje"><b>Mensaje</b></label>
    <textarea placeholder="Escribe tu mensaje aquí..." name="mensaje" id="mensaje" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 8px; box-sizing: border-box; background-color: #fcfcfc; font-family: inherit;" required></textarea>

    <div class="clearfix">
      <button type="button" class="cancelbtn" onclick="window.location.href='index.html'">Volver al inicio</button>
      <button type="submit" class="signupbtn">Enviar mensaje</button>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('formContacto');
      if (!form) return;

      form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Enviando...';

        try {
          const formData = new FormData(form);
          const res = await fetch('backend/enviar_contacto.php', { method: 'POST', body: formData });
          const data = await res.json();

          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: '¡Mensaje enviado!',
              text: data.mensaje || 'Tu mensaje ha sido recibido correctamente. Nos pondremos en contacto pronto.',
              confirmButtonText: 'Aceptar'
            });
            form.reset();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.mensaje || 'Ocurrió un error al enviar tu mensaje.',
              confirmButtonText: 'Aceptar'
            });
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'Ocurrió un problema al comunicarse con el servidor.',
            confirmButtonText: 'Aceptar'
          });
        } finally {
          btn.disabled = false;
          btn.textContent = 'Enviar mensaje';
        }
      });
    });
  </script>

<footer class="bg-pnk text-center py-4 mt-5 border-top">
  <p class="mb-0" style="color:#3c3c3c;">© 2026 PNK Inmobiliaria - Todos los derechos reservados</p>
</footer>
</body>
</html>
