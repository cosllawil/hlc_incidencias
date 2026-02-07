document.addEventListener('DOMContentLoaded', () => {

  (() => {
    const $id = (id) => document.getElementById(id);
    const HLC = window.HLC || {};
    function hlcToast(type, title, message, ms) {
      const wrap = $id('hlcToastWrap');
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
    if (HLC.toastError) hlcToast('error', 'Validación', HLC.toastError, 4200);
    if (HLC.toastOk) hlcToast('success', 'Registro', HLC.toastOk, 2600);
    const form = $id('frmIncidencia');
    const Descripcion = $id('Descripcion');
    if (!form) { console.warn('No se encontró #frmIncidencia'); return; }
    if (!Descripcion) { console.warn('No se encontró #Descripcion'); return; }
    function validarDescripcion(showToast = false) {
      const val = (Descripcion.value || '').trim();
      const ok = val.length >= 5;
      if (!ok && showToast) hlcToast('error', 'Validación', 'Ingrese una descripción (mín. 5 caracteres).', 3400);
      return ok;
    }

    Descripcion.addEventListener('blur', () => validarDescripcion(true));

    form.addEventListener('submit', (e) => {
      if (!validarDescripcion(false)) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Ingrese una descripción (mín. 5 caracteres).', 3400);
        Descripcion.focus();
        return;
      }
    });

  })();

});
