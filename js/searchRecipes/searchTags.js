document.addEventListener('DOMContentLoaded', () => {
    // Obtiene todos los botones de filtro de tags
    const tagButtons = Array.from(document.querySelectorAll('.tag-filter'));
    // Obtiene la referencia al modal de tags (si existe)
    const modal = document.getElementById('allTagsModal');
    // Inicializa el Set global que almacena los IDs de los tags seleccionados
    window.selectedTags = new Set(); 

    // Si no hay botones de tag, termina la ejecución
    if (tagButtons.length === 0) return;

    // Sincronizar el estado de los botones duplicados (si hay un modal)
    if (modal) {
        // Se ejecuta cuando el modal se muestra completamente
        modal.addEventListener('shown.bs.modal', () => {
            // Recorre todos los botones de tag
            tagButtons.forEach(btn => {
                const tagId = btn.dataset.tag;
                if (window.selectedTags.has(tagId)) {
                    btn.classList.add('active');
                }
            });
        });
    }

    // Función para actualizar el estilo de todos los botones que representan el mismo tag
    function updateTagButtonState(btn) {
        const tagId = btn.dataset.tag;
        const isSelected = window.selectedTags.has(String(tagId));
        // actualizar todos los botones con el mismo data-tag (modal y lista principal)
        const sameBtns = document.querySelectorAll(`.tag-filter[data-tag="${tagId}"]`);
        sameBtns.forEach(b => {
            if (isSelected) {
                b.classList.remove('btn-outline-primary');
                b.classList.add('btn-primary');
                b.classList.add('active');
            } else {
                b.classList.remove('btn-primary');
                b.classList.remove('active');
                b.classList.add('btn-outline-primary');
            }
        });
    }

    // Inicializa el estado visual de todos los botones al cargar la página
    tagButtons.forEach(btn => updateTagButtonState(btn));

    // Configura el listener de clic para cada botón de tag
    tagButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tagId = btn.dataset.tag;
            
            // Alterna el estado del tag en el Set
            if (window.selectedTags.has(tagId)) {
                window.selectedTags.delete(tagId);
            } else {
                window.selectedTags.add(tagId);
            }
            
            // Actualiza la apariencia visual del botón
            updateTagButtonState(btn);
            
            // Ejecuta la búsqueda: preferimos la API global performSearch si existe
            if (typeof window.performSearch === 'function') {
                window.performSearch();
            } else {
                const searchButton = document.getElementById('searchButton');
                if (searchButton) searchButton.click();
                else console.error("No se encontró el botón de búsqueda ni performSearch()");
            }
        });
    });
});