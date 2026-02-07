<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Hospital La Caleta - Bienvenido</title>
    <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/hlc_incidencias/assets/css/welcome.css?v=1">
</head>

<body data-base="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">
    <img class="bg" src="/hlc_incidencias/assets/img/fondo.png" alt="Fondo ola HLC">
    <div id="checkConexion" class="check-conn" role="dialog" aria-live="polite" style="display:none;">
        <div class="check-card">
            <div class="check-title" id="chkTitle">Sin conexión a Internet</div>
            <div class="check-sub" id="chkSub">Verifica tu red y vuelve a intentar.</div>
        </div>
    </div>
    <div class="preload" id="preload" aria-hidden="false">
        <img src="/hlc_incidencias/assets/img/logo_nuevo.png" alt="Hospital La Caleta" class="logo">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/hlc_incidencias/assets/js/welcome.js?v=1" defer></script>
</body>

</html>