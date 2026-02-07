<?php
$flashError = $flashError ?? ($_SESSION['flash_error'] ?? '');
$flashSuccess = $flashSuccess ?? ($_SESSION['flash_success'] ?? '');
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

$dni = $dni ?? ($_SESSION['reset_user']['Dni'] ?? '');
$nombres = $nombres ?? ($_SESSION['reset_user']['Nombres'] ?? '');
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Restablecer contraseña</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/auth_reseteo.css?v=1">
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
        <img src="../assets/img/logo_nuevo.png" alt="Hospital La Caleta — Nivel de Complejidad II-2" class="logo">
      </div>
    </div>

    <div class="hello-card">
      <div class="bubble"><i class="bi bi-heart-pulse"></i></div>
      <div>
        <h5>¡Hola!</h5>
        <p>Estamos aquí para ayudarte.</p>
      </div>
    </div>

    <div class="section-head d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Restablecer contraseña</h6>
      <a href="login" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <div class="formulario" autocomplete="off">
      <form method="post" action="contrasena">

        <div class="position-relative mb-14">
          <i class="bi bi-credit-card-2-front input-icon fs-5"></i>
          <input
            type="text"
            name="Dni"
            id="Dni"
            inputmode="numeric"
            class="form-control"
            placeholder="DNI / Documento"
            value="<?= htmlspecialchars((string)$dni, ENT_QUOTES, 'UTF-8') ?>"
            readonly="readonly"
            style="background:#FFFFFF"
            autocomplete="off">
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-person input-icon fs-5"></i>
          <input
            type="text"
            name="Nombres"
            id="Nombres"
            value="<?= htmlspecialchars((string)$nombres, ENT_QUOTES, 'UTF-8') ?>"
            class="form-control"
            placeholder="Nombre del usuario"
            autocomplete="off"
            readonly="readonly"
            style="background:#FFFFFF"
            inputmode="text">
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

        <p class="text-center mb-1">
          ¿Ya tienes una cuenta? &nbsp;
          <a href="login" style="text-decoration: none; color: #0d6efd;">¡Iniciar sesión!</a>
        </p>
      </form>
    </div>

    <br>
  </div>

 <div class="spacer"></div> 

  <nav class="tabbar" role="navigation" aria-label="Barra inferior">
    <div class="tabbar-inner">
      <a href="#" class="item">
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
      <a href="#" class="item">
        <i class="bi bi-person"></i>
        <span>Cuenta</span>
      </a>
    </div>
  </nav>

  <div class="hlc-toast-wrap" id="hlcToastWrap" aria-live="polite" aria-atomic="true"></div>

  <script>
    window.HLC = window.HLC || {};
    window.HLC.flashError = <?= json_encode($flashError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.flashSuccess = <?= json_encode($flashSuccess, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/hlc_incidencias/assets/js/auth_reseteo.js?v=1" defer></script>

</body>

</html>