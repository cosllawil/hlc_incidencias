<?php
$u      = $user ?? [];
$items  = $items ?? [];
$lastId = $lastId ?? 0;

function h($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$items = array_values(array_filter($items, function ($it) {
  $estado = strtolower(trim((string)($it['Estado'] ?? '')));
  return $estado === 'pendiente';
}));

$total = count($items);
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Atender</title>

  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="/hlc_incidencias/assets/css/atender.css?v=1">
</head>

<body data-base="<?= h(rtrim(BASE_URL, '/')) ?>">

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
        <img src="../assets/img/logo_nuevo.png" alt="Hospital La Caleta" class="logo">
      </div>
    </div>

    <div class="hello-card">
      <div class="bubble"><i class="bi bi-heart-pulse"></i></div>
      <div>
        <h5>¡Hola, <?= h($u['Nombres'] ?? 'Usuario') ?>!</h5>
        <p>Estamos aquí para ayudarte.</p>
      </div>
    </div>

    <div class="section-head d-flex align-items-center justify-content-between">
      <div>
        <h6 class="mb-0">Atender Incidencias</h6>
      </div>

      <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <input type="hidden" id="incLastId" value="<?= (int)$lastId ?>">
    <input type="hidden" id="incPollMs" value="3000">

    <div class="formulario">
      <div class="hlc-card-scroll" id="incList">
        <?php if (empty($items)): ?>
          <div class="op-card">
            <div class="text-center text-secondary py-4">
              No hay incidencias pendientes.
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($items as $it):
            $id = (int)($it['Id'] ?? 0);
            $tel = trim((string)($it['Telefono'] ?? ''));
            $fullName = trim(($it['Nombres'] ?? '') . ' ' . ($it['Apellidos'] ?? ''));
          ?>
            <div class="inc-card" data-id="<?= $id ?>">
              <div style="min-width:0">
                <div class="inc-title"><?= h($it['TipoProblema'] ?? '') ?></div>
                <div class="inc-meta">Oficina: <?= h($it['Oficina'] ?? '') ?></div>
                <div class="inc-user">Reportado por : <span class="inc-user"><?= h($fullName ?: ($it['Dni'] ?? '')) ?></span></div>
              </div>

              <div class="inc-actions">
                <button class="btn-pill btn-atendido js-atendido" type="button" data-id="<?= $id ?>">
                  <i class="bi-check2"></i> Atender
                </button>

                 <button class="btn-pill btn-llamar js-llamar" type="button"
                  data-tel="<?= h($tel) ?>"
                  <?= (!$tel) ? 'disabled' : '' ?>>
                  <i class="bi-telephone-outbound"></i> Llamar
                </button>

                <a class="btn-pill btn-vermas" href="verdetalles?id=<?= $id ?>" style="text-decoration:none">
                 <i class="bi-search"></i> Ver más
                </a>

              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="op-meta mt-1">
        Total : <strong id="incTotal"><?= (int)$total ?></strong>
      </div>
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

  <script>
    window.HLC = window.HLC || {};
    window.HLC.base = <?= json_encode(rtrim(BASE_URL, '/'), JSON_UNESCAPED_SLASHES) ?>;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/hlc_incidencias/assets/js/atender.js?v=1" defer></script>
</body>

</html>