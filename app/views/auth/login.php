<?php
$flashError = $flashError ?? ($_SESSION['flash_error'] ?? '');
$flashSuccess = $flashSuccess ?? ($_SESSION['flash_success'] ?? '');
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Login</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/auth_login.css?v=1">
</head>

<body data-base="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">

  <div class="hlc-preload" id="hlcPreload" role="dialog" aria-modal="true">
    <div class="hlc-preload__panel" role="status">
      <div class="hlc-preload__spinner">
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
      <img src="/hlc_incidencias/assets/img/logo_nuevo.png"
        alt="Hospital La Caleta — Nivel de Complejidad II-2"
        class="logo">
    </div>

    <div class="hello-card">
      <div class="bubble"><i class="bi bi-heart-pulse"></i></div>
      <div>
        <h5>¡Hola!</h5>
        <p>Estamos aquí para ayudarte.</p>
      </div>
    </div>

    <div class="section-head">
      <h6>Iniciar sesión</h6>
    </div>

    <div class="formulario" autocomplete="off">
      <form method="post" action="login">

        <div class="position-relative mb-14">
          <i class="bi bi-credit-card-2-front input-icon fs-5"></i>
          <input type="text" name="dni" id="dni"
            inputmode="numeric"
            class="form-control"
            placeholder="Número de Documento"
            autocomplete="off">
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-shield-lock input-icon fs-5"></i>
          <input type="password" name="password" id="password"
            class="form-control"
            placeholder="Contraseña"
            autocomplete="new-password">
        </div>

        <div class="muted mb-2 text-end">
          <a href="reset" class="text-decoration-none">
            ¿Has olvidado tu contraseña?
          </a>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
          Iniciar sesión <i class="bi bi-lock-fill fs-5"></i>
        </button>

        <p class="text-center mb-1">
          ¿No tienes una cuenta?
          <a href="register" class="fw-bold text-decoration-none">
            ¡Regístrate ahora!
          </a>
        </p>

      </form>
    </div>
  </div>
  
  <div class="spacer"></div> 

  <nav class="tabbar" role="navigation" aria-label="Barra inferior">
    <div class="tabbar-inner">
      <a href="javascript:void(0)" class="item">
        <i class="bi bi-house"></i>
        <span>Inicio</span>
      </a>
      <a href="javascript:void(0)" class="item">
        <i class="bi bi-chat-dots"></i>
        <span>Chat</span>
      </a>
      <a href="javascript:void(0)" class="item">
        <i class="bi bi-card-checklist"></i>
        <span>Atenciones</span>
      </a>
      <a href="javascript:void(0)" class="item">
        <i class="bi bi-bell"></i>
        <span>Avisos</span>
      </a>
      <a href="javascript:void(0)" class="item">
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
  <script src="/hlc_incidencias/assets/js/auth_login.js?v=1" defer></script>

</body>

</html>