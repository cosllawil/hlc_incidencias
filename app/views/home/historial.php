<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireLogin();
$user = Auth::user() ?? [];
$userName = $user['Nombres'] ?? 'Usuario';
$incidencias = $incidencias ?? [];
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Historial</title>
  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/historial.css?v=2">
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
        <img src="/hlc_incidencias/assets/img/logo_nuevo.png" class="logo" alt="Hospital La Caleta">
      </div>
    </div>
    <div class="hello-card">
      <div class="bubble"><i class="bi bi-heart-pulse"></i></div>
      <div>
        <h5>¡Hola, <?= htmlspecialchars($userName) ?>!</h5>
        <p>Historial de tus incidencias.</p>
      </div>
    </div>
    <div class="section-head d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Historial de Incidencias</h6>
      <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
    <div class="formulario">
      <div class="hlc-card-scroll">
        <?php if (empty($incidencias)): ?>
          <div class="text-center text-secondary py-4">
            No hay incidencias registradas.
          </div>
        <?php else: ?>
          <div class="d-grid gap-2">
            <?php foreach ($incidencias as $r): ?>
              <?php
              $id = (int)($r['id'] ?? 0);
              $estadoRaw = strtoupper(trim((string)($r['estado'] ?? 'PENDIENTE')));
              switch ($estadoRaw) {
                case 'ATENDIDO':
                  $badgeHtml = '<span class="badge-atendido"><i class="bi bi-check-lg"></i> Atendido</span>';
                  break;
                case 'PENDIENTE':
                default:
                  $badgeHtml = '<span class="badge-pendiente"><i class="bi-exclamation-diamond"></i> Pendiente</span>';
                  break;
              }
              ?>
              <div class="hlc-item d-flex justify-content-between align-items-start gap-3" data-id="<?= $id ?>">
                <div style="min-width:0">
                  <p class="hlc-title mb-1">
                    <?= htmlspecialchars($r['tipoproblema'] ?? 'Sin tipo') ?>
                  </p>
                  <div class="hlc-muted">
                    Oficina: <?= htmlspecialchars($r['oficina'] ?? '—') ?>
                  </div>
                </div>
                <div class="text-end">
                  <?= $badgeHtml ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="mt-2">
        Total: <strong><?= count($incidencias) ?></strong>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/hlc_incidencias/assets/js/historial.js?v=1" defer></script>

</body>

</html>