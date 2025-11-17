// savedPosts.js
// este archivo maneja la carga y renderización de las recetas guardadas por el usuario

import { setupPostActions } from './savedAccions.js'; // Se importa la función para configurar los event listeners de las tarjetas

function timeAgo(dateString) {
  // Convierte una fecha a formato relativo (ejemplo: "hace 2 horas")
  const now = new Date();
  const date = new Date(dateString.replace(" ", "T"));
  const diff = Math.floor((now - date) / 1000);
  if (diff < 60) return "hace unos segundos";
  if (diff < 3600) return `hace ${Math.floor(diff / 60)} minutos`;
  if (diff < 86400) return `hace ${Math.floor(diff / 3600)} horas`;
  return `hace ${Math.floor(diff / 86400)} días`;
}

function createSavedPostCard(post) {
    // Determina la URL del avatar del usuario, usando una imagen por defecto si no hay imagen de usuario
    const avatarUrl = post.userImage ? `data:image/jpeg;base64,${post.userImage}` : "../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg";
    let carouselHtml = '';
    
    // Generación del código HTML para el carrusel de imágenes de la receta
    if (post.images && post.images.length > 0) {
      // Se genera un ID único para el carrusel basado en el ID de la publicación
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

    // Retorna la estructura HTML completa de la tarjeta de receta
    return `
      <article class="post-card" data-post-id="${post.postId}">
        <div class="post-image">${carouselHtml}</div>
        <div class="post-content">
          <div class="post-header">
            <div class="post-left">
              <img class="post-avatar" src="${avatarUrl}" alt="Avatar">
              <div class="post-meta">
                <div class="post-author">${post.displayName}</div>
                <div class="post-time">${timeAgo(post.postDate)}</div>
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
    // Obtiene el contenedor donde se renderizarán los posts guardados
    const container = document.getElementById('savedPosts');
    // Si no encuentra el contenedor, detiene la ejecución
    if (!container) return;

    // Inicia la petición para obtener la lista de recetas favoritas del usuario
    fetch('../getFavorites.php')
        .then((res) => {
            // Verifica si la respuesta HTTP fue exitosa
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            // Parsea la respuesta como JSON
            return res.json();
        })
        .then((posts) => {
            // Si no hay posts o la lista está vacía, muestra un mensaje de lista vacía
            if (!posts || posts.length === 0) {
                container.innerHTML = '<p>No tienes recetas guardadas.</p>';
                return;
            }

            // Mapea el array de posts para crear un array de cadenas HTML y luego las une
            const postsHtml = posts.map(createSavedPostCard).join('');
            // Renderiza el HTML de las tarjetas dentro de un contenedor de cuadrícula
            container.innerHTML = `<div class="posts-grid">${postsHtml}</div>`;

            // Configura los listeners de clic en los botones de "Ver" y "Quitar de Guardados"
            setupPostActions(container); 
        })
        .catch(err => {
            // Captura errores durante la carga o el parsing JSON
            console.error('Error al cargar favoritos', err);
            // Muestra un mensaje de error al usuario
            container.innerHTML = '<p>Error al cargar tus recetas guardadas. Intenta más tarde.</p>';
        });
});