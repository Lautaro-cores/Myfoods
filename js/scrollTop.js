// scrollTop.js
// Muestra el botón cuando se baja cierta cantidad y al hacer click vuelve suavemente arriba
(function () {
  const SHOW_AFTER = 300; // px scrolled
  let btn;

  function createButton() {
    btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'scroll-top';
    btn.setAttribute('aria-label', 'Ir arriba');
    btn.innerHTML = '<i class="bi bi-arrow-up-short" aria-hidden="true"></i>';
    document.body.appendChild(btn);

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  function onScroll() {
    if (!btn) return;
    if (window.scrollY > SHOW_AFTER) {
      btn.classList.add('show');
    } else {
      btn.classList.remove('show');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    createButton();
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  });
})();