<?php

require_once 'config.php';
$conn = conectar();

// Obtener menu
$menu = [];
$r = $conn->query("SELECT * FROM menu ORDER BY categoria, nombre");
while ($f = $r->fetch_assoc()) $menu[] = $f;

// Obtener noticias
$noticias = [];
$r = $conn->query("SELECT * FROM noticias ORDER BY fecha_creacion DESC");
while ($f = $r->fetch_assoc()) $noticias[] = $f;

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cafeteria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>

<nav>
  <a href="#" class="nav-logo">
    Cafeteria
    <span>Buen cafe - Buen sabor</span>
  </a>
  <ul class="nav-links">
    <li><a href="#menu">Menu</a></li>
    <li><a href="#noticias">Noticias</a></li>
    <li><a href="nosotros.php">Nosotros</a></li>
    <li><a href="login.php">Administrar</a></li>
  </ul>
</nav>

<header class="hero">
  <h1>Bienvenidos</h1>
  <p>Disfruta del mejor cafe - hecho con amor cada dia</p>
</header>

<!-- MENU -->
<section id="menu">
  <h2 class="section-titulo">Nuestro Menu</h2>
  <div class="section-linea"></div>

  <div class="filtros-menu">
    <button class="btn-filtro activo" onclick="filtrar('Todos', this)">Todos</button>
    <button class="btn-filtro" onclick="filtrar('Bebidas', this)">Bebidas</button>
    <button class="btn-filtro" onclick="filtrar('Comida', this)">Comida</button>
    <button class="btn-filtro" onclick="filtrar('Postres', this)">Postres</button>
    <button class="btn-filtro" onclick="filtrar('Desayunos', this)">Desayunos</button>
    <button class="btn-filtro" onclick="filtrar('Especiales', this)">Especiales</button>
  </div>

  <div class="menu-grid" id="menu-grid">
    <?php foreach ($menu as $p): ?>
      <div class="producto-card" data-categoria="<?= htmlspecialchars($p['categoria']) ?>">
        <?php if (!empty($p['imagen'])): ?>
          <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"/>
        <?php else: ?>
          <div class="img-placeholder"><span>*</span><small>Sin imagen</small></div>
        <?php endif; ?>
        <div class="producto-info">
          <div class="producto-categoria"><?= htmlspecialchars($p['categoria']) ?></div>
          <div class="producto-nombre">
            <?= htmlspecialchars($p['nombre']) ?>
            <?php if (!$p['disponible']): ?>
              <span class="badge-no-disponible">Agotado</span>
            <?php endif; ?>
          </div>
          <div class="producto-descripcion"><?= htmlspecialchars($p['descripcion']) ?></div>
          <div class="producto-precio">$<?= number_format($p['precio'], 2) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($menu)): ?>
      <p style="color:#999;text-align:center;grid-column:1/-1;padding:2rem;">No hay productos en el menu todavia.</p>
    <?php endif; ?>
  </div>
</section>

<!-- NOTICIAS -->
<div id="noticias" style="background:var(--crema);padding:4rem 2rem;">
  <h2 class="section-titulo">Noticias</h2>
  <div class="section-linea"></div>
  <div class="noticias-grid">
    <?php foreach ($noticias as $n): ?>
      <div class="noticia-card">
        <div class="noticia-fecha"><?= date('d/m/Y', strtotime($n['fecha'])) ?></div>
        <h3 class="noticia-titulo"><?= htmlspecialchars($n['titulo']) ?></h3>
        <p class="noticia-texto"><?= htmlspecialchars($n['texto']) ?></p>
      </div>
    <?php endforeach; ?>
    <?php if (empty($noticias)): ?>
      <p style="color:#999;text-align:center;grid-column:1/-1;">No hay noticias publicadas.</p>
    <?php endif; ?>
  </div>
</div>

<footer>
  <div style="max-width:1100px;margin:0 auto;display:flex;flex-direction:column;align-items:center;gap:1rem;">
    <div style="background:white;padding:0.6rem;border-radius:10px;display:inline-block;">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . '/nosotros.php') ?>"
           alt="Codigo QR - Conoce nuestra historia" width="120" height="120"/>
    </div>
    <p style="font-size:0.85rem;opacity:0.85;">Escanea para conocer nuestra historia, calidad del cafe y contacto</p>
    <p>&copy; 2025 Cafeteria &nbsp;·&nbsp; <a href="login.php">Administracion</a></p>
  </div>
</footer>

<script>
function filtrar(categoria, btn) {
  document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('activo'));
  btn.classList.add('activo');
  document.querySelectorAll('.producto-card').forEach(card => {
    if (categoria === 'Todos' || card.dataset.categoria === categoria) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>

</body>
</html>
