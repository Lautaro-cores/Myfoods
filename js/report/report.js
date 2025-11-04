// Centraliza la lógica de abrir el modal de reporte y enviar el formulario
(function(){
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.report-btn');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    const targetType = btn.getAttribute('data-target-type') || btn.getAttribute('data-target') || 'post';
    const targetId = btn.getAttribute('data-target-id') || btn.getAttribute('data-post-id') || btn.getAttribute('data-id');

    const modalEl = document.getElementById('reportModal');
    if (!modalEl) {
      console.error('No se encontró #reportModal en la página');
      return;
    }

    const inputType = document.getElementById('reportTargetType');
    const inputId = document.getElementById('reportTargetId');
    const reason = document.getElementById('reportReason');

    if (inputType) inputType.value = targetType;
    if (inputId) inputId.value = targetId;
    if (reason) reason.value = '';

    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
  });

  // Manejar envío del formulario
  document.addEventListener('submit', function(e){
    const form = e.target;
    if (!form || form.id !== 'reportForm') return;
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const fd = new FormData(form);

    fetch('../submitReport.php', {
      method: 'POST',
      body: fd,
    })
    .then(res => res.json())
    .then(data => {
      if (data && data.success) {
        const modalEl = document.getElementById('reportModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();
        // Mensaje simple
        form.reset();
        
      } else {
        alert('No se pudo enviar el reporte: ' + (data && data.error ? data.error : 'Error desconocido'));
      }
    })
    .catch(err => {
      console.error('Error al enviar reporte:', err);
      alert('Error al enviar el reporte. Revisa la consola.');
    })
    .finally(() => {
      if (submitBtn) submitBtn.disabled = false;
    });
  });
})();
