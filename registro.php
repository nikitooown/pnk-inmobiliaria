<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - PNK Inmobiliaria</title>
  <link rel="stylesheet" href="css/mystyle.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .tab-registro {
      max-width: 700px;
      margin: 30px auto;
      padding: 0 20px;
    }
    .tab-buttons {
      display: flex;
      justify-content: center;
      gap: 0;
      margin-bottom: 0;
    }
    .tab-buttons button {
      padding: 14px 40px;
      border: none;
      background-color: #dedbc1;
      color: #3c3c3c;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
      font-size: 1rem;
      letter-spacing: 0.5px;
      flex: 1;
      max-width: 250px;
    }
    .tab-buttons button:first-child {
      border-radius: 10px 0 0 0;
    }
    .tab-buttons button:last-child {
      border-radius: 0 10px 0 0;
    }
    .tab-buttons button.active {
      background-color: #b0a78f;
      color: white;
    }
    .tab-buttons button:hover:not(.active) {
      background-color: #c9c1a7;
      color: #3c3c3c;
    }
    .tab-panel {
      display: none;
    }
    .tab-panel.active {
      display: block;
    }
    .tab-panel form {
      border-radius: 0 0 15px 15px;
      margin-top: 0;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }
  </style>
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

  <div class="tab-registro">

    <div class="tab-buttons">
      <button id="tabPropietario" class="active" onclick="mostrarPestana('propietario')">Propietario</button>
      <button id="tabGestor" onclick="mostrarPestana('gestor')">Gestor Inmobiliario</button>
    </div>

    <!-- PROPIETARIO -->
     
    <div id="panelPropietario" class="tab-panel active">
      <form action="backend/registrar_usuario.php" method="post">
        <div class="container">
    <h1>Registrarse</h1>
    <p>Rellene el formulario</p>
    <hr>

    <label for="Rut"><b>Rut</b></label>
    <input type="text" placeholder="Ingrese su Rut" name="rut" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="nombre"><b>Nombre</b></label>
    <input type="text" placeholder="Ingrese su nombre" name="nombre" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="apellidos"><b>Apellidos</b></label>
    <input type="text" placeholder="Ingrese sus Apellidos" name="apellido" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="fecha_nacimiento"><b>Fecha nacimiento</b></label>
    <input type="date" placeholder="Ingrese su fecha de nacimiento" name="fecha_nacimiento" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>


    <label for="genero">Sexo:</label>
    <select id="genero" name="genero">
      <option value="Masculino">Masculino</option>
      <option value="Femenino">Femenino</option>
    </select>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="telefono"><b>Teléfono</b></label>
    <input type="text" placeholder="Ingrese su teléfono (+569)" name="telefono" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Ingrese su email" name="email" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="pswd"><b>Contraseña</b></label>
    <input type="password" placeholder="Ingrese su contraseña" name="pswd" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="pswd-repeat"><b>Repita su contraseña</b></label>
    <input type="password" placeholder="Repita su contraseña" name="pswd-repeat" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label>
    <input type="checkbox" checked="checked" name="Recordar" style="margin-bottom:15px"> Recuerdame
    </label>

<p>
  <label>
    <input type="checkbox" name="terminos" required>
    Acepto los <a href="#" style="color:#3c3c3c">Términos y condiciones</a>.
  </label>
</p>

    <div class="clearfix">
    <button type="button" class="cancelbtn">Cancelar</button>
    <input type="hidden" name="idperfil" value="2">
    <button type="submit" name="registrar_propietario">Registrar</button>
    </div>
  </div>
      </form>
    </div>

    <!-- GESTOR -->
    <div id="panelGestor" class="tab-panel">
      <form action="backend/registrar_usuario.php" method="post" enctype="multipart/form-data">
        <div class="container">
    <h1>Registrarse</h1>
    <p>Rellene el formulario</p>
    <hr>

    <label for="Rut"><b>Rut</b></label>
    <input type="text" placeholder="Ingrese su Rut" name="rut" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="nombre"><b>Nombre</b></label>
    <input type="text" placeholder="Ingrese su nombre" name="nombre" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="apellidos"><b>Apellidos</b></label>
    <input type="text" placeholder="Ingrese sus Apellidos" name="apellido" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="fecha_nacimiento"><b>Fecha nacimiento</b></label>
    <input type="date" placeholder="Ingrese su fecha de nacimiento" name="fecha_nacimiento" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>


    <label for="genero">Sexo:</label>
    <select id="genero" name="genero">
      <option value="Masculino">Masculino</option>
      <option value="Femenino">Femenino</option>
    </select>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

        <label for="telefono"><b>Teléfono</b></label>
    <input type="text" placeholder="Ingrese su teléfono (+569)" name="telefono" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Ingrese su email" name="email" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="pswd"><b>Contraseña</b></label>
    <input type="password" placeholder="Ingrese su contraseña" name="pswd" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="pswd-repeat"><b>Repita su contraseña</b></label>
    <input type="password" placeholder="Repita su contraseña" name="pswd-repeat" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label for="certificado"><b>Certificado de Antecedentes</b></label>
    <input type="file" placeholder="Adjunte su Certificado de Antecedentes" name="certificado" required>
    <span class="error-msg" style="color:red; font-size:0.9em;"></span>

    <label>
    <input type="checkbox" checked="checked" name="Recordar" style="margin-bottom:15px"> Recuerdame
    </label>

<p>
  <label>
    <input type="checkbox" name="terminos" required>
    Acepto los <a href="#" style="color:#3c3c3c">Términos y condiciones</a>.
  </label>
</p>
    <div class="clearfix">
    <button type="button" class="cancelbtn">Cancelar</button>
    <input type="hidden" name="idperfil" value="3">
    <button type="submit" name="registrar_gestor">Registrar</button>
    </div>
  </div>
      </form>
    </div>

  </div>

  <script>
    function mostrarPestana(tipo) {
      document.getElementById('panelPropietario').classList.remove('active');
      document.getElementById('panelGestor').classList.remove('active');
      document.getElementById('tabPropietario').classList.remove('active');
      document.getElementById('tabGestor').classList.remove('active');
      if (tipo === 'propietario') {
        document.getElementById('panelPropietario').classList.add('active');
        document.getElementById('tabPropietario').classList.add('active');
      } else {
        document.getElementById('panelGestor').classList.add('active');
        document.getElementById('tabGestor').classList.add('active');
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const forms = document.querySelectorAll("form");

  forms.forEach(form => {
    form.addEventListener("submit", function(e) {
      let valido = true;

      // Limpiar mensajes previos
      form.querySelectorAll(".error-msg").forEach(el => el.textContent = "");

      // Campos
      let rut = form.querySelector("[name='rut']");
      let nombre = form.querySelector("[name='nombre']");
      let apellido = form.querySelector("[name='apellido']");
      let fecha = form.querySelector("[name='fecha_nacimiento']");
      let telefono = form.querySelector("[name='telefono']");
      let email = form.querySelector("[name='email']");
      let clave = form.querySelector("[name='pswd']");
      let claveRepeat = form.querySelector("[name='pswd-repeat']");
      let terminos = form.querySelector("[name='terminos']");

      // Validar Rut
      if (rut.value.trim() === "") {
        rut.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (!/^\d{1,2}\.?\d{3}\.?\d{3}-[0-9Kk]$/.test(rut.value.trim())) {
        rut.nextElementSibling.textContent = "Rut inválido (ejemplo: 12345678-9).";
        valido = false;
      }

      // Validar nombre y apellido
      if (nombre.value.trim() === "") {
        nombre.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (nombre.value.trim().length < 2) {
        nombre.nextElementSibling.textContent = "El nombre debe tener al menos 2 caracteres.";
        valido = false;
      }

      if (apellido.value.trim() === "") {
        apellido.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (apellido.value.trim().length < 2) {
        apellido.nextElementSibling.textContent = "El apellido debe tener al menos 2 caracteres.";
        valido = false;
      }

      // Validar fecha y mayor de edad
      if (!fecha.value) {
        fecha.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else {
        let nacimiento = new Date(fecha.value);
        let hoy = new Date();
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        let mes = hoy.getMonth() - nacimiento.getMonth();
        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
          edad--;
        }
        if (edad < 18) {
          fecha.nextElementSibling.textContent = "Debe ser mayor de edad (mínimo 18 años).";
          valido = false;
        }
      }

      // Validar teléfono
      if (telefono.value.trim() === "") {
        telefono.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (!/^\+569\d{8}$/.test(telefono.value.trim())) {
        telefono.nextElementSibling.textContent = "Teléfono inválido (+569XXXXXXXX).";
        valido = false;
      }

      // Validar email
      if (email.value.trim() === "") {
        email.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        email.nextElementSibling.textContent = "Correo electrónico inválido.";
        valido = false;
      }

      // Validar contraseñas
      if (clave.value === "") {
        clave.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (clave.value.length < 8) {
        clave.nextElementSibling.textContent = "La contraseña debe tener al menos 8 caracteres.";
        valido = false;
      }

      if (claveRepeat.value === "") {
        claveRepeat.nextElementSibling.textContent = "Este campo es obligatorio.";
        valido = false;
      } else if (clave.value !== claveRepeat.value) {
        claveRepeat.nextElementSibling.textContent = "Las contraseñas no coinciden.";
        valido = false;
      }

      // Validar términos
      if (!terminos.checked) {
        terminos.nextElementSibling.textContent = "Debe aceptar los términos y condiciones.";
        valido = false;
      }

      if (!valido) {
        e.preventDefault();
      }
    });
  });
});
</script>

<script>
  // Mostrar errores de certificado con SweetAlert2
  const urlParams = new URLSearchParams(window.location.search);
  
  if (urlParams.has('error')) {
    const errorCode = urlParams.get('error');
    let errorMessage = 'Ocurrió un error durante el registro.';
    
    // Mapear códigos de error específicos para certificado
    if (errorCode === 'cert_1') {
      errorMessage = 'Error al subir el archivo. Por favor, intenta nuevamente.';
    } else if (errorCode === 'cert_2') {
      errorMessage = 'El archivo es demasiado grande. El tamaño máximo permitido es 5MB.';
    } else if (errorCode === 'cert_3') {
      errorMessage = 'Formato de archivo no permitido. Solo se aceptan: PDF, JPG, JPEG, PNG.';
    } else if (errorCode === 'cert_4') {
      errorMessage = 'Error al guardar el archivo. Por favor, intenta nuevamente.';
    } else if (errorCode === 'db_error') {
      // Error de base de datos (duplicado, etc)
      errorMessage = <?php echo json_encode($_SESSION['error_registro'] ?? 'Error al procesar tu registro. Intenta nuevamente.'); ?>;
      <?php if (isset($_SESSION['error_registro'])) unset($_SESSION['error_registro']); ?>
    }
    
    Swal.fire({
      icon: 'error',
      title: 'Error en el Registro',
      text: errorMessage,
      confirmButtonText: 'Aceptar'
    }).then(() => {
      // Limpiar URL después de mostrar el mensaje
      window.history.replaceState({}, document.title, 'registro.php');
    });
  }
</script>

<footer class="bg-pnk text-center py-4 mt-5 border-top">
  <p class="mb-0" style="color:#3c3c3c;">© 2026 PNK Inmobiliaria - Todos los derechos reservados</p>
</footer>
</body>
</html>
