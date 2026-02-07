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
  const HLC = window.HLC || {};
  const BASE = (HLC.base || '').replace(/\/$/, '');
  const listEl = document.getElementById('incList');
  const totalEl = document.getElementById('incTotal');
  const statusEl = document.getElementById('incStatus');
  const lastIdEl = document.getElementById('incLastId');
  const pollMsEl = document.getElementById('incPollMs');

  if (!listEl) return;
  let lastId = Number(lastIdEl?.value || 0);
  const POLL_MS = Number(pollMsEl?.value || 3000);
  let busy = false;
  const esc = (s) => String(s ?? '')
    .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;').replaceAll("'", "&#039;");
  async function getJSON(url, opts) {
    const res = await fetch(url, opts || { headers: { 'Accept': 'application/json' } });
    const text = await res.text();
    try { return JSON.parse(text); } catch { return null; }
  }


  function render(items) {
    if (!items || items.length === 0) {
      listEl.innerHTML = `
        <div class="op-card">
          <div class="op-meta">Sin registros</div>
          <div class="op-desc mb-0">No hay incidencias en esta semana.</div>
        </div>
      `;
      return;
    }


    listEl.innerHTML = items.map(it => {
      const id = Number(it.Id || 0);
      const fullName = ((it.Nombres || '') + ' ' + (it.Apellidos || '')).trim() || (it.Dni || '');
      const tel = String(it.Telefono || '').trim();
      const estado = String(it.Estado || '').toUpperCase();
      const disabled = (estado === 'ATENDIDO') ? 'disabled' : '';
      return `
        <div class="inc-card" data-id="${id}">
          <div style="min-width:0">
            <div class="inc-title">${esc(it.TipoProblema)}</div>
            <div class="inc-meta">Oficina: ${esc(it.Oficina)}</div>
            <div class="inc-meta">Usuario : <span class="inc-user">${esc(fullName)}</span></div>
          </div>
          <div class="inc-actions">
            <button class="btn-pill btn-atendido js-atendido" type="button" data-id="${id}" ${disabled}>
              <i class="bi-check2"></i> Atender
            </button>
            <button class="btn-pill btn-llamar js-llamar" type="button"
              data-tel="${esc(tel)}"
              ${(!tel || disabled) ? 'disabled' : ''}>
             <i class="bi-telephone-outbound"></i>  Llamar
            </button>
          </div>
        </div>
      `;
    }).join('');
  }

  async function marcarAtendido(id, btn) {
    if (!id) return;
    const card = btn.closest('.inc-card');
    const llamarBtn = card?.querySelector('.js-llamar');
    btn.disabled = true;
    if (llamarBtn) llamarBtn.disabled = true;
    const body = new URLSearchParams();
    body.set('id', id);
    body.set('estado', 'ATENDIDO');
    try {
      const res = await fetch(`${BASE}/admin/estado?ajax=1`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'Accept': 'application/json'
        },
        body: body.toString()
      });
      const json = await res.json();
      if (!json.ok) {
        throw new Error('No se pudo actualizar');
      }
      card.style.opacity = '0';
      card.style.transform = 'scale(.98)';
      setTimeout(() => card.remove(), 250);
      const total = Number(totalEl.textContent || 0);
      totalEl.textContent = Math.max(0, total - 1);
      if (statusEl) {
        statusEl.textContent = 'Atendido ✓';
        setTimeout(() => statusEl.textContent = 'En línea', 1000);
      }

    } catch (e) {
      btn.disabled = false;
      if (llamarBtn) llamarBtn.disabled = false;

      if (statusEl) statusEl.textContent = 'Error';
    }
  }

  function llamar(btn) {
    if (btn.disabled) return;
    const tel = (btn.getAttribute('data-tel') || '').trim();
    if (!tel) return;
    const clean = tel.replace(/\s+/g, '').replace(/-/g, '');
    window.location.href = `tel:${clean}`;
  }

  document.addEventListener('click', (e) => {
    const atendidoBtn = e.target.closest('.js-atendido');
    if (atendidoBtn) {
      const id = Number(atendidoBtn.getAttribute('data-id') || 0);
      return marcarAtendido(id, atendidoBtn);
    }

    const llamarBtn = e.target.closest('.js-llamar');
    if (llamarBtn) return llamar(llamarBtn);
  });

  async function tick() {
    if (busy) return;
    busy = true;

    try {
      if (statusEl) statusEl.textContent = 'Revisando...';

      const meta = await getJSON(`${BASE}/admin/atender?ajax=1&meta=1`);
      if (!meta || !meta.ok) { if (statusEl) statusEl.textContent = 'Error'; return; }
      const newLastId = Number(meta.lastId || 0);
      if (totalEl) totalEl.textContent = String(meta.total ?? 0);
      if (newLastId > lastId) {
        const data = await getJSON(`${BASE}/admin/atender?ajax=1`);
        if (data && data.ok) {
          lastId = Number(data.lastId || newLastId);
          if (lastIdEl) lastIdEl.value = String(lastId);
          render(data.items || []);
          if (statusEl) statusEl.textContent = 'Nuevo registro ✓';
          setTimeout(() => { if (statusEl) statusEl.textContent = 'En línea'; }, 900);
        }
      } else {
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
