(() => {
    const base = document.body?.dataset?.base || '';
    const check = document.getElementById('checkConexion');
    const preload = document.getElementById('preload');
    window.__conn_ok = false;
    let preloadTimerStarted = false;
    function startPreloadTimerOnce() {
        if (preloadTimerStarted || !window.__conn_ok) return;
        preloadTimerStarted = true;
        setTimeout(() => {
            if (!preload) return;
            preload.classList.add('preload--hide');
            setTimeout(() => {
                window.location.href = "auth/login";
            }, 600);

        }, 2500);
    }

    function showOffline() {
        if (!check) return;
        check.style.display = "flex";
        if (!document.getElementById('btnRetry')) {
            const btn = document.createElement('button');
            btn.id = 'btnRetry';
            btn.className = 'btn-retry';
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Reintentar';
            btn.addEventListener('click', async () => {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Reintentando...';
                const ok = await verificarConexion();
                if (!ok) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Reintentar';
                }
            });

            check.querySelector('.check-card')?.appendChild(btn);
        }
    }

    function hideCheck() {
        if (!check) return;
        check.style.display = "none";
    }

    const TIMEOUT = 4000;
    function pingFetch(url) {
        return new Promise((resolve, reject) => {
            const controller = new AbortController();
            const to = setTimeout(() => {
                controller.abort();
                reject(new Error('timeout'));
            }, TIMEOUT);
            fetch(url, {
                cache: 'no-store',
                mode: 'no-cors',
                signal: controller.signal
            })
                .then(() => {
                    clearTimeout(to);
                    resolve(true);
                })
                .catch(() => {
                    clearTimeout(to);
                    reject(new Error('fetch_failed'));
                });
        });
    }

    async function isOnline() {
        if (!navigator.onLine) return false;
        try {
            await Promise.any([
                pingFetch('https://www.gstatic.com/generate_204'),
                pingFetch(window.location.origin + '/favicon.ico'),
            ]);
            return true;
        } catch {
            return false;
        }
    }

    async function verificarConexion() {
        const ok = await isOnline();
        if (ok) {
            window.__conn_ok = true;
            hideCheck();
            startPreloadTimerOnce();
        } else {
            window.__conn_ok = false;
            showOffline();
        }
        return ok;
    }
    window.addEventListener('load', verificarConexion);
    window.addEventListener('online', verificarConexion);
    window.addEventListener('offline', showOffline);
    document.oncontextmenu = () => false;
})();
