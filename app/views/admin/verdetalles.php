<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireLogin();
$user = Auth::user() ?? [];
$userName = $user['Nombres'] ?? 'Usuario';
$inc = $inc ?? null;
?>

<?php
$foto = trim((string)($inc['foto'] ?? $inc['Foto'] ?? ''));
$fotoUrl = '';
if ($foto !== '') {
  $base = rtrim((string)BASE_URL, '/');

  if (str_starts_with($foto, '/')) {
    $fotoUrl = $base . $foto;
  } else {
    $fotoUrl = $base . '/uploads/incidentes/' . $foto;
  }
}
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital La Caleta - Ver detalles</title>

  <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/hlc_incidencias/assets/css/verdetalles.css?v=1">
</head>

<body>
  <div class="screen">
    <div class="hero">
      <div class="text-white">
        <img src="/hlc_incidencias/assets/img/logo_nuevo.png" alt="Hospital La Caleta" class="logo">
      </div>
    </div>

    <div class="hello-card">
      <div class="bubble"><i class="bi bi-heart-pulse"></i></div>
      <div>
        <h5>¡Hola, <?= htmlspecialchars($userName) ?>!</h5>
        <p>Detalle de la incidencia.</p>
      </div>
    </div>

    <div class="section-head d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Detalle de la incidencia</h6>
      <a href="atender" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <div class="formulario">
      <form>
        <?php if (!$inc): ?>
          <div class="text-center text-secondary py-4">Incidencia no encontrada.</div>
        <?php else: ?>
          <div class="hlc-detail-card">
            <div class="row g-3">

              <div class="col-12">
                <p class="inc-user">Oficina</p>
                <p class="op-meta"><?= htmlspecialchars($inc['oficina'] ?? '—') ?></p>
              </div>

              <div class="col-12">
                <p class="inc-user">Tipo de problema</p>
                <p class="op-meta"><?= htmlspecialchars($inc['tipoproblema'] ?? '—') ?></p>
              </div>

              <div class="col-12">
                <p class="inc-user">Descripción del problema</p>
                <p class="op-meta"><?= htmlspecialchars($inc['descripcion'] ?? '—') ?></p>
              </div>

              <div class="col-12 d-flex gap-2">
                <button
                  type="button"
                  class="btn btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#mdlFoto">
                  Ver foto <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endif; ?>
    </div>
    </form>
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
      <a href="#" class="item">
        <i class="bi bi-person"></i>
        <span>Cuenta</span>
      </a>
    </div>
  </nav>

  <div class="modal fade" id="mdlFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Foto de la incidencia</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body text-center">
          <?php if (!empty($fotoUrl)): ?>
            <img
              src="<?= htmlspecialchars($fotoUrl, ENT_QUOTES, 'UTF-8') ?>"
              alt="Foto de incidencia"
              class="img-fluid rounded"
              style="max-height:70vh">
          <?php else: ?>
            <p class="text-muted mb-0">No se adjuntó ninguna foto.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>