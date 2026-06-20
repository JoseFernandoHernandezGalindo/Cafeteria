<?php

session_start();
if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';
    $correo    = trim($_POST['correo'] ?? '');
    $contrasena = md5($_POST['contrasena'] ?? '');
    $conn = conectar();
    $correo_esc = $conn->real_escape_string($correo);
    $resultado = $conn->query(
        "SELECT id FROM admin WHERE correo = '$correo_esc' AND contrasena = '$contrasena' LIMIT 1"
    );
    if ($resultado->num_rows === 1) {
        $_SESSION['admin'] = true;
        $_SESSION['correo'] = $correo;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Correo o contrasena incorrectos.';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administracion - Cafeteria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --cafe-oscuro: #3B1F0E;
      --cafe-claro:  #A0622A;
      --dorado:      #C8922A;
      --crema:       #F5EDD8;
      --crema-claro: #FBF7F0;
      --rojo:        #e74c3c;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Lato', sans-serif;
      background: linear-gradient(135deg, var(--cafe-oscuro) 0%, #6F3D1F 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .login-box {
      background: var(--crema-claro);
      border-radius: 16px;
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    }
    .login-logo { text-align: center; margin-bottom: 2rem; }
    .login-logo h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.9rem;
      color: var(--cafe-oscuro);
    }
    .login-logo p {
      font-size: 0.82rem;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--dorado);
      font-weight: 700;
      margin-top: 0.2rem;
    }
    .icono-taza { font-size: 3rem; display: block; margin-bottom: 0.5rem; }
    .grupo { margin-bottom: 1.2rem; }
    .grupo label {
      display: block;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--cafe-oscuro);
      margin-bottom: 0.4rem;
    }
    .input-wrap { position: relative; }
    .grupo input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 2px solid #ddd;
      border-radius: 10px;
      font-size: 0.95rem;
      font-family: 'Lato', sans-serif;
      background: white;
      transition: border 0.2s;
    }
    .grupo input:focus { outline: none; border-color: var(--cafe-claro); }
    .btn-ojo {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #999;
      transition: color 0.2s;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .btn-ojo:hover { color: var(--cafe-claro); }
    .btn-ojo svg { display: block; }
    #contrasena { padding-right: 2.8rem; }
    .btn-login {
      width: 100%;
      padding: 0.85rem;
      background: var(--cafe-claro);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 700;
      font-family: 'Lato', sans-serif;
      cursor: pointer;
      transition: background 0.25s;
      margin-top: 0.5rem;
    }
    .btn-login:hover { background: var(--cafe-oscuro); }
    .error-msg {
      background: #fdecea;
      color: var(--rojo);
      border-left: 4px solid var(--rojo);
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-size: 0.88rem;
      margin-bottom: 1rem;
    }
    .volver {
      display: block;
      text-align: center;
      margin-top: 1.25rem;
      color: var(--cafe-claro);
      text-decoration: none;
      font-size: 0.88rem;
    }
    .volver:hover { color: var(--cafe-oscuro); }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-logo">
      <h1>Cafeteria</h1>
      <p>Panel de Administracion</p>
    </div>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="grupo">
        <label for="correo">Correo electronico</label>
        <input type="email" id="correo" name="correo" placeholder="Correo" required/>
      </div>
      <div class="grupo">
        <label for="contrasena">Contrasena</label>
        <div class="input-wrap">
          <input type="password" id="contrasena" name="contrasena" placeholder="Tu contrasena" required/>
          <button class="btn-ojo" type="button" id="btn-ojo" title="Ver contrasena" onclick="toggleContrasena()">
            <svg id="ojo-abierto" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
              viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg id="ojo-cerrado" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
              viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              style="display:none">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
              <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-login">Iniciar sesion</button>
    </form>

    <a href="index.php" class="volver">← Volver a la cafeteria</a>
  </div>

  <script>
    function toggleContrasena() {
      const input      = document.getElementById('contrasena');
      const btn        = document.getElementById('btn-ojo');
      const ojoAbierto = document.getElementById('ojo-abierto');
      const ojoCerrado = document.getElementById('ojo-cerrado');
      if (input.type === 'password') {
        input.type = 'text';
        ojoAbierto.style.display = 'none';
        ojoCerrado.style.display = 'block';
        btn.title = 'Ocultar contrasena';
      } else {
        input.type = 'password';
        ojoAbierto.style.display = 'block';
        ojoCerrado.style.display = 'none';
        btn.title = 'Ver contrasena';
      }
    }
  </script>
</body>
</html>
