// Centraliza la lógica de abrir el modal de reporte y enviar el formulario
(function(){
    // Escucha clics en todo el documento para delegación de eventos
    document.addEventListener('click', function(e){
        // Busca el botón de reporte más cercano al elemento clickeado
        const btn = e.target.closest('.report-btn');
        // Si no es un botón de reporte, termina la ejecución
        if (!btn) return;
        // Previene la acción por defecto y la propagación del evento
        e.preventDefault();
        e.stopPropagation();

        // Determina el tipo de elemento a reportar ('post', 'user', 'comment', etc.)
        const targetType = btn.getAttribute('data-target-type') || btn.getAttribute('data-target') || 'post';
        // Determina el ID del elemento a reportar
        const targetId = btn.getAttribute('data-target-id') || btn.getAttribute('data-post-id') || btn.getAttribute('data-id');

        // Obtiene la referencia al elemento modal de Bootstrap
        const modalEl = document.getElementById('reportModal');
        // Muestra un error si no se encuentra el modal
        if (!modalEl) {
            console.error('No se encontró #reportModal en la página');
            return;
        }

        // Obtiene las referencias a los inputs ocultos dentro del modal
        const inputType = document.getElementById('reportTargetType');
        const inputId = document.getElementById('reportTargetId');
        const reason = document.getElementById('reportReason');

        // Asigna el tipo de objetivo y el ID a los inputs ocultos del formulario
        if (inputType) inputType.value = targetType;
        if (inputId) inputId.value = targetId;
        // Limpia el campo de razón para un nuevo reporte
        if (reason) reason.value = '';

        // Crea una instancia del modal de Bootstrap y lo muestra
        modalEl.show();
    });

    // Manejar el envío del formulario de reporte
    document.addEventListener('submit', function(e){
        const form = e.target;
        // Verifica si el formulario enviado es el formulario de reporte
        if (!form || form.id !== 'reportForm') return;
        // Previene el envío estándar del formulario
        e.preventDefault();

        // Deshabilita el botón de envío para evitar envíos duplicados
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        // Crea un objeto FormData con los datos del formulario
        const fd = new FormData(form);

        // Envía el reporte al servidor a través de Fetch API
        fetch('../submitReport.php', {
            method: 'POST',
            body: fd,
        })
        // Procesa la respuesta, asumiendo que siempre será JSON
        .then(res => res.json())
        .then(data => {
            // Verifica si el servidor reportó éxito
            if (data && data.success) {
                // Si es exitoso, oculta el modal
                const modalEl = document.getElementById('reportModal');
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
                // Limpia el formulario después del éxito
                form.reset();
                
            } else {
                // Muestra una alerta con el mensaje de error del servidor o un error genérico
                alert('No se pudo enviar el reporte: ' + (data && data.error ? data.error : 'Error desconocido'));
            }
        })
        .catch(err => {
            // Maneja cualquier error de red o de la promesa
            console.error('Error al enviar reporte:', err);
            alert('Error al enviar el reporte. Revisa la consola');
        })
        .finally(() => {
            // Se ejecuta siempre, restablece el botón de envío sin importar el resultado
            if (submitBtn) submitBtn.disabled = false;
        });
    });
})();