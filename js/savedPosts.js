// savedPosts.js
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('savedPosts');
  if (!container) return;

  fetch('../getFavorites.php')
    .then((res) => res.json())
    .then((posts) => {
      if (!posts || posts.length === 0) {
        container.innerHTML = '<p>No tienes recetas guardadas.</p>';
        return;
      }

      container.innerHTML = '<div class="posts-grid">' + posts.map((post) => {
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
          <article class="post-card">
            <div class="post-image">${carouselHtml}</div>
            <div class="post-content">
              <div class="post-header">
                <div class="post-left">
                  <img class="post-avatar" src="${avatarUrl}" alt="Avatar">
                  <div class="post-meta">
                    <div class="post-author">${post.userName}</div>
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
      }).join('') + '</div>';

      // Handlers
      container.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          window.location.href = `viewRecipe.php?id=${id}`;
        });
      });

      container.querySelectorAll('.remove-fav').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const form = new URLSearchParams();
          form.append('postId', id);
          fetch('../toggleFavorite.php', {
            method: 'POST',
            body: form.toString(),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          }).then(res => res.json()).then(r => {
            if (r.success) {
              btn.closest('article').remove();
            } else {
              alert(r.msj || 'Error al quitar de guardados');
            }
          }).catch(err => console.error(err));
        });
      });
    })
    .catch(err => {
      console.error('Error al cargar favoritos', err);
      container.innerHTML = '<p>Error al cargar tus recetas guardadas.</p>';
    });
});
