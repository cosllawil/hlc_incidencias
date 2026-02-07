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
  <title>Hospital La Caleta - Crear usuario</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/auth_register.css?v=1">
</head>

<body data-base="<?= htmlspecialchars(rtrim(BASE_URL, '/'), ENT_QUOTES, 'UTF-8') ?>">
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
      <h6 class="mb-0">Craer cuenta</h6>
      <a href="login" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <div class="formulario">
      <form method="post" action="register">
        <div class="position-relative mb-14">
          <i class="bi bi-person input-icon"></i>
          <select name="TipoDocumento" id="TipoDocumento" class="form-select" onchange="TipoDocumento()">
            <option value="" selected disabled>Seleccione tipo documento</option>
            <option value="DNI">DNI</option>
            <option value="CE">CARNET DE EXTRANJERIA</option>
            <option value="PAS">PASAPORTE</option>
          </select>
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-credit-card-2-front input-icon fs-5"></i>
          <input
            type="text"
            name="Dni"
            id="Dni"
            inputmode="numeric"
            class="form-control"
            placeholder="DNI / Documento"
            autocomplete="off">
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-person input-icon fs-5"></i>
          <input
            type="text"
            name="Nombres"
            id="Nombres"
            class="form-control"
            placeholder="Nombres"
            autocomplete="off"
            inputmode="text">
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-person-vcard input-icon fs-5"></i>
          <input
            type="text"
            name="Apellidos"
            id="Apellidos"
            class="form-control"
            placeholder="Apellidos"
            autocomplete="off"
            inputmode="text">
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-telephone input-icon fs-5"></i>
          <input
            type="tel"
            name="Telefono"
            id="Telefono"
            class="form-control"
            placeholder="Teléfono (9 dígitos)"
            autocomplete="new-tel"
            inputmode="numeric"
            pattern="[0-9]{9}"
            maxlength="9">
        </div>

        <div class="position-relative mb-3">
          <i class="bi bi-shield-lock input-icon fs-5"></i>
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
          <i class="bi bi-shield-lock input-icon fs-5"></i>
          <input
            type="password"
            name="RepetirPassword"
            id="RepetirPassword"
            class="form-control"
            placeholder="Repetir contraseña"
            autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
          Crear cuenta <i class="bi-check-circle-fill fs-5"></i>
        </button>

        <p class="text-center mb-1">
          ¿Ya tienes una cuenta? &nbsp;
          <a href="login" class="fw-bold text-decoration-none" style="color: #0d6efd;">¡Iniciar sesión!</a>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <div class="hlc-toast-wrap" id="hlcToastWrap" aria-live="polite" aria-atomic="true"></div>

  <script>
    window.HLC = window.HLC || {};
    window.HLC.base = <?= json_encode(rtrim(BASE_URL, '/'), JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.flashError = <?= json_encode($flashError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.flashSuccess = <?= json_encode($flashSuccess ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.reniecProxy = window.HLC.base + "/public/ApiReniec/reniec_proxy.php";
  </script>

  <script src="/hlc_incidencias/assets/js/auth_register.js?v=1" defer></script>

</body>

</html>