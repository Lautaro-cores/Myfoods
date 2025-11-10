(function () {
  const SHOW_AFTER = 300; // Cantidad de píxeles que deben desplazarse antes de mostrar el botón
  let btn;

  // Crea el botón de "volver arriba" y lo agrega al documento
  function createButton() {
    btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'scroll-top';
    btn.setAttribute('aria-label', 'Ir arriba');
    btn.innerHTML = '<i class="bi bi-arrow-up-short" aria-hidden="true"></i>';
    document.body.appendChild(btn);

    // Al hacer clic, se desplaza la ventana suavemente hacia la parte superior
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Controla la visibilidad del botón según la posición del scroll
  function onScroll() {
    if (!btn) return;
    if (window.scrollY > SHOW_AFTER) {
      btn.classList.add('show');
    } else {
      btn.classList.remove('show');
    }
  }

  // Espera a que el DOM esté cargado para inicializar el botón y el evento de scroll
  document.addEventListener('DOMContentLoaded', () => {
    createButton();
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  });
})();
