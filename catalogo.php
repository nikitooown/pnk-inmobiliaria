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
    <form method="get" action="catalogo.php">
      <label for="tipo">Propiedad:</label>
      <select id="tipo" name="tipo">
        <option value="">-- Todos --</option>
        <option value="Casa" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Casa') ? 'selected' : ''; ?>>Casa</option>
        <option value="Departamento" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Departamento') ? 'selected' : ''; ?>>Departamento</option>
        <option value="Terreno" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'Terreno') ? 'selected' : ''; ?>>Terreno</option>
      </select>

      <label for="comuna">Comuna:</label>
      <select id="comuna" name="comuna">
        <option value="">-- Todas --</option>
        <option value="La Serena" <?php echo (isset($_GET['comuna']) && $_GET['comuna'] === 'La Serena') ? 'selected' : ''; ?>>La Serena</option>
        <option value="Coquimbo" <?php echo (isset($_GET['comuna']) && $_GET['comuna'] === 'Coquimbo') ? 'selected' : ''; ?>>Coquimbo</option>
        <option value="Andacollo" <?php echo (isset($_GET['comuna']) && $_GET['comuna'] === 'Andacollo') ? 'selected' : ''; ?>>Andacollo</option>
        <option value="La Higuera" <?php echo (isset($_GET['comuna']) && $_GET['comuna'] === 'La Higuera') ? 'selected' : ''; ?>>La Higuera</option>
        <option value="Paihuano" <?php echo (isset($_GET['comuna']) && $_GET['comuna'] === 'Paihuano') ? 'selected' : ''; ?>>Paihuano</option>
        <option value="Vicuña" <?php echo (isset($_GET['comuna']) && $_GET['comuna'] === 'Vicuña') ? 'selected' : ''; ?>>Vicuña</option>
      </select>

      <label for="sector">Sector:</label>
      <input type="text" id="sector" name="sector" placeholder="Buscar por sector..." value="<?php echo htmlspecialchars($_GET['sector'] ?? ''); ?>">

      <button type="submit">Buscar</button>
    </form>
  </section>

  <!-- GRILLA DINÁMICA DE PROPIEDADES -->
  <section class="zona-tarjetas">

    <?php
    include_once("config/setup.php");

    $conexion = conectar();

    // Construir consulta SQL dinámicamente con filtros combinados
    $sql = "SELECT p.id, p.titulo, p.tipo, p.comuna, p.sector, p.precio, p.uf,
                  (SELECT f.ruta FROM fotografias f WHERE f.id_propiedad = p.id AND f.es_principal = 1 LIMIT 1) AS foto_principal
            FROM propiedades p
            WHERE p.estado = 'Publicada'";

    $params = [];
    $types = "";

    if (!empty($_GET['tipo'])) {
        $sql .= " AND p.tipo = ?";
        $params[] = $_GET['tipo'];
        $types .= "s";
    }

    if (!empty($_GET['comuna'])) {
        $sql .= " AND p.comuna LIKE ?";
        $params[] = '%' . $_GET['comuna'] . '%';
        $types .= "s";
    }

    if (!empty($_GET['sector'])) {
        $sql .= " AND p.sector LIKE ?";
        $params[] = '%' . $_GET['sector'] . '%';
        $types .= "s";
    }

    $sql .= " ORDER BY p.fecha_publicacion DESC";

    $stmt = mysqli_prepare($conexion, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
            $foto = $row['foto_principal'] ?? 'img/placeholder.jpg';
            $precio_formateado = number_format($row['precio'], 0, ',', '.');
            $uf_valor = $row['uf'] ?? 0;
            $uf_formateado = number_format($uf_valor, 2, ',', '.');
    ?>
        <article class="tarjeta">
          <div class="caja-foto">
            <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($row['titulo']) ?>">
          </div>
          <div class="texto-propiedad">
            <h3><?= htmlspecialchars($row['titulo']) ?></h3>
            <p class="ubicacion">📍 <?= htmlspecialchars($row['sector'] ? $row['sector'] . ', ' : '') ?><?= htmlspecialchars($row['comuna']) ?></p>
            <p class="precio-destacado">$<?= $precio_formateado ?></p>
            <?php if ($uf_valor > 0): ?>
              <p class="precio-uf">UF <?= $uf_formateado ?></p>
            <?php endif; ?>
          </div>
          <a href="detalle.php?id=<?= $row['id'] ?>" class="btn btn-primary">Quiero saber más!</a>
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

<footer class="bg-pnk text-center py-4 mt-5 border-top">
  <p class="mb-0" style="color:#3c3c3c;">© 2026 PNK Inmobiliaria - Todos los derechos reservados</p>
</footer>
</body>
</html>