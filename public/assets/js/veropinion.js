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


(() => {
  const HLC = window.HLC || {};
  const BASE = (HLC.base || '').replace(/\/$/, '');
  const listEl = document.getElementById('opList');
  const totalEl = document.getElementById('opTotal');
  const statusEl = document.getElementById('opStatus');
  const lastIdEl = document.getElementById('opLastId');
  const pollMsEl = document.getElementById('opPollMs');
  if (!listEl) return;
  let lastId = Number(lastIdEl?.value || 0);
  const POLL_MS = Number(pollMsEl?.value || 3000);
  const esc = (s) => String(s ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", "&#039;");

  function toDMY(fecha) {
    const s = String(fecha ?? '').trim();
    const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!m) return s;
    return `${m[3]}/${m[2]}/${m[1]}`;
  }
  function render(items) {
    if (!items || items.length === 0) {
      listEl.innerHTML = `
        <div class="op-card">
          <div class="op-meta">Sin registros</div>
          <div class="op-desc mb-0">Aún no hay opiniones registradas.</div>
        </div>
      `;
      return;
    }
    listEl.innerHTML = items.map(it => {
      const fullName = ((it.Nombres || '') + ' ' + (it.Apellidos || '')).trim();
      const fecha = toDMY(it.Fecha);
      const hora = it.Hora ? `<br>${esc(it.Hora)}` : '';
      return `
        <div class="op-card mb-2">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="fw-semibold">${esc(fullName)}</div>
              <div class="op-meta">DNI: ${esc(it.Dni)}</div>
            </div>
            <div class="text-end op-meta">${esc(fecha)}${hora}</div>
          </div>
          <div class="op-desc">${esc(it.Descripcion)}</div>
        </div>
      `;
    }).join('');
  }

  let busy = false;
  async function getJSON(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const text = await res.text();
    try { return JSON.parse(text); } catch { return null; }
  }

  async function tick() {
    if (busy) return;
    busy = true;
    try {
      if (statusEl) statusEl.textContent = 'Revisando...';
      const meta = await getJSON(`${BASE}/admin/veropinion?ajax=1&meta=1`);
      if (!meta || !meta.ok) {
        if (statusEl) statusEl.textContent = 'Error';
        return;
      }
      const newLastId = Number(meta.lastId || 0);
      if (newLastId > lastId) {
        const data = await getJSON(`${BASE}/admin/veropinion?ajax=1`);
        if (data && data.ok) {
          lastId = Number(data.lastId || newLastId);
          if (lastIdEl) lastIdEl.value = String(lastId);
          if (totalEl) totalEl.textContent = String(data.total ?? meta.total ?? 0);
          render(data.items || []);
          if (statusEl) statusEl.textContent = 'Nuevo registro ✓';
          setTimeout(() => { if (statusEl) statusEl.textContent = 'En línea'; }, 900);
        } else {
          if (statusEl) statusEl.textContent = 'Error';
        }
      } else {
        if (totalEl) totalEl.textContent = String(meta.total ?? totalEl.textContent ?? 0);
        if (statusEl) statusEl.textContent = 'En línea';
      }

    } catch (e) {
      if (statusEl) statusEl.textContent = 'Error';
    } finally {
      busy = false;
    }
  }
  tick();
  setInterval(tick, POLL_MS);
})();


document.oncontextmenu = () => false;
