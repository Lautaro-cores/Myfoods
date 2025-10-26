// js/view_recipe_init.js
import { loadComments, setupCommentForm } from './comments.js';
import { loadLikes, setupLikeToggle } from './likes.js';
import { loadFavorite, setupFavoriteToggle } from './favorites.js';

document.addEventListener("DOMContentLoaded", () => {
    // 1. Obtener postId y defaultImageUrl
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get("id");
    
    // Obtener la URL de imagen por defecto (ajusta el método si es necesario)
    const defaultImageUrl = 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg'; 
    
    if (!postId) return; 

    // 2. Inicializar todas las funcionalidades:
    
    // Comentarios
    loadComments(postId, defaultImageUrl);
    setupCommentForm(postId, defaultImageUrl);
    
    // Likes
    loadLikes(postId);
    setupLikeToggle(postId);
    
    // Favoritos
    loadFavorite(postId);
    setupFavoriteToggle(postId);
});