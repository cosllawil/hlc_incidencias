(() => {
  const $ = (id) => document.getElementById(id);
  const HLC = window.HLC || {};
  const BASE = (HLC.base || '').replace(/\/$/, '');


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
    setTimeout(() => {
      if (pre && pre.parentNode) pre.remove();
    }, 700);
  }


  window.addEventListener('load', () => setTimeout(hidePreload, 200));
  setTimeout(hidePreload, 4000);
  function hlcToast(type, title, message, ms) {
    const wrap = $('hlcToastWrap');
    if (!wrap) {
      if (message) console.warn(title || 'Aviso', message);
      return;
    }
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

  if (HLC.flashError) {
    hlcToast('error', 'Registro', HLC.flashError, 4200);
  }

  if (HLC.flashSuccess) {
    hlcToast('success', 'Registro', HLC.flashSuccess, 2400);
    setTimeout(() => {
      window.location.href = BASE + 'login';
    }, 1300);
  }


  let DOC_MAX = 15;
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const tipoSel = $('TipoDocumento');
    const dniInput = $('Dni');
    const nombres = $('Nombres');
    const apellidos = $('Apellidos');
    const telefono = $('Telefono');
    const pass = $('Password');
    const repass = $('RepetirPassword');
    if (!form || !tipoSel || !dniInput) return;
    const limpiarNombre = () => {
      if (nombres) nombres.value = '';
      if (apellidos) apellidos.value = '';
    };
    const setReadonly = (on) => {
      if (!nombres || !apellidos) return;
      nombres.readOnly = on;
      apellidos.readOnly = on;
      nombres.style.background = '#FFFFFF';
      apellidos.style.background = '#FFFFFF';
    };

    const soloLetrasMayus = (input) => {
      if (!input) return;
      input.addEventListener('input', () => {
        input.value = input.value
          .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')
          .replace(/\s{2,}/g, ' ')
          .toUpperCase();
      });
    };
    soloLetrasMayus(nombres);
    soloLetrasMayus(apellidos);
    if (telefono) {
      telefono.addEventListener('input', () => {
        telefono.value = telefono.value.replace(/\D/g, '').slice(0, 9);
      });
    }

    function aplicarTipo() {
      const tipo = tipoSel.value;
      dniInput.value = '';
      limpiarNombre();
      DOC_MAX = 15;
      dniInput.maxLength = 15;
      dniInput.placeholder = 'Documento';
      setReadonly(false);
      if (tipo === 'DNI') {
        DOC_MAX = 8;
        dniInput.maxLength = 8;
        dniInput.placeholder = 'DNI (8 dígitos)';
        setReadonly(true);
      } else if (tipo === 'CE') {
        DOC_MAX = 12;
        dniInput.maxLength = 12;
        dniInput.placeholder = 'Carnet de Extranjería (12 dígitos)';
      } else if (tipo === 'PAS') {
        DOC_MAX = 12;
        dniInput.maxLength = 12;
        dniInput.placeholder = 'Pasaporte (12 dígitos)';
      }
      setTimeout(() => dniInput.focus(), 0);
    }
    tipoSel.addEventListener('change', aplicarTipo);
    dniInput.addEventListener('input', () => {
      dniInput.value = dniInput.value.replace(/\D/g, '').slice(0, DOC_MAX);
    });

    form.addEventListener('submit', (e) => {
      const tipo = (tipoSel.value || '').trim();
      const dni = (dniInput.value || '').trim();
      const nom = (nombres?.value || '').trim();
      const ape = (apellidos?.value || '').trim();
      const tel = (telefono?.value || '').trim();
      const p1 = (pass?.value || '');
      const p2 = (repass?.value || '');

      if (!tipo) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Seleccione tipo documento.', 3200);
        return;
      }

      if (!dni) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Ingrese el documento.', 3200);
        return;
      }

      if (tipo === 'DNI' && dni.length !== 8) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'El DNI debe tener 8 dígitos.', 3200);
        return;
      }

      if (!nom || !ape) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Ingrese nombres y apellidos.', 3200);
        return;
      }

      if (tel && tel.length !== 9) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'El teléfono debe tener 9 dígitos.', 3200);
        return;
      }

      if (!p1 || !p2) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Ingrese y repita la contraseña.', 3200);
        return;
      }

      if (p1.length < 6) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'La contraseña debe tener mínimo 6 caracteres.', 3200);
        return;
      }

      if (p1 !== p2) {
        e.preventDefault();
        hlcToast('error', 'Validación', 'Las contraseñas no coinciden.', 3200);
        return;
      }

      showPreload('Registrando...');
    });


    const PROXY_URL = (HLC.reniecProxy)
      ? HLC.reniecProxy
      : (BASE + '/public/ApiReniec/reniec_proxy.php');

    let lastDni = '';
    let timer = null;

    async function consultarReniec(dni) {
      showPreload('Buscando en RENIEC...');
      try {
        const r = await fetch(`${PROXY_URL}?dni=${encodeURIComponent(dni)}`, {
          headers: { 'Accept': 'application/json' },
          cache: 'no-store'
        });
        const data = await r.json().catch(() => ({}));

        if (!r.ok) {
          hlcToast('error', 'RENIEC', 'Error HTTP consultando RENIEC. Revisa la ruta del proxy.', 4200);
          limpiarNombre();
          return;
        }
        if (!data.ok) {
          hlcToast('error', 'RENIEC', data.error || 'DNI no encontrado', 3200);
          limpiarNombre();
          return;
        }

        if (nombres) nombres.value = (data.nombres || '').toUpperCase();
        if (apellidos) {
          const ape = `${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
          apellidos.value = ape.toUpperCase();
        }

        hlcToast('success', 'RENIEC', 'Datos encontrados', 1800);
        telefono?.focus();

      } catch (e) {
        console.error(e);
        hlcToast('error', 'RENIEC', 'No se pudo consultar RENIEC. Revisa consola (F12).', 4200);
        limpiarNombre();
      } finally {
        hidePreload();
      }
    }

    dniInput.addEventListener('input', () => {
      if (tipoSel.value !== 'DNI') return;
      dniInput.value = dniInput.value.replace(/\D/g, '').slice(0, 8);
      setReadonly(true);
      const dni = dniInput.value;
      if (dni.length < 8) {
        lastDni = '';
        limpiarNombre();
        return;
      }

      if (dni === lastDni) return;
      clearTimeout(timer);
      timer = setTimeout(() => {
        lastDni = dni;
        consultarReniec(dni);
      }, 250);
    });

  });
})();
