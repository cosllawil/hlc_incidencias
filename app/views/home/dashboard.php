<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireLogin();
$user = Auth::user() ?? [];
$userName = $user['Nombres'] ?? 'Usuario';
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Dashboard</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/home_home.css?v=1">
</head>

<body>
  <div class="hlc-preload" id="hlcPreload" role="dialog" aria-modal="true" aria-label="Cargando contenido">
    <div class="hlc-preload__panel" role="status" aria-live="polite">
      <div class="hlc-preload__spinner" aria-hidden="true">
        <svg viewBox="0 0 100 100">
          <circle class="track" cx="50" cy="50" r="34"></circle>
          <circle class="arc" cx="50" cy="50" r="34"></circle>
        </svg>
      </div>
      <div class="hlc-preload__text">Cargando...</div>
    </div>
  </div>

  <div class="screen">
    <div class="hero">
      <div class="text-white">
        <img src="/hlc_incidencias/assets/img/logo_nuevo.png" alt="Hospital La Caleta — Nivel de Complejidad II-2" class="logo">
      </div>
    </div>
    <div class="hello-card">
      <div class="bubble"><i class="bi bi-heart-pulse"></i></div>
      <div>
        <h5>¡Hola, <?= htmlspecialchars($userName) ?>!</h5>
        <p>Estamos aquí para ayudarte.</p>
      </div>
    </div>
    <div class="section-head">
      <h6>Incidencias</h6>
    </div>
    <div class="quick-grid">
      <a href="computadora" class="quick-card" data-action="agenda">
        <div class="icon"><i class="bi bi-tv fs-5"></i></div>
        <div class="label">Mi<br>Computadora</div>
      </a>
      <a href="impresora" class="quick-card" data-action="historial">
        <div class="icon"><i class="bi bi-printer fs-5"></i></div>
        <div class="label">Mi<br>Impresora</div>
      </a>

      <a href="internet" class="quick-card" data-action="medico">
        <div class="icon"><i class="bi bi-globe fs-5"></i></div>
        <div class="label">Mi<br>Internet</div>
      </a>
      <a href="luz" class="quick-card" data-action="admision">
        <div class="icon"><i class="bi bi-plug fs-5"></i></div>
        <div class="label">No hay<br>Luz</div>
      </a>
      <a href="telefono" class="quick-card" data-action="admision">
        <div class="icon"><i class="bi bi-telephone-forward fs-5"></i></div>
        <div class="label">Mi<br>Teléfono</div>
      </a>
      <a href="otros" class="quick-card" data-action="admision">
        <div class="icon"><i class="bi bi-tools fs-5"></i></div>
        <div class="label">Otros<br>Problemas</div>
      </a>
    </div>
    <div class="screen">
      <div class="wrap">
        <div class="section-head2">
          <h6>Historial de Incidencias</h6>
        </div>
        <a href="historial" class="app-card d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm text-decoration-none">
          <div class="d-flex align-items-center gap-3 flex-grow-1">
            <span class="d-flex align-items-center justify-content-center rounded-circle text-white" style="background-color:#00b0ff; width:42px; height:42px; font-size:1.2rem;">
              <i class="bi bi-x-diamond fs-5"></i>
            </span>
            <div class="app-card__text flex-grow-1">
              <span class="title d-block text-dark small">
                Aquí podrás ver tus incidencias
              </span>
            </div>
          </div>
          <i class="bi bi-chevron-right text-dark fs-5"></i>
        </a>
      </div>
    </div>
    <div class="screen">
      <div class="wrap">
        <div class="section-head2">
          <h6>Ayudanos a Mejorar</h6>
        </div>
        <a href="opinion" class="app-card d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm text-decoration-none">
          <div class="d-flex align-items-center gap-3 flex-grow-1">
            <span class="d-flex align-items-center justify-content-center rounded-circle text-white" style="background-color:#00b0ff; width:42px; height:42px; font-size:1.2rem;">
              <i class="bi bi-megaphone fs-5"></i>
            </span>
            <div class="app-card__text flex-grow-1">
              <span class="title d-block text-dark small">
                ¡Tu opinión es clave para mejorar!
              </span>
            </div>
          </div>
          <i class="bi bi-chevron-right text-dark fs-5"></i>
        </a>
      </div>
    </div>

    <div class="spacer"></div>

    <nav class="tabbar" role="navigation" aria-label="Barra inferior">
      <div class="tabbar-inner">
        <a href="dashboard" class="item active">
          <i class="bi bi-house"></i>
          <span>Inicio</span>
        </a>
        <a href="chat.php" class="item">
          <i class="bi bi-chat-dots"></i>
          <span>Chat</span>
        </a>
        <a href="atenciones.php" class="item">
          <i class="bi bi-card-checklist"></i>
          <span>Atenciones</span>
        </a>
        <a href="#" class="item">
          <i class="bi bi-bell"></i>
          <span>Avisos</span>
        </a>
        <a href="cuenta" class="item">
          <i class="bi bi-person"></i>
          <span>Cuenta</span>
        </a>
      </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/hlc_incidencias/assets/js/home_home.js?v=1" defer></script>
</body>

</html>