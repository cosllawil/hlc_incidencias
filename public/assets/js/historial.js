(function () {
  const pre = document.getElementById('hlcPreload');
  if (!pre) return;
  const prevOverflow = document.documentElement.style.overflow;
  document.documentElement.style.overflow = 'hidden';
  let closed = false;
  function forceRemove() {
    if (!pre) return;
    if (pre.parentNode) pre.parentNode.removeChild(pre);
  }

  function hide() {
    if (closed) return;
    closed = true;

    pre.classList.add('hlc-preload--hide');
    document.documentElement.style.overflow = prevOverflow || '';
    setTimeout(forceRemove, 650);
  }

  window.addEventListener('load', () => setTimeout(hide, 250));
  setTimeout(hide, 3000);
  setTimeout(hide, 8000);
})();


document.oncontextmenu = () => false;

(() => {
  const ENDPOINT = "/hlc_incidencias/home/historial-updates";
  const POLL_MS = 5000;

  function nowMysql() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    return (
      d.getFullYear() + "-" +
      pad(d.getMonth() + 1) + "-" +
      pad(d.getDate()) + " " +
      pad(d.getHours()) + ":" +
      pad(d.getMinutes()) + ":" +
      pad(d.getSeconds())
    );
  }


  let since = (() => {
    const d = new Date(Date.now() - 60000);
    const pad = (n) => String(n).padStart(2, '0');
    return (
      d.getFullYear() + "-" +
      pad(d.getMonth() + 1) + "-" +
      pad(d.getDate()) + " " +
      pad(d.getHours()) + ":" +
      pad(d.getMinutes()) + ":" +
      pad(d.getSeconds())
    );
  })();

  let busy = false;
  function badgeHTML(estado) {
    estado = (estado || '').toUpperCase().trim();
    if (estado === 'PENDIENTE') return '<span class="badge-pendiente"><i class="bi-bell-slash"></i> Pendiente</span>';
    if (estado === 'ATENDIDO') return '<span class="badge-atendido"><i class="bi-check-lg"></i> Atendido</span>';
    return '<span class="badge-proceso">En proceso</span>';
  }

  async function safeJSON(res) {
    const ct = (res.headers.get('content-type') || '').toLowerCase();
    if (!ct.includes('application/json')) return null;
    try { return await res.json(); } catch { return null; }
  }

  async function checkUpdates() {
    if (busy) return;
    busy = true;

    try {
      const url = ENDPOINT + '?since=' + encodeURIComponent(since);
      const res = await fetch(url, { credentials: 'same-origin', cache: 'no-store' });
      if (!res.ok) return;
      const data = await safeJSON(res);
      if (!data) return;
      since = (data.serverTime && String(data.serverTime).length >= 19)
        ? data.serverTime
        : nowMysql();
      const changes = Array.isArray(data.changes) ? data.changes : [];
      changes.forEach(c => {
        const id = Number(c.id || 0);
        if (!id) return;
        const row = document.querySelector('.hlc-item[data-id="' + id + '"]');
        if (!row) return;
        const badge = row.querySelector('[data-badge]');
        if (!badge) return;
        badge.innerHTML = badgeHTML(c.estado);
        row.style.transition = 'background .2s ease';
        row.style.background = 'rgba(0,176,255,.08)';
        setTimeout(() => { row.style.background = ''; }, 600);
      });

    } catch (e) {

    } finally {
      busy = false;
    }
  }
  setTimeout(checkUpdates, 1500);
  setInterval(checkUpdates, POLL_MS);
})();
