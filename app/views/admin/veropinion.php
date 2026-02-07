<?php
$flashError = $flashError ?? ($_SESSION['flash_error'] ?? '');
$flashSuccess = $flashSuccess ?? ($_SESSION['flash_success'] ?? '');
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

$u      = $user ?? [];
$items  = $items ?? [];
$total  = $total ?? count($items);
$lastId = $lastId ?? 0;
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
    <link rel="stylesheet" href="/hlc_incidencias/assets/css/veropinion.css?v=1">
</head>

<body data-base="<?= htmlspecialchars(rtrim(BASE_URL, '/'), ENT_QUOTES, 'UTF-8') ?>">

    <!-- PRELOAD -->
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
            <div>
                <h6 class="mb-0">Opiniones a Mejorar</h6>
            </div>
            <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>


        <input type="hidden" id="opLastId" value="<?= (int)$lastId ?>">
        <input type="hidden" id="opPollMs" value="3000">


        <div class="formulario">

            <div class="hlc-card-scroll">
                <?php if (empty($items)): ?>
                    <div class="op-card">
                        <div class="op-meta">Sin registros</div>
                        <div class="op-desc mb-0">Aún no hay opiniones registradas.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $it): ?>
                        <div class="op-card mb-2">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="inc-user">
                                        <?= htmlspecialchars(trim(($it['Nombres'] ?? '') . ' ' . ($it['Apellidos'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="op-meta">DNI: <?= htmlspecialchars(($it['Dni'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="text-end op-meta">
                                    <?= htmlspecialchars(($it['Fecha'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($it['Hora'])): ?>
                                        <br><?= htmlspecialchars(($it['Hora'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="op-desc"><?= htmlspecialchars(($it['Descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="op-meta mt-1">
                Total: <strong id="opTotal"><?= (int)$total ?></strong>
            </div>
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

    <script>
        window.HLC = window.HLC || {};
        window.HLC.base = <?= json_encode(rtrim(BASE_URL, '/'), JSON_UNESCAPED_SLASHES) ?>;
        window.HLC.flashError = <?= json_encode($flashError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        window.HLC.flashSuccess = <?= json_encode($flashSuccess ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/hlc_incidencias/assets/js/veropinion.js?v=1" defer></script>
</body>

</html>