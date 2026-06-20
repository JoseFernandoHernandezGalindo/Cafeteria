<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nosotros - Cafeteria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles.css"/>
  <style>
    .nosotros-hero {
      background: linear-gradient(135deg, var(--cafe-oscuro) 0%, var(--cafe-medio) 100%);
      color: var(--crema);
      text-align: center;
      padding: 4rem 1.5rem;
    }
    .nosotros-hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.4rem;
      color: var(--dorado);
      margin-bottom: 0.5rem;
      line-height: 1.2;
    }
    .nosotros-hero p {
      font-size: 1rem;
      opacity: 0.85;
    }

    .bloque {
      max-width: 850px;
      margin: 0 auto;
      padding: 3rem 1.5rem;
    }
    .bloque-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.7rem;
      color: var(--cafe-oscuro);
      margin-bottom: 1rem;
      border-left: 4px solid var(--dorado);
      padding-left: 1rem;
      line-height: 1.3;
    }
    .bloque-texto {
      font-size: 1rem;
      color: var(--gris-texto);
      line-height: 1.8;
    }

    .bloque-claro {
      background: var(--crema);
    }

    .tarjetas-calidad {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
      margin-top: 1.5rem;
    }
    .tarjeta-calidad {
      background: var(--blanco);
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: var(--sombra);
      text-align: center;
    }
    .tarjeta-calidad h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.1rem;
      color: var(--cafe-oscuro);
      margin-bottom: 0.5rem;
    }
    .tarjeta-calidad p {
      font-size: 0.9rem;
      color: var(--gris-texto);
    }

    .contacto-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-top: 1.5rem;
    }
    .contacto-item {
      background: var(--blanco);
      border-radius: 12px;
      padding: 1.5rem 1rem;
      box-shadow: var(--sombra);
      text-align: center;
      min-width: 0;
    }
    .contacto-item h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1rem;
      color: var(--dorado);
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .contacto-item p, .contacto-item a {
      font-size: 0.95rem;
      color: var(--cafe-oscuro);
      text-decoration: none;
      word-break: break-word;
      overflow-wrap: break-word;
    }
    .contacto-item a:hover { color: var(--cafe-claro); }

    .volver-inicio {
      display: block;
      text-align: center;
      padding: 2rem 1.5rem;
    }
    .volver-inicio a {
      color: var(--cafe-claro);
      text-decoration: none;
      font-weight: 700;
    }
    .volver-inicio a:hover { color: var(--cafe-oscuro); }

    /* ── Responsivo para telefono ── */
    @media (max-width: 640px) {
      .nosotros-hero { padding: 3rem 1rem; }
      .nosotros-hero h1 { font-size: 1.9rem; }
      .nosotros-hero p { font-size: 0.9rem; }

      .bloque { padding: 2.25rem 1.25rem; }
      .bloque-titulo { font-size: 1.4rem; padding-left: 0.75rem; }
      .bloque-texto { font-size: 0.95rem; }

      .tarjetas-calidad,
      .contacto-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .contacto-item a {
        font-size: 0.9rem !important;
        letter-spacing: normal !important;
        white-space: normal !important;
      }
    }
  </style>
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">
    Cafeteria
    <span>Buen cafe - Buen sabor</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php#menu">Menu</a></li>
    <li><a href="index.php#noticias">Noticias</a></li>
    <li><a href="nosotros.php">Nosotros</a></li>
    <li><a href="login.php">Administrar</a></li>
  </ul>
</nav>

<header class="nosotros-hero">
  <h1>Nuestra Historia</h1>
  <p>Origen, calidad y pasion en cada taza</p>
</header>

<section class="bloque">
  <h2 class="bloque-titulo">Nuestro Origen</h2>
  <p class="bloque-texto">
    Nuestra cafeteria nacio del amor por el buen cafe y el deseo de compartir esa pasion
    con nuestra comunidad. Comenzamos como un pequeno proyecto familiar, seleccionando
    cuidadosamente granos de las mejores regiones cafetaleras para ofrecer una experiencia
    autentica en cada taza. Hoy seguimos fieles a esa misma esencia: calidad, calidez y
    atencion personalizada.
  </p>
</section>

<section class="bloque bloque-claro">
  <h2 class="bloque-titulo">Calidad de Nuestro Cafe</h2>
  <p class="bloque-texto">
    Trabajamos con granos seleccionados y tostados en pequenos lotes para garantizar
    frescura y sabor en cada preparacion.
  </p>
  <div class="tarjetas-calidad">
    <div class="tarjeta-calidad">
      <h3>Grano seleccionado</h3>
      <p>Elegimos cuidadosamente cada lote de cafe arabica de alta calidad.</p>
    </div>
    <div class="tarjeta-calidad">
      <h3>Tostado artesanal</h3>
      <p>Tostamos en pequenas cantidades para resaltar el sabor natural del grano.</p>
    </div>
    <div class="tarjeta-calidad">
      <h3>Preparacion fresca</h3>
      <p>Cada bebida se prepara al momento, justo cuando la pides.</p>
    </div>
  </div>
</section>

<section class="bloque">
  <h2 class="bloque-titulo">Nuestra Pasion</h2>
  <p class="bloque-texto">
    Creemos que una buena taza de cafe puede alegrar el dia de cualquier persona. Por eso
    cuidamos cada detalle, desde el origen del grano hasta la sonrisa con la que te recibimos.
    Gracias por ser parte de esta historia y por confiar en nosotros cada vez que nos visitas.
  </p>
</section>

<section class="bloque bloque-claro">
  <h2 class="bloque-titulo">Contactanos</h2>
  <div class="contacto-grid">
    <div class="contacto-item">
      <h3>Telefono</h3>
      <a href="tel:+522321144445">+52 232 114 4445</a>
    </div>
    <div class="contacto-item">
      <h3>Correo</h3>
      <a href="mailto:jofernando@cafeteriabuena.com">jofernando@cafeteriabuena.com</a>
    </div>
    <div class="contacto-item">
      <h3>Direccion</h3>
      <p>Martinez de la Torre, Veracruz</p>
    </div>
  </div>
</section>

<div class="volver-inicio">
  <a href="index.php">&larr; Volver a la cafeteria</a>
</div>

<footer>
  <p>&copy; 2025 Cafeteria &nbsp;&middot;&nbsp; <a href="login.php">Administracion</a></p>
</footer>

</body>
</html>
