// js/favorite_handler.js

const favBtn = document.getElementById('favBtn');

// 1. Función de Carga de Favoritos (Obtiene el estado actual)
export function loadFavorite(postId) {
    if (!favBtn) return;
    
    fetch(`../isFavorite.php?postId=${postId}`)
        .then(res => res.json())
        .then(data => {
            if (data.isFavorite) {
                favBtn.classList.add('saved');
                favBtn.innerHTML = '<i class="bi bi-bookmark-fill"></i>';
            } else {
                favBtn.classList.remove('saved');
                favBtn.innerHTML = '<i class="bi bi-bookmark"></i>';
            }
        })
        .catch(err => console.error('Error cargando estado guardado:', err));
}

// 2. Setup del Evento para el botón
export function setupFavoriteToggle(postId) {
    if (!favBtn) return;

    favBtn.addEventListener('click', () => {
        const form = new URLSearchParams();
        form.append('postId', postId);

        fetch('../toggleFavorite.php', {
            method: 'POST',
            body: form.toString(),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(res => res.json()).then(result => {
            if (result.success) {
                loadFavorite(postId); // Recarga el estado después del toggle
            } else {
                alert(result.msj || 'No se pudo actualizar favorito.');
            }
        }).catch(err => console.error('Error al alternar favorito:', err));
    });
}