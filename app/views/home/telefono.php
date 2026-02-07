<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireLogin();
$toastError = $_SESSION['toast_error'] ?? '';
$toastOk    = $_SESSION['toast_ok'] ?? '';
unset($_SESSION['toast_error'], $_SESSION['toast_ok']);
$user = Auth::user() ?? [];
$userName = $user['Nombres'] ?? 'Usuario';
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Teléfono</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/telefono.css?v=1">
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
      <h6 class="mb-0">Mi Teléfono</h6>
      <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
    <div class="formulario">
      <form method="post" action="telefono" enctype="multipart/form-data" id="frmIncidencia">
        <div class="position-relative mb-14">
          <i class="bi bi-house-door input-icon"></i>
          <select name="Oficina" id="Oficina" class="form-select select2" style="width:100%;">
            <option value="" selected disabled>Seleccione la oficina</option>
            <?php foreach ($servicios as $srv): ?>
              <option value="<?= htmlspecialchars($srv['nombre']) ?>">
                <?= htmlspecialchars($srv['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="position-relative mb-14">
          <i class="bi bi-lightbulb input-icon"></i>
          <select name="TipoProblema" id="TipoProblema" class="form-select select2" style="width:100%;">
            <option value="" selected disabled>Seleccione el problema</option>
            <?php foreach ($telefono as $tel): ?>
              <option value="<?= htmlspecialchars($tel['nombre']) ?>">
                <?= htmlspecialchars($tel['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="position-relative mb-14">
          <i class="bi bi-layers textarea-icon"></i>
          <textarea
            name="Descripcion"
            id="Descripcion"
            class="form-control"
            placeholder="Breve descripción del problema"
            rows="3"
            style="resize:none"></textarea>
        </div>
        <div class="position-relative mb-3">
          <i class="bi bi-image input-icon fs-5"></i>
          <input
            type="file"
            name="Foto"
            id="Foto"
            class="form-control"
            accept=".jpg,.jpeg,.png,.webp">
        </div>
        <div class="position-relative mb-14">
          <i class="bi bi-telephone input-icon fs-5"></i>
          <input
            type="text"
            name="Telefono"
            id="Telefono"
            class="form-control"
            placeholder="Teléfono de Contacto"
            autocomplete="off"
            inputmode="numeric"
            maxlength="9">
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">
          Registrar Incidencia <i class="bi-check-circle-fill fs-5"></i>
        </button>
      </form>
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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/hlc_incidencias/assets/js/home_home.js?v=1" defer></script>
  <script src="/hlc_incidencias/assets/js/telefono.js?v=1" defer></script>

  <div class="hlc-toast-wrap" id="hlcToastWrap" aria-live="polite" aria-atomic="true"></div>

  <script>
    window.HLC = window.HLC || {};
    window.HLC.base = <?= json_encode(rtrim(BASE_URL, '/'), JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.toastError = <?= json_encode($toastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.HLC.toastOk = <?= json_encode($toastOk, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>

</body>

</html>