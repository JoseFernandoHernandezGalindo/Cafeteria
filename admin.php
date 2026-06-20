<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';
$conn = conectar();

$total_productos = $conn->query("SELECT COUNT(*) as c FROM menu")->fetch_assoc()['c'];
$total_pedidos   = $conn->query("SELECT COUNT(*) as c FROM pedidos")->fetch_assoc()['c'];
$pendientes      = $conn->query("SELECT COUNT(*) as c FROM pedidos WHERE estado='Pendiente'")->fetch_assoc()['c'];
$total_noticias  = $conn->query("SELECT COUNT(*) as c FROM noticias")->fetch_assoc()['c'];

$menu = [];
$r = $conn->query("SELECT * FROM menu ORDER BY categoria, nombre");
while ($f = $r->fetch_assoc()) $menu[] = $f;

$pedidos = [];
$r = $conn->query("SELECT * FROM pedidos ORDER BY fecha DESC");
while ($f = $r->fetch_assoc()) $pedidos[] = $f;

$noticias = [];
$r = $conn->query("SELECT * FROM noticias ORDER BY fecha_creacion DESC");
while ($f = $r->fetch_assoc()) $noticias[] = $f;

$conn->close();

function badgeEstado($estado) {
    $clases = ['Pendiente'=>'badge-pendiente','En proceso'=>'badge-proceso','Listo'=>'badge-listo','Cancelado'=>'badge-cancelado'];
    $clase = $clases[$estado] ?? '';
    return "<span class='badge $clase'>$estado</span>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel Admin - Cafeteria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="admin.css"/>
  <style>
    .filtros-admin {
      display: flex;
      gap: 0.6rem;
      flex-wrap: wrap;
      margin-bottom: 1.25rem;
    }
    .filtro-btn {
      padding: 0.4rem 1rem;
      border: 2px solid var(--cafe-claro);
      background: transparent;
      color: var(--cafe-claro);
      border-radius: 20px;
      cursor: pointer;
      font-family: 'Lato', sans-serif;
      font-size: 0.85rem;
      font-weight: 700;
      transition: all 0.2s;
    }
    .filtro-btn:hover, .filtro-btn.activo {
      background: var(--cafe-claro);
      color: white;
    }

    .producto-admin-card { position: relative; }
    .overlay-editar {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(59,31,14,0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.25s;
      border-radius: 12px;
      cursor: pointer;
    }
    .producto-admin-card:hover .overlay-editar { opacity: 1; }
    .overlay-editar svg { color: white; width: 36px; height: 36px; }
    .overlay-editar span {
      color: white;
      font-family: 'Lato', sans-serif;
      font-size: 0.9rem;
      font-weight: 700;
      margin-top: 0.4rem;
    }
    .overlay-editar-inner { display: flex; flex-direction: column; align-items: center; gap: 0.3rem; }

    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
      z-index: 999;
      align-items: center;
      justify-content: center;
    }
    .modal-overlay.visible { display: flex; }
    .modal-box {
      background: white;
      border-radius: 16px;
      padding: 2rem;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      margin-bottom: 1.25rem;
      padding-bottom: 0.75rem;
      border-bottom: 2px solid var(--crema);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .modal-cerrar {
      background: none;
      border: none;
      font-size: 1.4rem;
      cursor: pointer;
      color: #999;
      line-height: 1;
    }
    .modal-cerrar:hover { color: var(--rojo); }

    /* Buscador de pedidos */
    .buscador-pedidos {
      display: flex;
      gap: 0.6rem;
      margin-bottom: 1rem;
      align-items: center;
    }
    .buscador-pedidos input {
      padding: 0.6rem 1rem;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-family: 'Lato', sans-serif;
      font-size: 0.9rem;
      max-width: 260px;
      flex: 1;
    }
    .buscador-pedidos input:focus {
      outline: none;
      border-color: var(--cafe-claro);
    }
    .btn-editar-pedido {
      background: var(--cafe-claro);
      color: white;
    }
    .btn-editar-pedido:hover { background: var(--cafe-oscuro); }
  </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <h2>Cafeteria</h2>
    <p>ADMINISTRACION</p>
  </div>
  <ul class="sidebar-menu">
    <li><a href="#" class="activo" onclick="mostrarPanel('inicio',this)">Inicio</a></li>
    <li><a href="#" onclick="mostrarPanel('pedidos',this)">Pedidos</a></li>
    <li><a href="#" onclick="mostrarPanel('menu',this)">Menu</a></li>
    <li><a href="#" onclick="mostrarPanel('noticias',this)">Noticias</a></li>
  </ul>
  <div class="sidebar-salir">
    <a href="logout.php">Cerrar sesion</a>
  </div>
</aside>

<main class="contenido">

  <!-- INICIO -->
  <div id="panel-inicio" class="panel activo">
    <h1 class="pagina-titulo">Bienvenido</h1>
    <p class="pagina-subtitulo">Resumen general de tu cafeteria</p>
    <div class="stats-grid">
      <div class="stat-card"><div class="stat-numero"><?= $total_productos ?></div><div class="stat-label">Productos en menu</div></div>
      <div class="stat-card"><div class="stat-numero"><?= $total_pedidos ?></div><div class="stat-label">Pedidos totales</div></div>
      <div class="stat-card"><div class="stat-numero"><?= $pendientes ?></div><div class="stat-label">Pedidos pendientes</div></div>
      <div class="stat-card"><div class="stat-numero"><?= $total_noticias ?></div><div class="stat-label">Noticias publicadas</div></div>
    </div>
    <div class="tabla-contenedor" style="padding:1.5rem;">
      <h3 style="font-family:'Playfair Display',serif;margin-bottom:1rem;">Accesos rapidos</h3>
      <div style="display:flex;gap:1rem;flex-wrap:wrap;">
        <button class="btn btn-primario" onclick="mostrarPanel('pedidos',null)">Ver pedidos</button>
        <button class="btn btn-dorado"   onclick="mostrarPanel('menu',null)">Agregar producto</button>
        <button class="btn btn-verde"    onclick="mostrarPanel('noticias',null)">Nueva noticia</button>
      </div>
    </div>
  </div>

  <!-- PEDIDOS -->
  <div id="panel-pedidos" class="panel">
    <h1 class="pagina-titulo">Pedidos</h1>
    <p class="pagina-subtitulo">Administra y actualiza el estado de los pedidos</p>

    <div class="form-card">
      <h3>Registrar nuevo pedido</h3>
      <div id="alerta-pedido" class="alerta alerta-ok"></div>
      <div class="form-fila">
        <div class="form-grupo"><label>Nombre del cliente</label><input type="text" id="pedido-cliente" placeholder="Ej. Maria Garcia"/></div>
        <div class="form-grupo"><label>Productos</label><input type="text" id="pedido-productos" placeholder="Ej. 2 cafe americano"/></div>
      </div>
      <div class="form-grupo"><label>Total ($)</label><input type="number" id="pedido-total" placeholder="Ej. 135"/></div>
      <button class="btn btn-primario" onclick="agregarPedido()">Registrar pedido</button>
    </div>

    <div class="buscador-pedidos">
      <input type="text" id="buscador-id-pedido" placeholder="Buscar por numero de pedido (ej. 12)" oninput="buscarPedido()"/>
      <button class="btn btn-gris btn-sm" onclick="limpiarBusqueda()">Limpiar busqueda</button>
    </div>

    <div class="tabla-contenedor">
      <div class="tabla-header">
        <h3>Lista de pedidos</h3>
        <button class="btn btn-rojo btn-sm" onclick="limpiarPedidos()">Limpiar todos</button>
      </div>
      <div style="overflow-x:auto;">
        <table>
          <thead><tr><th>#</th><th>Cliente</th><th>Productos</th><th>Total</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody id="tabla-pedidos">
            <?php foreach ($pedidos as $p): ?>
            <tr id="fila-pedido-<?= $p['id'] ?>" data-id="<?= $p['id'] ?>">
              <td><small>#<?= $p['id'] ?></small></td>
              <td><strong><?= htmlspecialchars($p['cliente']) ?></strong></td>
              <td><?= htmlspecialchars($p['productos']) ?></td>
              <td><strong>$<?= number_format($p['total'],2) ?></strong></td>
              <td><small><?= $p['fecha'] ?></small></td>
              <td><?= badgeEstado($p['estado']) ?></td>
              <td class="acciones-tabla">
                <select class="estado-select" onchange="cambiarEstado(<?= $p['id'] ?>, this.value)">
                  <?php foreach (['Pendiente','En proceso','Listo','Cancelado'] as $e): ?>
                    <option <?= $p['estado']===$e?'selected':'' ?>><?= $e ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-editar-pedido btn-sm" onclick="abrirEditarPedido(
                  <?= $p['id'] ?>,
                  <?= htmlspecialchars(json_encode($p['cliente'])) ?>,
                  <?= htmlspecialchars(json_encode($p['productos'])) ?>,
                  <?= $p['total'] ?>
                )">Editar</button>
                <button class="btn btn-rojo btn-sm" onclick="eliminarPedido(<?= $p['id'] ?>)">Eliminar</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pedidos)): ?>
            <tr><td colspan="7" style="text-align:center;color:#999;padding:2rem;">No hay pedidos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MENU -->
  <div id="panel-menu" class="panel">
    <h1 class="pagina-titulo">Menu</h1>
    <p class="pagina-subtitulo">Agrega, edita y administra los productos del menu</p>

    <div class="form-card">
      <h3>Agregar nuevo producto</h3>
      <div id="alerta-menu" class="alerta alerta-ok"></div>
      <div class="form-fila">
        <div class="form-grupo"><label>Nombre *</label><input type="text" id="prod-nombre" placeholder="Ej. Cafe Latte"/></div>
        <div class="form-grupo"><label>Categoria *</label>
          <select id="prod-categoria">
            <option>Bebidas</option><option>Comida</option><option>Postres</option><option>Desayunos</option><option>Especiales</option>
          </select>
        </div>
      </div>
      <div class="form-grupo"><label>Descripcion</label><textarea id="prod-descripcion" placeholder="Descripcion del producto..."></textarea></div>
      <div class="form-grupo"><label>Precio ($) *</label><input type="number" id="prod-precio" placeholder="Ej. 45"/></div>
      <div class="form-grupo">
        <label>Imagen del producto</label>
        <div class="zona-imagen" onclick="document.getElementById('input-imagen').click()">
          <p><strong>Haz clic aqui para subir la foto</strong></p>
          <p>Formatos: JPG, PNG — Maximo 2 MB</p>
          <input type="file" id="input-imagen" accept="image/*" onchange="previsualizarImagen(this, 'preview-img')"/>
          <img id="preview-img" class="preview-imagen" alt="Vista previa"/>
        </div>
      </div>
      <button class="btn btn-primario" onclick="agregarProducto()">Guardar producto</button>
    </div>

    <div class="filtros-admin">
      <button class="filtro-btn activo" onclick="filtrarProductos('Todos', this)">Todos</button>
      <button class="filtro-btn" onclick="filtrarProductos('Bebidas', this)">Bebidas</button>
      <button class="filtro-btn" onclick="filtrarProductos('Comida', this)">Comida</button>
      <button class="filtro-btn" onclick="filtrarProductos('Postres', this)">Postres</button>
      <button class="filtro-btn" onclick="filtrarProductos('Desayunos', this)">Desayunos</button>
      <button class="filtro-btn" onclick="filtrarProductos('Especiales', this)">Especiales</button>
    </div>

    <div id="productos-admin-grid" class="productos-admin-grid">
      <?php foreach ($menu as $p): ?>
      <div class="producto-admin-card" id="prod-card-<?= $p['id'] ?>" data-categoria="<?= htmlspecialchars($p['categoria']) ?>">
        <div class="overlay-editar" onclick="abrirEditar(
          <?= $p['id'] ?>,
          <?= htmlspecialchars(json_encode($p['nombre'])) ?>,
          <?= htmlspecialchars(json_encode($p['descripcion'])) ?>,
          <?= $p['precio'] ?>,
          <?= htmlspecialchars(json_encode($p['categoria'])) ?>,
          <?= htmlspecialchars(json_encode($p['imagen'] ?? '')) ?>
        )">
          <div class="overlay-editar-inner">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Editar</span>
          </div>
        </div>
        <?php if (!empty($p['imagen'])): ?>
          <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"/>
        <?php else: ?>
          <div class="img-ph"></div>
        <?php endif; ?>
        <div class="producto-admin-info">
          <div class="producto-admin-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
          <div class="producto-admin-precio">$<?= number_format($p['precio'],2) ?> · <?= htmlspecialchars($p['categoria']) ?></div>
          <span class="badge <?= $p['disponible'] ? 'badge-disponible' : 'badge-agotado' ?>"><?= $p['disponible'] ? 'Disponible' : 'Agotado' ?></span>
          <div class="producto-admin-acciones" style="margin-top:0.6rem;">
            <button class="btn btn-dorado btn-sm" onclick="toggleDisponible(<?= $p['id'] ?>)"><?= $p['disponible'] ? 'Marcar agotado' : 'Marcar disponible' ?></button>
            <button class="btn btn-rojo btn-sm" onclick="eliminarProducto(<?= $p['id'] ?>)">Eliminar</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- NOTICIAS -->
  <div id="panel-noticias" class="panel">
    <h1 class="pagina-titulo">Noticias</h1>
    <p class="pagina-subtitulo">Publica avisos y novedades de tu cafeteria</p>
    <div class="form-card">
      <h3>Nueva noticia</h3>
      <div id="alerta-noticias" class="alerta alerta-ok"></div>
      <div class="form-grupo"><label>Titulo *</label><input type="text" id="noticia-titulo" placeholder="Ej. Nuevo menu de temporada"/></div>
      <div class="form-grupo"><label>Contenido *</label><textarea id="noticia-texto" rows="4" placeholder="Escribe el mensaje..."></textarea></div>
      <button class="btn btn-verde" onclick="agregarNoticia()">Publicar noticia</button>
    </div>
    <div class="tabla-contenedor">
      <div class="tabla-header"><h3>Noticias publicadas</h3></div>
      <div style="overflow-x:auto;">
        <table>
          <thead><tr><th>Titulo</th><th>Fecha</th><th>Vista previa</th><th>Acciones</th></tr></thead>
          <tbody id="tabla-noticias">
            <?php foreach ($noticias as $n): ?>
            <tr id="fila-noticia-<?= $n['id'] ?>">
              <td><strong><?= htmlspecialchars($n['titulo']) ?></strong></td>
              <td><small><?= $n['fecha'] ?></small></td>
              <td style="max-width:250px;font-size:0.85rem;color:#666;"><?= htmlspecialchars(substr($n['texto'],0,80)) ?>...</td>
              <td><button class="btn btn-rojo btn-sm" onclick="eliminarNoticia(<?= $n['id'] ?>)">Eliminar</button></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($noticias)): ?>
            <tr><td colspan="4" style="text-align:center;color:#999;padding:2rem;">No hay noticias publicadas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</main>

<!-- MODAL EDITAR PRODUCTO -->
<div class="modal-overlay" id="modal-editar">
  <div class="modal-box">
    <div class="modal-titulo">
      Editar producto
      <button class="modal-cerrar" onclick="cerrarModal('modal-editar')">&#10005;</button>
    </div>
    <input type="hidden" id="edit-id"/>
    <div id="alerta-editar" class="alerta alerta-ok"></div>
    <div class="form-fila">
      <div class="form-grupo"><label>Nombre *</label><input type="text" id="edit-nombre"/></div>
      <div class="form-grupo"><label>Categoria *</label>
        <select id="edit-categoria">
          <option>Bebidas</option><option>Comida</option><option>Postres</option><option>Desayunos</option><option>Especiales</option>
        </select>
      </div>
    </div>
    <div class="form-grupo"><label>Descripcion</label><textarea id="edit-descripcion" rows="3"></textarea></div>
    <div class="form-grupo"><label>Precio ($) *</label><input type="number" id="edit-precio"/></div>
    <div class="form-grupo">
      <label>Imagen</label>
      <div class="zona-imagen" onclick="document.getElementById('edit-imagen-input').click()">
        <p><strong>Haz clic para cambiar la foto</strong></p>
        <input type="file" id="edit-imagen-input" accept="image/*" onchange="previsualizarImagen(this, 'edit-preview')"/>
        <img id="edit-preview" class="preview-imagen" alt="Vista previa"/>
      </div>
    </div>
    <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1rem;">
      <button class="btn btn-gris" onclick="cerrarModal('modal-editar')">Cancelar</button>
      <button class="btn btn-primario" onclick="guardarEdicion()">Guardar cambios</button>
    </div>
  </div>
</div>

<!-- MODAL EDITAR PEDIDO -->
<div class="modal-overlay" id="modal-editar-pedido">
  <div class="modal-box">
    <div class="modal-titulo">
      Editar pedido
      <button class="modal-cerrar" onclick="cerrarModal('modal-editar-pedido')">&#10005;</button>
    </div>
    <input type="hidden" id="editp-id"/>
    <div id="alerta-editar-pedido" class="alerta alerta-ok"></div>
    <div class="form-grupo"><label>Nombre del cliente *</label><input type="text" id="editp-cliente"/></div>
    <div class="form-grupo"><label>Productos *</label><input type="text" id="editp-productos"/></div>
    <div class="form-grupo"><label>Total ($) *</label><input type="number" id="editp-total"/></div>
    <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1rem;">
      <button class="btn btn-gris" onclick="cerrarModal('modal-editar-pedido')">Cancelar</button>
      <button class="btn btn-primario" onclick="guardarEdicionPedido()">Guardar cambios</button>
    </div>
  </div>
</div>

<script>
function mostrarPanel(nombre, linkEl) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('activo'));
  document.getElementById('panel-' + nombre).classList.add('activo');
  document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('activo'));
  if (linkEl) linkEl.classList.add('activo');
  return false;
}

function mostrarAlerta(id, msg, tipo) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.className = 'alerta ' + (tipo === 'error' ? 'alerta-error' : 'alerta-ok') + ' visible';
  setTimeout(() => el.classList.remove('visible'), 3500);
}

function cerrarModal(id) {
  document.getElementById(id).classList.remove('visible');
}

document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('visible'); });
});

// ── Filtrar productos ──────────────────────────────────────
function filtrarProductos(categoria, btn) {
  document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('activo'));
  btn.classList.add('activo');
  document.querySelectorAll('.producto-admin-card').forEach(card => {
    card.style.display = (categoria === 'Todos' || card.dataset.categoria === categoria) ? 'block' : 'none';
  });
}

// ── Buscador de pedidos por ID ─────────────────────────────
function buscarPedido() {
  const valor = document.getElementById('buscador-id-pedido').value.trim();
  document.querySelectorAll('#tabla-pedidos tr[data-id]').forEach(fila => {
    const id = fila.dataset.id;
    fila.style.display = (valor === '' || id.includes(valor)) ? '' : 'none';
  });
}

function limpiarBusqueda() {
  document.getElementById('buscador-id-pedido').value = '';
  buscarPedido();
}

// ── Editar producto ────────────────────────────────────────
let editImagenBase64 = null;

function abrirEditar(id, nombre, descripcion, precio, categoria, imagen) {
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-nombre').value = nombre;
  document.getElementById('edit-descripcion').value = descripcion;
  document.getElementById('edit-precio').value = precio;
  document.getElementById('edit-categoria').value = categoria;
  const preview = document.getElementById('edit-preview');
  if (imagen) { preview.src = imagen; preview.style.display = 'block'; }
  else { preview.style.display = 'none'; }
  editImagenBase64 = imagen || null;
  document.getElementById('modal-editar').classList.add('visible');
}

function guardarEdicion() {
  const id = document.getElementById('edit-id').value;
  const nombre = document.getElementById('edit-nombre').value.trim();
  const descripcion = document.getElementById('edit-descripcion').value.trim();
  const precio = document.getElementById('edit-precio').value;
  const categoria = document.getElementById('edit-categoria').value;
  if (!nombre || !precio) { alert('El nombre y precio son obligatorios.'); return; }

  const fd = new FormData();
  fd.append('accion', 'editar'); fd.append('id', id); fd.append('nombre', nombre);
  fd.append('descripcion', descripcion); fd.append('precio', precio);
  fd.append('categoria', categoria); fd.append('imagen', editImagenBase64 || '');

  fetch('api/menu.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) {
      mostrarAlerta('alerta-editar', 'Producto actualizado correctamente.');
      setTimeout(() => { cerrarModal('modal-editar'); location.reload(); }, 1200);
    } else {
      mostrarAlerta('alerta-editar', 'Error al guardar.', 'error');
    }
  });
}

// ── Editar pedido ──────────────────────────────────────────
function abrirEditarPedido(id, cliente, productos, total) {
  document.getElementById('editp-id').value = id;
  document.getElementById('editp-cliente').value = cliente;
  document.getElementById('editp-productos').value = productos;
  document.getElementById('editp-total').value = total;
  document.getElementById('modal-editar-pedido').classList.add('visible');
}

function guardarEdicionPedido() {
  const id = document.getElementById('editp-id').value;
  const cliente = document.getElementById('editp-cliente').value.trim();
  const productos = document.getElementById('editp-productos').value.trim();
  const total = document.getElementById('editp-total').value;
  if (!cliente || !productos || !total) { alert('Todos los campos son obligatorios.'); return; }

  const fd = new FormData();
  fd.append('accion', 'editar'); fd.append('id', id); fd.append('cliente', cliente);
  fd.append('productos', productos); fd.append('total', total);

  fetch('api/pedidos.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) {
      mostrarAlerta('alerta-editar-pedido', 'Pedido actualizado correctamente.');
      setTimeout(() => { cerrarModal('modal-editar-pedido'); location.reload(); }, 1200);
    } else {
      mostrarAlerta('alerta-editar-pedido', 'Error al guardar.', 'error');
    }
  });
}

// ── Imagen preview ─────────────────────────────────────────
let imagenBase64 = null;

function previsualizarImagen(input, previewId) {
  const archivo = input.files[0];
  if (!archivo) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    const data = e.target.result;
    if (previewId === 'edit-preview') editImagenBase64 = data;
    else imagenBase64 = data;
    const preview = document.getElementById(previewId);
    preview.src = data;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(archivo);
}

// ── Pedidos ────────────────────────────────────────────────
function agregarPedido() {
  const cliente = document.getElementById('pedido-cliente').value.trim();
  const productos = document.getElementById('pedido-productos').value.trim();
  const total = document.getElementById('pedido-total').value;
  if (!cliente || !productos || !total) { alert('Llena todos los campos del pedido.'); return; }
  const fd = new FormData();
  fd.append('accion', 'agregar'); fd.append('cliente', cliente);
  fd.append('productos', productos); fd.append('total', total);
  fetch('api/pedidos.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) { mostrarAlerta('alerta-pedido', 'Pedido registrado.'); location.reload(); }
  });
}

function cambiarEstado(id, estado) {
  const fd = new FormData();
  fd.append('accion', 'estado'); fd.append('id', id); fd.append('estado', estado);
  fetch('api/pedidos.php', { method:'POST', body:fd });
}

function eliminarPedido(id) {
  if (!confirm('Eliminar este pedido?')) return;
  const fd = new FormData();
  fd.append('accion', 'eliminar'); fd.append('id', id);
  fetch('api/pedidos.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) document.getElementById('fila-pedido-' + id).remove();
  });
}

function limpiarPedidos() {
  if (!confirm('Borrar TODOS los pedidos?')) return;
  const fd = new FormData(); fd.append('accion', 'limpiar');
  fetch('api/pedidos.php', { method:'POST', body:fd }).then(() => location.reload());
}

// ── Menu ───────────────────────────────────────────────────
function agregarProducto() {
  const nombre = document.getElementById('prod-nombre').value.trim();
  const categoria = document.getElementById('prod-categoria').value;
  const descripcion = document.getElementById('prod-descripcion').value.trim();
  const precio = document.getElementById('prod-precio').value;
  if (!nombre || !precio) { alert('El nombre y el precio son obligatorios.'); return; }
  const fd = new FormData();
  fd.append('accion', 'agregar'); fd.append('nombre', nombre); fd.append('categoria', categoria);
  fd.append('descripcion', descripcion); fd.append('precio', precio); fd.append('imagen', imagenBase64 || '');
  fetch('api/menu.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) {
      mostrarAlerta('alerta-menu', 'Producto guardado correctamente.');
      document.getElementById('prod-nombre').value = '';
      document.getElementById('prod-descripcion').value = '';
      document.getElementById('prod-precio').value = '';
      document.getElementById('input-imagen').value = '';
      document.getElementById('preview-img').style.display = 'none';
      imagenBase64 = null;
      location.reload();
    }
  });
}

function toggleDisponible(id) {
  const fd = new FormData(); fd.append('accion', 'toggle'); fd.append('id', id);
  fetch('api/menu.php', { method:'POST', body:fd }).then(() => location.reload());
}

function eliminarProducto(id) {
  if (!confirm('Eliminar este producto del menu?')) return;
  const fd = new FormData(); fd.append('accion', 'eliminar'); fd.append('id', id);
  fetch('api/menu.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) document.getElementById('prod-card-' + id).remove();
  });
}

// ── Noticias ───────────────────────────────────────────────
function agregarNoticia() {
  const titulo = document.getElementById('noticia-titulo').value.trim();
  const texto = document.getElementById('noticia-texto').value.trim();
  if (!titulo || !texto) { alert('El titulo y el contenido son obligatorios.'); return; }
  const fd = new FormData();
  fd.append('accion', 'agregar'); fd.append('titulo', titulo); fd.append('texto', texto);
  fetch('api/noticias.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) { mostrarAlerta('alerta-noticias', 'Noticia publicada.'); location.reload(); }
  });
}

function eliminarNoticia(id) {
  if (!confirm('Eliminar esta noticia?')) return;
  const fd = new FormData(); fd.append('accion', 'eliminar'); fd.append('id', id);
  fetch('api/noticias.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{
    if (d.ok) document.getElementById('fila-noticia-' + id).remove();
  });
}
</script>

</body>
</html>
