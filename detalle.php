<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PNK Inmobiliaria - Detalle</title>
  <link rel="stylesheet" href="css/mystyle.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    .detalle-header {
      background-color: #dedbc1;
      padding: 20px 0;
      margin-bottom: 30px;
    }
    .detalle-header h1 {
      background-color: transparent;
      margin: 0;
      font-size: 2rem;
    }
    .carousel-detalle {
      max-width: 800px;
      margin: 0 auto 30px auto;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .carousel-detalle .carousel-item img {
      height: 450px;
      object-fit: cover;
      width: 100%;
    }
    .detalle-info {
      max-width: 800px;
      margin: 0 auto 40px auto;
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .detalle-info .descripcion {
      font-size: 1.05rem;
      line-height: 1.7;
      color: #555;
      margin-bottom: 25px;
    }
    .detalle-info .precio-grande {
      font-size: 1.8rem;
      font-weight: bold;
      color: #ff4500;
    }
    .icono-caracteristica {
      font-size: 1.8rem;
      color: #b0a78f;
      margin-bottom: 5px;
    }
    .caracteristica-item {
      text-align: center;
      padding: 15px 20px;
      background: #f8f7f2;
      border-radius: 10px;
      flex: 1;
      min-width: 100px;
    }
    .caracteristica-item .valor {
      font-size: 1.3rem;
      font-weight: bold;
      color: #3c3c3c;
    }
    .caracteristica-item .etiqueta {
      font-size: 0.85rem;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .caracteristicas-grid {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 25px;
    }
    .detalle-ubicacion {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #666;
      font-size: 1.1rem;
      margin-bottom: 20px;
    }
    .btn-volver {
      display: inline-block;
      padding: 10px 25px;
      background-color: #b0a78f;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .btn-volver:hover {
      background-color: #4a4444;
      color: white;
    }
    .no-foto-placeholder {
      height: 450px;
      background: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
      font-size: 1.2rem;
    }
    @media (max-width: 768px) {
      .carousel-detalle .carousel-item img {
        height: 280px;
      }
      .no-foto-placeholder {
        height: 280px;
      }
      .detalle-info {
        margin: 0 15px 30px 15px;
        padding: 20px;
      }
    }
  </style>
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
      <li><a href="catalogo.php">Catálogo</a></li>
      <li><a href="contacto.php">Contacto</a></li>
    </ul>
  </header>

  <?php
  include_once("config/setup.php");

  $id = (int) ($_GET['id'] ?? 0);

  if ($id <= 0) {
      echo '<div class="container mt-5"><div class="alert alert-danger">ID de propiedad no válido.</div>';
      echo '<a href="catalogo.php" class="btn-volver">← Volver al catálogo</a></div>';
      exit();
  }

  $conexion = conectar();

  // Obtener datos de la propiedad
  $sql = "SELECT * FROM propiedades WHERE id = ?";
  $stmt = mysqli_prepare($conexion, $sql);
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $propiedad = mysqli_fetch_assoc($result);
  mysqli_stmt_close($stmt);

  if (!$propiedad) {
      echo '<div class="container mt-5"><div class="alert alert-warning">Propiedad no encontrada.</div>';
      echo '<a href="catalogo.php" class="btn-volver">← Volver al catálogo</a></div>';
      exit();
  }

  // Obtener fotografías
  $sql_fotos = "SELECT id, ruta, es_principal FROM fotografias WHERE id_propiedad = ? ORDER BY es_principal DESC, id ASC";
  $stmt_fotos = mysqli_prepare($conexion, $sql_fotos);
  mysqli_stmt_bind_param($stmt_fotos, "i", $id);
  mysqli_stmt_execute($stmt_fotos);
  $result_fotos = mysqli_stmt_get_result($stmt_fotos);
  $fotografias = [];
  while ($foto = mysqli_fetch_assoc($result_fotos)) {
      $fotografias[] = $foto;
  }
  mysqli_stmt_close($stmt_fotos);

  $precio_formateado = number_format($propiedad['precio'], 0, ',', '.');
  $total_fotos = count($fotografias);
  ?>

  <div class="detalle-header">
    <div class="container text-center">
      <h1><?= htmlspecialchars($propiedad['titulo']) ?></h1>
      <p class="detalle-ubicacion" style="justify-content:center;">
        <i class="bi bi-geo-alt-fill"></i>
        <?= htmlspecialchars($propiedad['comuna']) ?>, <?= htmlspecialchars($propiedad['provincia']) ?>
        <?php if (!empty($propiedad['direccion'])): ?>
          — <?= htmlspecialchars($propiedad['direccion']) ?>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <div class="container">

    <!-- CARRUSEL BOOTSTRAP 5 -->
    <div class="carousel-detalle">
      <?php if ($total_fotos > 0): ?>
        <div id="carouselPropiedad" class="carousel slide" data-bs-ride="carousel">

          <!-- Indicadores -->
          <div class="carousel-indicators">
            <?php for ($i = 0; $i < $total_fotos; $i++): ?>
              <button type="button" data-bs-target="#carouselPropiedad" data-bs-slide-to="<?= $i ?>" 
                      class="<?= $i === 0 ? 'active' : '' ?>" 
                      aria-label="Foto <?= $i + 1 ?>"></button>
            <?php endfor; ?>
          </div>

          <!-- Slides -->
          <div class="carousel-inner">
            <?php foreach ($fotografias as $index => $foto): ?>
              <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img src="<?= htmlspecialchars($foto['ruta']) ?>" 
                     class="d-block w-100" 
                     alt="Foto <?= $index + 1 ?> de <?= htmlspecialchars($propiedad['titulo']) ?>">
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Controles -->
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselPropiedad" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselPropiedad" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </div>
      <?php else: ?>
        <div class="no-foto-placeholder">
          <i class="bi bi-image" style="font-size:3rem; margin-right:10px;"></i> Sin imágenes disponibles
        </div>
      <?php endif; ?>
    </div>

    <!-- INFORMACIÓN DE LA PROPIEDAD -->
    <div class="detalle-info">

      <!-- Características con Bootstrap Icons -->
      <div class="caracteristicas-grid">
        <div class="caracteristica-item">
          <div class="icono-caracteristica"><i class="bi bi-door-open"></i></div>
          <div class="valor"><?= (int) $propiedad['habitaciones'] ?></div>
          <div class="etiqueta">Habitaciones</div>
        </div>
        <div class="caracteristica-item">
          <div class="icono-caracteristica"><i class="bi bi-droplet"></i></div>
          <div class="valor"><?= (int) $propiedad['banos'] ?></div>
          <div class="etiqueta">Baños</div>
        </div>
        <div class="caracteristica-item">
          <div class="icono-caracteristica"><i class="bi bi-arrows-angle-expand"></i></div>
          <div class="valor"><?= (int) $propiedad['m2'] ?></div>
          <div class="etiqueta">m²</div>
        </div>
        <div class="caracteristica-item">
          <div class="icono-caracteristica"><i class="bi bi-house-door"></i></div>
          <div class="valor"><?= htmlspecialchars($propiedad['tipo']) ?></div>
          <div class="etiqueta">Tipo</div>
        </div>
      </div>

      <!-- Precio -->
      <p class="precio-grande">$<?= $precio_formateado ?> UF</p>

      <!-- Descripción -->
      <?php if (!empty($propiedad['descripcion'])): ?>
        <div class="descripcion">
          <?= nl2br(htmlspecialchars($propiedad['descripcion'])) ?>
        </div>
      <?php endif; ?>

      <!-- Botón volver -->
      <a href="catalogo.php" class="btn-volver">← Volver al catálogo</a>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>