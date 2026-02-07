(() => {
  const byId = (id) => document.getElementById(id);

  (() => {
    const tabs = document.querySelectorAll('.tabbar .item');
    if (!tabs.length) return;
    tabs.forEach(el => {
      el.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        el.classList.add('active');
      });
    });
  })();


  document.oncontextmenu = () => false;


  function hlcToast(type, title, message, ms) {
    const wrap = byId('hlcToastWrap');
    if (!wrap) return;
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


  (() => {
    const pre = byId('hlcPreload');
    if (!pre) return;

    const prevOverflow = document.documentElement.style.overflow;
    document.documentElement.style.overflow = 'hidden';

    const hide = () => {
      if (pre.classList.contains('hlc-preload--hide')) return;
      pre.classList.add('hlc-preload--hide');
      document.documentElement.style.overflow = prevOverflow || '';
      setTimeout(() => {
        if (pre && pre.parentNode) pre.remove();
      }, 700);
    };
    window.addEventListener('load', () => setTimeout(hide, 250));
    setTimeout(hide, 4000);
  })();

  if (window.HLC && window.HLC.flashError) {
    hlcToast('error', 'Validación', window.HLC.flashError, 4200);
  }
  if (window.HLC && window.HLC.flashSuccess) {
    hlcToast('success', 'OK', window.HLC.flashSuccess, 3000);
  }


  document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[method="post"]');
    if (!form) return;
    const pass = form.querySelector('input[name="Password"]');
    const rep = form.querySelector('input[name="RepetirPassword"]');
    form.addEventListener('submit', (e) => {
      const p1 = (pass?.value || '').trim();
      const p2 = (rep?.value || '').trim();

      if (!p1 || p1.length < 4) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Ingrese una contraseña (mínimo 4 caracteres).', 3800);
        pass?.focus();
        return;
      }
      if (p1 !== p2) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Las contraseñas no coinciden.', 3800);
        rep?.focus();
      }
    });
  });
})();
