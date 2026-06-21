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

  <form action="backend/enviar_contacto.php" method="POST" style="max-width: 600px;">
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
    // Detectar parámetros de éxito o error y mostrar mensajes SweetAlert2
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('exito')) {
      Swal.fire({
        icon: 'success',
        title: '¡Mensaje enviado!',
        text: 'Tu mensaje ha sido recibido correctamente. Nos pondremos en contacto pronto.',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        // Limpiar URL después de mostrar el mensaje
        window.history.replaceState({}, document.title, 'contacto.php');
      });
    } else if (urlParams.has('error')) {
      const errorCode = urlParams.get('error');
      let errorMessage = 'Ocurrió un error al enviar tu mensaje.';
      
      switch(errorCode) {
        case '1':
          errorMessage = 'Por favor, ingresa tu nombre.';
          break;
        case '2':
          errorMessage = 'Por favor, ingresa tu correo electrónico.';
          break;
        case '3':
          errorMessage = 'Por favor, ingresa tu mensaje.';
          break;
        case '4':
          errorMessage = 'El correo electrónico no es válido. Verifica el formato.';
          break;
        case '5':
          errorMessage = 'Error en el servidor. Por favor, intenta más tarde.';
          break;
      }
      
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMessage,
        confirmButtonText: 'Aceptar'
      }).then(() => {
        // Limpiar URL después de mostrar el mensaje
        window.history.replaceState({}, document.title, 'contacto.php');
      });
    }
  </script>
</body>
</html>