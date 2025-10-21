// posts.js
// Descarga la lista de recetas publicadas y la renderiza en la página principal.

// Convierte una fecha en una cadena relativa (ej. "hace 2 horas").
function timeAgo(dateString) {
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
  if (!postsDiv) {
    console.error("No se encontró el contenedor #posts en la página");
    return;
  }

  fetch("../getPosts.php")
    .then((res) => res.json())
    .then((posts) => {
      if (!posts || posts.length === 0) {
        postsDiv.innerHTML = "<p>No hay recetas publicadas aún.</p>";
        return;
      }

      // construir grid de publicaciones
      postsDiv.innerHTML =
        '<div class="posts-grid">' +
        posts
          .map((post) => {
            const avatarUrl = post.userImage
              ? `data:image/jpeg;base64,${post.userImage}`
              : "../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg";
            // Carrousel de imágenes
            let carouselHtml = "";
            if (post.images && post.images.length > 0) {
              const carouselId = `carouselPost${post.postId}`;
              carouselHtml = `
                <div id="${carouselId}" class="carousel slide">
                  <div class="carousel-inner">
                    ${post.images.map((img, idx) => ` <div class="carousel-item${idx === 0 ? " active" : ""}">
                        <img src="data:image/jpeg;base64,${img}" class="d-block w-100" alt="Imagen ${idx + 1} de ${post.title}">
                      </div>
                    `
                      )
                      .join("")}
                  </div>
                  ${ post.images.length > 1 ? `
                  <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                  </button>
                  `
                  : ""}
                </div>
              `;
            }
            const likesCount = post.likesCount ? post.likesCount : 0;

            return `
              <article class="post-card">
                <div class="post-image">
                  ${carouselHtml}
                </div>
                <div class="post-content" onclick="location.href='../visual/viewRecipe.php?id=${post.postId}'">
                  <div class="post-header">
                    <div class="post-left">
                      <img class="post-avatar" src="${avatarUrl}" alt="Avatar ${
              post.userName
            }">
                      <div class="post-meta">
                        <div class="post-author">${post.userName}</div>
                        <div class="post-time">${timeAgo(post.postDate)}</div>
                      </div>
                    </div>
                    <div class="post-likes"><i class="bi bi-heart"></i> <span class="likes-count">${likesCount}</span></div>
                  </div>
                  <h3 class="post-title">${post.title}</h3>
                  <p class="post-desc">${
                    post.description ? post.description : ""
                  }</p>
                </div>
              </article>
            `;
          })
          .join("") +
        "</div>";
    })
    .catch((err) => console.error("Error cargando posts:", err));
});
 const btn = document.getElementById('searchButton');
  const input = document.getElementById('searchInput');


                function goSearch() {
                    const search = input.value.trim();
                    if (search === '') {
                        window.location.href = 'searchPage.php';
                    } else {
                        window.location.href = 'searchPage.php?search=' + encodeURIComponent(search);
                    }
                    input.value = '';
                }
                btn.addEventListener('click', goSearch);
                input.addEventListener('keydown', function(e){
                    if (e.key === 'Enter') goSearch();
                });