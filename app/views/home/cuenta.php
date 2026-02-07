<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireLogin();
$user = Auth::user() ?? [];
$userName = $user['Nombres'] ?? 'Usuario';
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hospital La Caleta - Cuenta</title>
    <link rel="icon" type="image/png" href="/hlc_incidencias/assets/favicon/favico.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/hlc_incidencias/assets/css/cuenta.css?v=1">
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
            <h6 class="mb-0">Mi cuenta</h6>
            <a href="dashboard" class="btn btn-sm btn-light rounded-pill">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
        <div class="formulario" autocomplete="off">
            <form>
                <div class="position-relative mb-14">
                    <a href="datospersonales" class="app-card d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm text-decoration-none">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <span class="d-flex align-items-center justify-content-center rounded-circle text-white"
                                style="background-color:#00b0ff; width:42px; height:42px; font-size:1.2rem;">
                                <i class="bi bi-person fs-5"></i>
                            </span>
                            <div class="app-card__text flex-grow-1">
                                <span class="title d-block text-dark small">
                                    Datos personales
                                </span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-dark fs-5"></i>
                    </a>
                </div>
                <div class="position-relative mb-14">
                    <a href="cambiarcontrasena" class="app-card d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm text-decoration-none">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <span class="d-flex align-items-center justify-content-center rounded-circle text-white"
                                style="background-color:#00b0ff; width:42px; height:42px; font-size:1.2rem;">
                                <i class="bi bi-shield fs-5"></i>
                            </span>
                            <div class="app-card__text flex-grow-1">
                                <span class="title d-block text-dark small">
                                    Cambiar contraseña
                                </span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-dark fs-5"></i>
                    </a>
                </div>
                <div class="position-relative mb-14">
                    <a href="javascript:void(0)" class="app-card d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm text-decoration-none">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <span class="d-flex align-items-center justify-content-center rounded-circle text-white"
                                style="background-color:#00b0ff; width:42px; height:42px; font-size:1.2rem;">
                                <i class="bi bi-question-circle fs-5"></i>
                            </span>
                            <div class="app-card__text flex-grow-1">
                                <span class="title d-block text-dark small">
                                    Centro de ayuda
                                </span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-dark fs-5"></i>
                    </a>
                </div>
                <button type="button" id="btnLogout" class="btn btn-primary w-100 mb-3">
                    Cerrar sesión <i class="bi bi-lock-fill fs-5"></i>
                </button>
                <p class="text-center mb-1">
                <section class="cuenta-info">
                    <div class="cuenta-info__row">
                        <div class="cuenta-info__label">Información de empresa</div>
                        <div class="cuenta-info__value">HOSPITAL LA CALETA - RUC 20186206852</div>
                    </div>
                    <div class="cuenta-info__row">
                        <div class="cuenta-info__label">Versión del App Web</div>
                        <div class="cuenta-info__value">02.0.1</div>
                    </div>
                </section>
                </p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/hlc_incidencias/assets/js/cuenta.js?v=1" defer></script>

</body>

</html>