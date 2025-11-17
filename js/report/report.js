// Centraliza la lógica de abrir el modal de reporte y enviar el formulario
(function(){
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.report-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();

        // determina el tipo y el id del elemento a reportar
        const targetType = btn.getAttribute('data-target-type') || btn.getAttribute('data-target') || 'post';
        const targetId = btn.getAttribute('data-target-id') || btn.getAttribute('data-post-id') || btn.getAttribute('data-id');

        const modalEl = document.getElementById('reportModal');
        if (!modalEl) {
            console.error('no se encontró #reportModal en la página');
            return;
        }

        const inputType = document.getElementById('reportTargetType');
        const inputId = document.getElementById('reportTargetId');
        const reason = document.getElementById('reportReason');

        // configura los datos del reporte y limpia el campo de razón
        if (inputType) inputType.value = targetType;
        if (inputId) inputId.value = targetId;
        if (reason) reason.value = '';

        // muestra el modal
   const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
        modalEl.show();
    });

    // manejar el envío del formulario de reporte
    document.addEventListener('submit', function(e){
        const form = e.target;
        if (!form || form.id !== 'reportForm') return;
        e.preventDefault();

        // deshabilita el botón de envío
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        const fd = new FormData(form);

        // envía los datos del formulario al servidor
        fetch('../submitReport.php', {
            method: 'POST',
            body: fd,
        })
        .then(res => res.json())
        .then(data => {
            // si es exitoso, oculta el modal y notifica al usuario
            if (data && data.success) {
                const modalEl = document.getElementById('reportModal');
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
                form.reset();
                alert('reporte enviado con éxito');
            } else {
                alert('no se pudo enviar el reporte: ' + (data && data.error ? data.error : 'error desconocido'));
            }
        })
        .catch(err => {
            // maneja errores de la red o del proceso de fetch
            console.error('error al enviar reporte:', err);
            alert('error al enviar el reporte. revisa la consola');
        })
        .finally(() => {
            // restablece el estado del botón de envío
            if (submitBtn) submitBtn.disabled = false;
        });
    });
})();