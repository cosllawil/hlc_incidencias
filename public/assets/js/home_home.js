
(function () {
  const tabs = document.querySelectorAll('.tabbar .item');
  tabs.forEach(el => {
    el.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      el.classList.add('active');
    });
  });
})();


(function () {
  const tiles = document.querySelectorAll('.quick-card');
  tiles.forEach(t => {
    t.addEventListener('click', () => {
      tiles.forEach(x => x.classList.remove('active'));
      t.classList.add('active');
    });
  });
})();


document.oncontextmenu = () => false;


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




