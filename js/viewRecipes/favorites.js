// favorites.js
// este archivo maneja la carga y el toggle de favoritos para una receta

const favBtn = document.getElementById('favBtn');

// 1. funcion para cargar el estado de favorito del post
export function loadFavorite(postId) {
    if (!favBtn) return;
    // hace la consulta al isFavorite.php para obtener el estado de favorito del usuario
    fetch(`../isFavorite.php?postId=${postId}`)
        .then(res => res.json())
        .then(data => {
            // actualiza el estado del botón de favorito
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

// 2. funcion para configurar el toggle de favorito
export function setupFavoriteToggle(postId) {
    if (!favBtn) return;
    // agrega el evento click al boton de favorito
    favBtn.addEventListener('click', () => {
        const form = new URLSearchParams();
        form.append('postId', postId);
        // envía la solicitud para alternar el favorito al toggleFavorite.php
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