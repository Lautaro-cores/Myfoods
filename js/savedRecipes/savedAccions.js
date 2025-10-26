// js/saved_post_actions.js

/**
 * Configura los event listeners para los botones "Ver" y "Quitar de Guardados"
 * una vez que se ha renderizado el contenido.
 * @param {HTMLElement} container - El contenedor que contiene las post-card.
 */
export function setupPostActions(container) {
    
    // 1. Handlers para el botón "Ver"
    container.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            window.location.href = `viewRecipe.php?id=${id}`;
        });
    });

    // 2. Handlers para el botón "Quitar de Guardados"
    container.querySelectorAll('.remove-fav').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const form = new URLSearchParams();
            form.append('postId', id);
            
            fetch('../toggleFavorite.php', {
                method: 'POST',
                body: form.toString(),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(res => res.json())
            .then(r => {
                if (r.success) {
                    // Elimina la tarjeta visualmente del DOM
                    const postCard = btn.closest('article');
                    if(postCard) postCard.remove(); 
                    
                    // Si ya no quedan posts, muestra el mensaje de lista vacía
                    if (container.querySelectorAll('article').length === 0) {
                        container.innerHTML = '<p>Ya no tienes recetas guardadas.</p>';
                    }
                } else {
                    alert(r.msj || 'Error al quitar de guardados');
                }
            })
            .catch(err => {
                console.error('Error al alternar favorito:', err);
                alert('Error de red al intentar quitar el favorito.');
            });
        });
    });
}