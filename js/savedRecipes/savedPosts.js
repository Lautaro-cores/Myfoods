// js/saved_post_renderer.js

import { setupPostActions } from './savedAccions.js'; // Importamos el manejador de acciones

/**
 * Crea el HTML completo para una tarjeta de receta guardada.
 * Esta función es extraída de tu bloque .map() original.
 */
function createSavedPostCard(post) {
    const avatarUrl = post.userImage ? `data:image/jpeg;base64,${post.userImage}` : "../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg";
    let carouselHtml = '';
    
    if (post.images && post.images.length > 0) {
      const carouselId = `carouselSaved${post.postId}`;
      carouselHtml = `
          <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                  ${post.images.map((img, idx) => `
                      <div class="carousel-item${idx === 0 ? " active" : ""}">
                          <img src="data:image/jpeg;base64,${img}" class="d-block w-100" alt="Imagen">
                      </div>
                  `).join('')}
              </div>
              ${post.images.length > 1 ? `
              <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Anterior</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Siguiente</span>
              </button>
              ` : ''}
          </div>`;
    }

    return `
      <article class="post-card" data-post-id="${post.postId}">
        <div class="post-image">${carouselHtml}</div>
        <div class="post-content">
          <div class="post-header">
            <div class="post-left">
              <img class="post-avatar" src="${avatarUrl}" alt="Avatar">
              <div class="post-meta">
                <div class="post-author">${post.displayName}</div>
                <div class="post-time">${post.postDate}</div>
              </div>
            </div>
            <div class="post-actions">
              <button class="buttono view-btn" data-id="${post.postId}">Ver</button>
              <button class="buttonw remove-fav" data-id="${post.postId}"><i class="bi bi-bookmark-fill"></i></button>
            </div>
          </div>
          <h3 class="post-title">${post.title}</h3>
          <p class="post-desc">${post.description || ''}</p>
        </div>
      </article>
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('savedPosts');
    if (!container) return;

    fetch('../getFavorites.php')
        .then((res) => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        })
        .then((posts) => {
            if (!posts || posts.length === 0) {
                container.innerHTML = '<p>No tienes recetas guardadas.</p>';
                return;
            }

            // Renderizar el HTML
            const postsHtml = posts.map(createSavedPostCard).join('');
            container.innerHTML = `<div class="posts-grid">${postsHtml}</div>`;

            // Configurar los handlers importados
            setupPostActions(container); 
        })
        .catch(err => {
            console.error('Error al cargar favoritos', err);
            container.innerHTML = '<p>Error al cargar tus recetas guardadas. Intenta más tarde.</p>';
        });
});