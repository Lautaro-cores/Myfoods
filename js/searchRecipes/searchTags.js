// js/search_tags_handler.js

/**
 * Módulo para manejar la selección de etiquetas de filtro.
 */
document.addEventListener('DOMContentLoaded', () => {
    const tagButtons = Array.from(document.querySelectorAll('.tag-filter'));
    const modal = document.getElementById('allTagsModal');
    window.selectedTags = new Set(); 

    if (tagButtons.length === 0) return;

    // Sincronizar el estado de los botones duplicados
    if (modal) {
        modal.addEventListener('shown.bs.modal', () => {
            tagButtons.forEach(btn => {
                const tagId = btn.dataset.tag;
                if (window.selectedTags.has(tagId)) {
                    btn.classList.add('active');
                }
            });
        });
    }

    /**
     * Actualiza la clase visual de un botón de etiqueta.
     * @param {HTMLElement} btn - El botón a actualizar.
     */
    function updateTagButtonState(btn) {
        const tagId = btn.dataset.tag;
        if (window.selectedTags.has(tagId)) {
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        }
    }

    // Inicializar el estado visual de los botones
    tagButtons.forEach(btn => updateTagButtonState(btn));

    // Configurar listener de clic
    tagButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tagId = btn.dataset.tag;
            
            // Alternar el estado en el Set
            if (window.selectedTags.has(tagId)) {
                window.selectedTags.delete(tagId);
            } else {
                window.selectedTags.add(tagId);
            }
            
            updateTagButtonState(btn);
            
            // Ejecutar búsqueda (delegamos el clic al botón de búsqueda principal)
            const searchButton = document.getElementById('searchButton');
            if (searchButton) {
                searchButton.click(); 
            } else {
                console.error("No se encontró el botón de búsqueda para ejecutarla.");
            }
        });
    });
});