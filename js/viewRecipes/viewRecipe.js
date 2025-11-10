import { loadComments, setupCommentForm } from './comments.js';
import { loadLikes, setupLikeToggle } from './likes.js';
import { loadFavorite, setupFavoriteToggle } from './favorites.js';

document.addEventListener("DOMContentLoaded", () => {
    // Obtener postId y la URL de imagen por defecto
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get("id");
    const defaultImageUrl = 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    if (!postId) return;

    // Inicializar comentarios
    loadComments(postId, defaultImageUrl);
    setupCommentForm(postId, defaultImageUrl);

    // Inicializar likes
    loadLikes(postId);
    setupLikeToggle(postId);

    // Inicializar favoritos
    loadFavorite(postId);
    setupFavoriteToggle(postId);

    // Recargar la puntuación después de enviar un comentario
    window.addEventListener('commentPosted', () => {
        if (typeof loadRecipeRating === 'function') {
            loadRecipeRating();
        }
    });
});