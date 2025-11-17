// viewRecipe.js
// este archivo maneja la vista de una receta individual, incluyendo comentarios, likes y favoritos

// importa las funciones necesarias para manejar comentarios, likes y favoritos
import { loadComments, setupCommentForm } from './comments.js';
import { loadLikes, setupLikeToggle } from './likes.js';
import { loadFavorite, setupFavoriteToggle } from './favorites.js';

document.addEventListener("DOMContentLoaded", () => {
    // Obtener postId y la URL de imagen por defecto
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get("id");
    const defaultImageUrl = 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    if (!postId) return;

    // se cargan los comentarios y se configura el formulario de comentarios
    loadComments(postId, defaultImageUrl);
    setupCommentForm(postId, defaultImageUrl);

    // se cargan los likes
    loadLikes(postId);
    setupLikeToggle(postId);

    // se cargan los favoritos
    loadFavorite(postId);
    setupFavoriteToggle(postId);

    // recargar la puntuación después de enviar un comentario
    window.addEventListener('commentPosted', () => {
        if (typeof loadRecipeRating === 'function') {
            loadRecipeRating();
        }
    });
});