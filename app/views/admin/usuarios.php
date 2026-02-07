<?php

$flashError = $flashError ?? ($_SESSION['flash_error'] ?? '');
$flashSuccess = $flashSuccess ?? ($_SESSION['flash_success'] ?? '');
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

$u = $user ?? [];
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Editar usuarios</title>

  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="/hlc_incidencias/assets/css/editarusuarios.css?v=1">
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
        <h5>¡Hola, <?= htmlspecialchars(($u['Nombres'] ?? 'Usuario'), ENT_QUOTES, 'UTF-8') ?>!</h5>
         <p>Estamos aquí para ayudarte.</p>
      </div>
    </div>

    <div class="section-head d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Editar usuarios</h6>
      <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <div class="formulario">
      <form method="post" action="usuarios" autocomplete="off">

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
          <i class="bi bi-person input-icon"></i>
          <select name="Rol" id="Rol" class="form-select" disabled>
            <option value="" selected disabled>Seleccione tipo de rol</option>
            <option value="ADMIN">ADMIN</option>
            <option value="PERSONAL">PERSONAL</option>
          </select>
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-person input-icon fs-5"></i>
          <input
            type="text"
            name="Nombres"
            id="Nombres"
            class="form-control"
            placeholder="Nombres"
            readonly
            style="background:#FFFFFF">
        </div>

        <div class="position-relative mb-14">
          <i class="bi bi-person-vcard input-icon fs-5"></i>
          <input
            type="text"
            name="Apellidos"
            id="Apellidos"
            class="form-control"
            placeholder="Apellidos"
            readonly
            style="background:#FFFFFF">
        </div>

        <button id="btnGuardarRol" type="submit" class="btn btn-primary w-100 mb-3" disabled>
          Modificar usuario <i class="bi bi-check-circle-fill fs-5"></i>
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
      <a href="cuenta" class="item">
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
  </script>

  <script src="/hlc_incidencias/assets/js/editarusuarios.js?v=1" defer></script>
</body>
</html>
