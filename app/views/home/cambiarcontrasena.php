<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireLogin();
$base = defined('BASE_URL') ? BASE_URL : '';
$toastError = $_SESSION['toast_error'] ?? '';
$toastOk    = $_SESSION['toast_ok'] ?? '';
unset($_SESSION['toast_error'], $_SESSION['toast_ok']);
$user = Auth::user() ?? [];
$userName = $user['Nombres'] ?? 'Usuario';
$dni = $user['Dni'] ?? $user['dni'] ?? '';
$nombres = trim((string)($user['Nombres'] ?? ''));
$apellidos = trim((string)($user['Apellidos'] ?? ''));
$nombreCompleto = trim($nombres . ' ' . $apellidos);
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Cambiar contraseña</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/cambiarcontrasena.css?v=1">
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
    <div class="section-head d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Cambiar contraseña</h6>
      <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
    <div class="formulario" autocomplete="off">
      <form method="post" action="cambiarcontrasena" id="cambiarcontrasena">

        <div class="position-relative mb-3">
          <i class="bi bi-shield-lock input-icon"></i>
          <input
            type="password"
            name="ActualPassword"
            id="ActualPassword"
            class="form-control"
            placeholder="Contraseña actual"
            autocomplete="new-password"
            autocorrect="off"
            autocapitalize="off"
            spellcheck="false">
        </div>
        <div class="position-relative mb-3">
          <i class="bi bi-shield-lock input-icon"></i>
          <input
            type="password"
            name="Password"
            id="Password"
            class="form-control"
            placeholder="Contraseña"
            autocomplete="new-password"
            autocorrect="off"
            autocapitalize="off"
            spellcheck="false">
        </div>
        <div class="position-relative mb-3">
          <i class="bi bi-shield-lock input-icon"></i>
          <input
            type="password"
            name="RepetirPassword"
            id="RepetirPassword"
            class="form-control"
            placeholder="Repetir contraseña"
            autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">
          Cambiar Contraseña <i class="bi-lock-fill"></i>
        </button>
      </form>
    </div>
  </div>

  <div class="spacer"></div>

  <nav class="tabbar" role="navigation" aria-label="Barra inferior">
    <div class="tabbar-inner">
      <a href="dashboard" class="item">
        <i class="bi bi-house"></i>
        <span>Inicio</span>
      </a>
      <a href="#" class="item">
        <i class="bi bi-chat-dots"></i>
        <span>Chat</span>
      </a>
      <a href="#" class="item">
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

  <div class="hlc-toast-wrap" id="hlcToastWrap" aria-live="polite" aria-atomic="true"></div>

  <script>
    window.HLC = window.HLC || {};
    window.HLC.base = <?= json_encode(rtrim($base, '/'), JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.toastError = <?= json_encode($toastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.toastOk = <?= json_encode($toastOk, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/hlc_incidencias/assets/js/cambiarcontrasena.js?v=1" defer></script>
</body>

</html>