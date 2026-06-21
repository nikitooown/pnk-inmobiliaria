<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PNK Inmobiliaria - Catálogo</title>
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
      <li><a href="registro.php">Registrate</a></li>
      <li><a href="iniciosesion.php">Inicio Sesión</a></li>
      <li><a href="contacto.php">Contacto</a></li>
    </ul>
  </header>

  <section class="filtros_propiedad">
    <form>
      <label for="tipo_propiedad">Propiedad:</label>
      <select id="tipo_propiedad" name="tipo_propiedad">
        <option value="Casa">Casa</option>
        <option value="Departamento">Departamento</option>
        <option value="Terreno">Terreno</option>
      </select>

      <label for="provincia">Provincia:</label>
      <select id="provincia" name="provincia">
        <option value="Elqui">Elqui</option>
        <option value="Limarí">Limarí</option>
        <option value="Choapa">Choapa</option>
      </select>

      <label for="comuna">Comuna:</label>
      <select id="comuna" name="comuna">
        <option value="La Serena">La Serena</option>
        <option value="Coquimbo">Coquimbo</option>
        <option value="Andacollo">Andacollo</option>
        <option value="La Higuera">La Higuera</option>
        <option value="Paihuano">Paihuano</option>
        <option value="Vicuña">Vicuña</option>
      </select>

      <button type="button" onclick="window.location.href='catalogo.php'">Buscar</button>
    </form>
  </section>

  <!-- GRILLA DINÁMICA DE PROPIEDADES -->
  <section class="zona-tarjetas">

    <?php
    include_once("config/setup.php");

    $conexion = conectar();

    // Consultar solo propiedades Activas con su foto principal
    $sql = "SELECT p.id, p.titulo, p.tipo, p.comuna, p.provincia, p.precio,
                   (SELECT f.ruta FROM fotografias f WHERE f.id_propiedad = p.id AND f.es_principal = 1 LIMIT 1) AS foto_principal
            FROM propiedades p
            WHERE p.estado = 'Activa'
            ORDER BY p.fecha_creacion DESC";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
            $foto = $row['foto_principal'] ?? 'img/placeholder.jpg';
            $precio_formateado = number_format($row['precio'], 0, ',', '.');
    ?>
        <article class="tarjeta">
          <div class="caja-foto">
            <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($row['titulo']) ?>">
          </div>
          <div class="texto-propiedad">
            <h3><?= htmlspecialchars($row['titulo']) ?></h3>
            <p class="ubicacion">📍 <?= htmlspecialchars($row['comuna']) ?>, <?= htmlspecialchars($row['provincia']) ?></p>
            <p class="precio-destacado">$<?= $precio_formateado ?> UF</p>
          </div>
          <a href="detalle.php?id=<?= $row['id'] ?>"><button>Más info</button></a>
        </article>
    <?php
        endwhile;
    else:
    ?>
        <div style="width:100%; text-align:center; padding:60px 20px;">
          <h3>No hay propiedades activas disponibles en este momento.</h3>
          <p>Vuelve a consultar más tarde o contacta con nosotros.</p>
        </div>
    <?php
    endif;

    mysqli_stmt_close($stmt);
    ?>

  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>