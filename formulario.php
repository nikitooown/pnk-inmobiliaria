<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bases de Datos</title>
  <link rel="stylesheet" href="css/mystyle.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

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
 <li class="dropdown">
      <a href="#">Formulario</a>
 </li>
</ul>
</header>


<form action="backend/registrar_usuario.php" method="POST" style="border:1px solid #ccc">
  <div class="container">
    <h1>Registrarse</h1>
    <p>Rellene el formulario</p>
    <hr>

    <label for="Rut"><b>Rut</b></label>
    <input type="text" placeholder="Ingrese su Rut" name="rut" required>

        <label for="nombre"><b>Nombre</b></label>
    <input type="text" placeholder="Ingrese su nombre" name="nombre" required>

        <label for="apellidos"><b>Apellidos</b></label>
    <input type="text" placeholder="Ingrese sus Apellidos" name="apellido" required>

        <label for="fecha_nacimiento"><b>Fecha nacimiento</b></label>
    <input type="date" placeholder="Ingrese su fecha de nacimiento" name="fecha_nacimiento" required>

    <label for="genero">Sexo:</label>
    <select id="genero" name="genero">
      <option value="Masculino">Masculino</option>
      <option value="Femenino">Femenino</option>
    </select>

        <label for="telefono"><b>Teléfono</b></label>
    <input type="text" placeholder="Ingrese su teléfono (+569)" name="telefono" required>

    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Ingrese su email" name="email" required>

    <label for="psw"><b>Contraseña</b></label>
    <input type="password" placeholder="Ingrese su contraseña" name="psw" required>

    <label for="psw-repeat"><b>Repita su contraseña</b></label>
    <input type="password" placeholder="Repita su contraseña" name="psw-repeat" required>

    <label for="certificado"><b>Certificado de Antecedentes</b></label>
    <input type="file" placeholder="Adjunte su Certificado de Antecedentes" name="certificado" required>

    <label>
    <input type="checkbox" checked="checked" name="Recordar" style="margin-bottom:15px"> Recuerdame
    </label>

<p>
  <label>
    <input type="checkbox" name="terminos" required>
    Acepto los <a href="#" style="color:dodgerblue">Términos y condiciones</a>.
  </label>
</p>
    <div class="clearfix">
    <button type="button" class="cancelbtn">Cancelar</button>
    <button type="submit" class="signupbtn">Registrarse</button>
    </div>
  </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>