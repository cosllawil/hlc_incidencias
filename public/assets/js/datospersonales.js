document.oncontextmenu = () => false;

(function () {
  const pre = document.getElementById('hlcPreload');
  if (!pre) return;

  const prevOverflow = document.documentElement.style.overflow;
  document.documentElement.style.overflow = 'hidden';

  let closed = false;

  function hide() {
    if (closed) return;
    closed = true;

    pre.classList.add('hlc-preload--hide');
    document.documentElement.style.overflow = prevOverflow || '';
  }

  window.addEventListener('load', () => setTimeout(hide, 250));
  setTimeout(hide, 3000);
  setTimeout(hide, 8000);
})();

const form = document.querySelector('#datospersonales');
const telEl = form?.querySelector('input[name="Telefono"], input[name="telefono"]');
const BASE = window.HLC?.base || '';

form?.addEventListener('submit', async (ev) => {
  ev.preventDefault();
  const tel = (telEl?.value || '').trim();
  const telefono = tel.replace(/\D/g, '');
  if (!telefono) {
    hlcToast('error', 'Datos personales', 'El teléfono es obligatorio.', 3200);
    telEl?.focus();
    return;
  }
  if (telefono.length < 9) {
    hlcToast('error', 'Datos personales', 'Ingrese un teléfono válido 9 dígitos.', 3200);
    telEl?.focus();
    return;
  }
  try {
    showPreload('Actualizando teléfono...');
    const fd = new FormData();
    fd.append('Telefono', telefono);
    const res = await fetch(`${BASE}/home/datospersonales?ajax=1`, {
      method: 'POST',
      body: fd,
      headers: { 'Accept': 'application/json' }
    });
    const ct = res.headers.get('content-type') || '';
    const json = ct.includes('application/json') ? await res.json() : null;
    hidePreload();
    if (!json?.ok) {
      hlcToast('error', 'Datos personales', json?.message || 'No se pudo actualizar.', 3600);
      return;
    }
    hlcToast('success', 'Datos personales', json?.message || 'Teléfono actualizado.', 1200);
    setTimeout(() => {
      location.reload();
    }, 1200);

  } catch (e) {
    hidePreload();
    hlcToast('error', 'Datos personales', 'Error actualizando teléfono.', 3600);
  }
});

function hlcToast(type, title, message, ms) {
  const wrap = document.getElementById('hlcToastWrap');
  if (!wrap) {
    alert((title ? title + "\n" : "") + (message || ''));
    return;
  }

  const t = document.createElement('div');
  t.className = `hlc-toast hlc-toast--${type || 'info'}`;
  const icon =
    (type === 'success') ? 'bi-check-circle-fill' :
      (type === 'error') ? 'bi-x-circle-fill' :
        'bi-info-circle-fill';

  t.innerHTML = `
    <div class="hlc-toast__icon"><i class="bi ${icon}"></i></div>
    <div style="min-width:0">
      <p class="hlc-toast__title">${title || 'Aviso'}</p>
      <p class="hlc-toast__msg">${message || ''}</p>
    </div>
    <button class="hlc-toast__close" type="button" aria-label="Cerrar">&times;</button>
  `;
  const close = () => {
    t.style.opacity = '0';
    t.style.transform = 'translateY(-6px)';
    setTimeout(() => t.remove(), 160);
  };
  t.querySelector('.hlc-toast__close')?.addEventListener('click', close);
  wrap.appendChild(t);
  setTimeout(close, Math.max(1200, ms || 2800));
}

function showPreload(text) {
  const pre = document.getElementById('hlcPreload');
  const preText = pre?.querySelector('.hlc-preload__text');
  if (!pre) return;
  if (preText && text) preText.textContent = String(text);
  pre.classList.remove('hlc-preload--hide');
}

function hidePreload() {
  const pre = document.getElementById('hlcPreload');
  if (!pre) return;
  pre.classList.add('hlc-preload--hide');
}

if (window.HLC?.toastError) hlcToast('error', 'Validación', window.HLC.toastError, 4200);
if (window.HLC?.toastOk) hlcToast('success', 'OK', window.HLC.toastOk, 2800);
