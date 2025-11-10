
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

document.addEventListener("DOMContentLoaded", () => {
  const postsDiv = document.getElementById("posts");
  if (!postsDiv) return;

  // Renderiza todas las publicaciones en la cuadrícula principal
  function renderPosts(posts) {
    if (!posts || posts.length === 0) {
      postsDiv.innerHTML = "<p>No hay recetas publicadas aún.</p>";
      return;
    }

    postsDiv.innerHTML =
      '<div class="posts-grid">' +
      posts.map((post) => {
        const avatarUrl = post.userImage
          ? `data:image/jpeg;base64,${post.userImage}`
          : "../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg";

        // Genera un carrusel de imágenes si hay más de una
        let carouselHtml = "";
        if (post.images && post.images.length > 0) {
          const carouselId = `carouselPost${post.postId}`;
          carouselHtml = `
            <div id="${carouselId}" class="carousel slide">
              <div class="carousel-inner">
                ${post.images.map((img, idx) => `
                  <div class="carousel-item${idx === 0 ? " active" : ""}">
                    <img src="data:image/jpeg;base64,${img}" class="d-block w-100" alt="Imagen ${idx + 1} de ${post.title}">
                  </div>`).join("")}
              </div>
              ${post.images.length > 1 ? `
                <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Siguiente</span>
                </button>` : ""}
            </div>
          `;
        }

        const likesCount = post.likesCount || 0;

        // Estructura visual de cada post
        return `
          <article class="post-card">
            <div class="post-image">${carouselHtml}</div>
            <div class="post-content" data-post-id="${post.postId}">
              <div class="post-header">
                <div class="post-left">
                  <img class="post-avatar" src="${avatarUrl}" alt="Avatar ${post.userName}">
                  <div class="post-meta">
                    <div class="post-author">${post.displayName}</div>
                    <div class="post-time">${timeAgo(post.postDate)}</div>
                  </div>
                </div>
                <div class="post-likes">
                  <i class="${post.userLiked > 0 ? "bi bi-heart-fill" : "bi bi-heart"}"></i>
                  <span class="likes-count">${likesCount}</span>
                  <button class="report-btn btn btn-sm" data-post-id="${post.postId}" type="button" aria-label="Denunciar publicación">
                    <i class="bi bi-flag"></i>
                  </button>
                </div>
              </div>
              <h3 class="post-title">${post.title}</h3>
              <p class="post-desc">${post.description || ""}</p>
            </div>
          </article>
        `;
      }).join("") +
      "</div>";
  }

  // Carga inicial: muestra posts aleatorios
  fetch("../getPosts.php?order=random")
    .then(res => res.json())
    .then(posts => renderPosts(posts))
    .catch(err => console.error("Error cargando posts:", err));

  // Botón "Más Likeados"
  const loadMoreBtn = document.getElementById('loadMoreButton');
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      fetch('../getPosts.php?order=likes')
        .then(res => res.json())
        .then(posts => renderPosts(posts))
        .catch(err => console.error('Error cargando posts por likes:', err));
    });
  }

  // Botón "Seguidos"
  const showFollowedBtn = document.getElementById('showFollowedButton');
  if (showFollowedBtn) {
    showFollowedBtn.addEventListener('click', () => {
      fetch('../getFollowedPosts.php')
        .then(res => res.json())
        .then(posts => renderPosts(posts))
        .catch(err => console.error('Error cargando posts de seguidos:', err));
    });
  }

  // Delegación de eventos: manejar clicks en botones o posts
  document.addEventListener('click', (e) => {
    const reportBtn = e.target.closest('.report-btn');
    if (reportBtn) {
      e.preventDefault();
      e.stopPropagation();
      const postId = reportBtn.getAttribute('data-post-id');
      const reportModalEl = document.getElementById('reportModal');
      if (reportModalEl) {
        const input = document.getElementById('reportPostId');
        if (input) input.value = postId;
        new bootstrap.Modal(reportModalEl).show();
      }
      return;
    }

    // Redirige al detalle de la receta
    const postContent = e.target.closest('.post-content');
    if (postContent) {
      const postId = postContent.getAttribute('data-post-id');
      if (postId) {
        window.location.href = `../visual/viewRecipe.php?id=${postId}`;
      }
    }
  });
});

// --- Búsqueda de recetas ---
const btn = document.getElementById('searchButton');
const input = document.getElementById('searchInput');

function goSearch() {
  const search = input.value.trim();
  window.location.href = search === ''
    ? 'searchPage.php'
    : 'searchPage.php?search=' + encodeURIComponent(search);
  input.value = '';
}

btn.addEventListener('click', goSearch);
input.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') goSearch();
});
