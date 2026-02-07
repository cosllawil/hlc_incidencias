(() => {
  const pre = document.getElementById("hlcPreload");
  if (pre) {
    const prevOverflow = document.documentElement.style.overflow;
    document.documentElement.style.overflow = "hidden";
    let closed = false;
    function forceRemove() {
      if (pre && pre.parentNode) pre.parentNode.removeChild(pre);
    }
    function hide() {
      if (closed) return;
      closed = true;
      pre.classList.add("hlc-preload--hide");
      document.documentElement.style.overflow = prevOverflow || "";
      setTimeout(forceRemove, 650);
    }
    window.addEventListener("load", () => setTimeout(hide, 250));
    setTimeout(hide, 3000);
    setTimeout(hide, 8000);
  }


function hlcToast({ type = "info", title = "Aviso", message = "", delay = 2800 }) {
  const wrap = document.getElementById("hlcToastWrap");
  if (!wrap) {
    if (message) console.warn(title || "Aviso", message);
    return;
  }
  const icon =
    (type === "success") ? "bi-check-circle-fill" :
    (type === "error")   ? "bi-x-circle-fill" :
                           "bi-info-circle-fill";
  const t = document.createElement("div");
  t.className = `hlc-toast hlc-toast--${type || "info"}`;
  t.innerHTML = `
    <div class="hlc-toast__icon"><i class="bi ${icon}"></i></div>
    <div style="min-width:0">
      <p class="hlc-toast__title">${title || "Aviso"}</p>
      <p class="hlc-toast__msg">${message || ""}</p>
    </div>
    <button class="hlc-toast__close" type="button" aria-label="Cerrar">&times;</button>
  `;
  const close = () => {
    t.style.opacity = "0";
    t.style.transform = "translateY(-6px)";
    setTimeout(() => t.remove(), 160);
  };
  t.querySelector(".hlc-toast__close")?.addEventListener("click", close);

  wrap.appendChild(t);
  setTimeout(close, Math.max(1200, delay || 2800));
}


  const HLC = window.HLC || {};
  const BASE = (HLC.base || "");
  const form = document.getElementById("cambiarcontrasena");
  if (!form) return;
  const btn = document.getElementById("btnCambiar");
  function val() {
    const a = (form.ActualPassword?.value || "").trim();
    const p = (form.Password?.value || "").trim();
    const r = (form.RepetirPassword?.value || "").trim();
    if (!a) return "Ingresa tu contraseña actual.";
    if (!p) return "Ingresa tu nueva contraseña.";
    if (!r) return "Repite tu nueva contraseña.";
    if (p.length < 6) return "La nueva contraseña debe tener mínimo 6 caracteres.";
    if (p !== r) return "La nueva contraseña no coincide.";
    if (a === p) return "La nueva contraseña no puede ser igual a la actual.";
    return "";
  }
  let sending = false;
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (sending) return;
    const err = val();
    if (err) {
      hlcToast({ type: "error", title: "Validación", message: err });
      return;
    }
    sending = true;
    if (btn) btn.disabled = true;
    const controller = new AbortController();
    const timeoutId = setTimeout(() => {
      try { controller.abort(); } catch { }
    }, 12000);
    try {
      hlcToast({ type: "info", title: "Procesando", message: "Actualizando contraseña…" });
      const fd = new FormData(form);
      const res = await fetch(`${BASE}/home/cambiarcontrasena?ajax=1`, {
        method: "POST",
        body: fd,
        signal: controller.signal
      });
      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch {
        throw new Error("Respuesta inválida del servidor.");
      }
      if (!res.ok || data?.ok !== true) {
        throw new Error(data?.message || "No se pudo actualizar la contraseña.");
      }
      hlcToast({ type: "success", title: "Correcto", message: data.message || "Contraseña actualizada." });
      form.reset();
    } catch (ex) {
      const msg =
        ex?.name === "AbortError"
          ? "El servidor demoró en responder."
          : (ex?.message || "Error inesperado.");

      hlcToast({ type: "error", title: "Error", message: msg });
    } finally {
      clearTimeout(timeoutId);
      sending = false;
      if (btn) btn.disabled = false;
    }
  });
})();
