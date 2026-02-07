$(function () {
    $('#TipoProblema').select2({
        placeholder: 'Seleccione el problema',
        allowClear: true,
        width: '100%'
    });
});


$(function () {
    $('#Oficina').select2({
        placeholder: 'Seleccione la oficina ó servicio',
        allowClear: true,
        width: '100%'
    });
});



const telefono = document.getElementById('Telefono');
telefono.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});


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
    const Oficina = $id('Oficina');
    const TipoProblema = $id('TipoProblema');
    const Descripcion = $id('Descripcion');
    const Foto = $id('Foto');
    const Telefono = $id('Telefono');
    if (!form) return;
    const onlyDigits = (s) => (s || '').replace(/\D+/g, '');
    function validarOficina(showToast = false) {
        const ok = !!(Oficina && Oficina.value && String(Oficina.value).trim() !== '');
        if (!ok && showToast) hlcToast('error', 'Validación', 'Seleccione la oficina.', 3200);
        return ok;
    }
    function validarTipo(showToast = false) {
        const ok = !!(TipoProblema && TipoProblema.value && String(TipoProblema.value).trim() !== '');
        if (!ok && showToast) hlcToast('error', 'Validación', 'Seleccione el tipo de problema.', 3200);
        return ok;
    }
    function validarDescripcion(showToast = false) {
        const val = (Descripcion?.value || '').trim();
        const ok = val.length >= 5;
        if (!ok && showToast) hlcToast('error', 'Validación', 'Ingrese una descripción (mín. 5 caracteres).', 3400);
        return ok;
    }
    function validarTelefono(showToast = false) {
        if (!Telefono) return true;
        Telefono.value = onlyDigits(Telefono.value).slice(0, 9);

        const ok = Telefono.value.length === 9;
        if (!ok && showToast) hlcToast('error', 'Validación', 'Ingrese un teléfono válido de 9 dígitos.', 3400);
        return ok;
    }

    function validarFoto(showToast = false) {
        if (!Foto) return true;

        const files = Foto.files || [];
        if (!files.length) {
            if (showToast) hlcToast('error', 'Validación', 'Seleccione una foto.', 3200);
            return false;
        }

        const file = files[0];
        const maxMB = 5;
        const name = (file.name || '').toLowerCase();

        const okExt = /\.(jpg|jpeg|png|webp)$/.test(name);
        const okSize = file.size <= maxMB * 1024 * 1024;

        if (!okExt) {
            if (showToast) hlcToast('error', 'Validación', 'Formato de foto no permitido (jpg, png, webp).', 3600);
            return false;
        }
        if (!okSize) {
            if (showToast) hlcToast('error', 'Validación', `La foto supera ${maxMB}MB.`, 3600);
            return false;
        }
        return true;
    }


    Oficina?.addEventListener('change', () => validarOficina(true));
    TipoProblema?.addEventListener('change', () => validarTipo(true));
    Descripcion?.addEventListener('blur', () => validarDescripcion(true));
    Telefono?.addEventListener('blur', () => validarTelefono(true));
    Telefono?.addEventListener('input', () => {
        Telefono.value = onlyDigits(Telefono.value).slice(0, 9);
    });
    Foto?.addEventListener('change', () => validarFoto(true));
    form.addEventListener('submit', (e) => {
        if (!validarOficina(false)) {
            e.preventDefault();
            hlcToast('error', 'Validación', 'Seleccione la oficina.', 3200);
            Oficina?.focus();
            return;
        }
        if (!validarTipo(false)) {
            e.preventDefault();
            hlcToast('error', 'Validación', 'Seleccione el tipo de problema.', 3200);
            TipoProblema?.focus();
            return;
        }
        if (!validarDescripcion(false)) {
            e.preventDefault();
            hlcToast('error', 'Validación', 'Ingrese una descripción (mín. 5 caracteres).', 3400);
            Descripcion?.focus();
            return;
        }
        if (!validarFoto(false)) {
            e.preventDefault();
            hlcToast('error', 'Validación', 'Seleccione una foto válida (jpg/png/webp y máx. 5MB).', 3600);
            Foto?.focus();
            return;
        }
        if (!validarTelefono(false)) {
            e.preventDefault();
            hlcToast('error', 'Validación', 'Ingrese un teléfono válido de 9 dígitos.', 3400);
            Telefono?.focus();
            return;
        }

    });

})();