(() => {
  const $ = (id) => document.getElementById(id);
  const HLC = window.HLC || {};
  const BASE = (HLC.base || '').replace(/\/$/, '');

  const pre = $('hlcPreload');
  const preText = pre ? pre.querySelector('.hlc-preload__text') : null;

  function showPreload(texto) {
    if (!pre) return;
    if (preText) preText.textContent = texto || 'Cargando...';
    pre.classList.remove('hlc-preload--hide');
  }
  function hidePreload() {
    if (!pre) return;
    pre.classList.add('hlc-preload--hide');
    setTimeout(() => { if (pre && pre.parentNode) pre.remove(); }, 700);
  }

  window.addEventListener('load', () => setTimeout(hidePreload, 200));
  setTimeout(hidePreload, 4000);


  function hlcToast(type, title, message, ms) {
    const wrap = $('hlcToastWrap');
    if (!wrap) return;
    const t = document.createElement('div');
    t.className = `hlc-toast hlc-toast--${type || 'info'}`;
    const icon = (type === 'success') ? 'bi-check-circle-fill'
      : (type === 'error') ? 'bi-x-circle-fill'
        : 'bi-info-circle-fill';

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


  if (HLC.flashError) hlcToast('error', 'Usuarios', HLC.flashError, 4200);
  if (HLC.flashSuccess) hlcToast('success', 'Usuarios', HLC.flashSuccess, 2400);
  const form = document.querySelector('form');
  const dniEl = $('Dni');
  const rolEl = $('Rol');
  const nomEl = $('Nombres');
  const apeEl = $('Apellidos');
  const btnSave = $('btnGuardarRol');
  const lockFields = (locked) => {
    if (nomEl) nomEl.readOnly = true;
    if (apeEl) apeEl.readOnly = true;
    if (rolEl) rolEl.disabled = !!locked;
    if (btnSave) btnSave.disabled = !!locked;
  };

  const clearFields = () => {
    if (nomEl) nomEl.value = '';
    if (apeEl) apeEl.value = '';
    if (rolEl) rolEl.value = '';
    lockFields(true);
  };

  lockFields(true);
  let tmr = null;
  const debounce = (fn, ms = 350) => {
    clearTimeout(tmr);
    tmr = setTimeout(fn, ms);
  };

  async function buscarPorDni(dni) {
    try {
      showPreload('Buscando usuario...');
      const url = `${BASE}/admin/usuarios?ajax=1&dni=${encodeURIComponent(dni)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const json = await res.json();
      hidePreload();
      if (!json?.ok) {
        clearFields();
        hlcToast('error', 'Usuarios', json?.message || 'No encontrado.', 3200);
        return;
      }
      const u = json.data || {};
      if (nomEl) nomEl.value = u.Nombres || '';
      if (apeEl) apeEl.value = u.Apellidos || '';
      if (rolEl) rolEl.value = u.Rol || '';
      lockFields(false);
      hlcToast('success', 'Usuarios', 'Usuario encontrado.', 1600);
    } catch (e) {
      hidePreload();
      clearFields();
      hlcToast('error', 'Usuarios', 'Error consultando al servidor.', 3200);
    }
  }


  dniEl?.addEventListener('input', () => {
    const dni = (dniEl.value || '').replace(/\D+/g, '');
    dniEl.value = dni;

    if (dni.length === 8) {
      debounce(() => buscarPorDni(dni), 350);
    } else {
      clearFields();
    }
  });


  form?.addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const dni = (dniEl?.value || '').trim();
    const rol = (rolEl?.value || '').trim();
    if (dni.length !== 8) {
      hlcToast('error', 'Usuarios', 'Ingrese un DNI válido (8 dígitos).', 3200);
      return;
    }
    if (!rol) {
      hlcToast('error', 'Usuarios', 'Seleccione un rol.', 3200);
      return;
    }
    try {
      showPreload('Actualizando rol...');
      const fd = new FormData();
      fd.append('Dni', dni);
      fd.append('Rol', rol);
      const res = await fetch(`${BASE}/admin/usuarios?ajax=1`, {
        method: 'POST',
        body: fd,
        headers: { 'Accept': 'application/json' }
      });
      const json = await res.json();
      hidePreload();
      if (!json?.ok) {
        hlcToast('error', 'Usuarios', json?.message || 'No se pudo actualizar.', 3600);
        return;
      }
      hlcToast('success', 'Usuarios', json?.message || 'Rol actualizado.', 2200);
    } catch (e) {
      hidePreload();
      hlcToast('error', 'Usuarios', 'Error actualizando rol.', 3600);
    }
  });
  
})();
