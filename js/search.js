document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("searchButton");
  const input = document.getElementById("searchInput");
  const postsDiv = document.getElementById("posts");

  if (!btn || !input || !postsDiv) return;

  // Reuse the same timeAgo helper as posts.js
  function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString.replace(" ", "T"));
    const diff = Math.floor((now - date) / 1000);
    if (diff < 60) return "hace unos segundos";
    if (diff < 3600) return `hace ${Math.floor(diff / 60)} minutos`;
    if (diff < 86400) return `hace ${Math.floor(diff / 3600)} horas`;
    return `hace ${Math.floor(diff / 86400)} días`;
  }

  function renderResults(data) {
    if (!Array.isArray(data) || data.length === 0) {
      postsDiv.innerHTML = "<p>No se encontraron resultados.</p>";
      return;
    }

    postsDiv.innerHTML =
      '<div class="posts-grid">' +
      data
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
                    ${post.images
                      .map(
                        (img, idx) => ` <div class="carousel-item${
                          idx === 0 ? " active" : ""
                        }">
                        <img src="data:image/jpeg;base64,${img}" class="d-block w-100" alt="Imagen ${
                          idx + 1
                        } de ${post.title}">
                      </div>
                    `
                      )
                      .join("")}
                  </div>
                  ${
                    post.images.length > 1
                      ? `
                  <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                  </button>
                  `
                      : ""
                  }
                </div>
              `;
          }
          const likesCount = post.likesCount ? post.likesCount : 0;

          return `
              <article class="post-card">
                <div class="post-image">
                  ${carouselHtml}
                </div>
                <div class="post-content" onclick="location.href='../visual/viewRecipe.php?id=${
                  post.postId
                }'">
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
  }

  function doSearch() {
    const contenido = input.value.trim();
    // Allow empty text search if tags are selected
    if (!contenido && selectedTags.size === 0) {
      postsDiv.innerHTML = "<p>Escribe algo para buscar o selecciona etiquetas.</p>";
      return;
    }

    // Construir query params
    const params = new URLSearchParams();
    if (contenido) params.set('contenido', contenido);
    if (selectedTags.size > 0) params.set('tags', Array.from(selectedTags).join(','));

    fetch(`../searchRecipes.php?${params.toString()}`)
      .then((res) => {
        if (!res.ok) throw new Error("Error de red");
        return res.json();
      })
      .then(renderResults)
      .catch((err) => {
        console.error("Error al buscar recetas:", err);
        postsDiv.innerHTML = "<p>Error al realizar la búsqueda.</p>";
      });
  }

  btn.addEventListener("click", doSearch);
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") doSearch();
  });
});

// Tag selection handling (global)
document.addEventListener('DOMContentLoaded', () => {
  const tagButtons = Array.from(document.querySelectorAll('.tag-filter'));
  window.selectedTags = new Set();

  function updateTagButtonState(btn) {
    const tagId = btn.dataset.tag;
    if (window.selectedTags.has(tagId)) {
      btn.classList.remove('btn-outline-primary');
      btn.classList.add('btn-primary');
    } else {
      btn.classList.remove('btn-primary');
      btn.classList.add('btn-outline-primary');
    }
  }

  tagButtons.forEach(btn => updateTagButtonState(btn));

  tagButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const tagId = btn.dataset.tag;
      if (window.selectedTags.has(tagId)) {
        window.selectedTags.delete(tagId);
      } else {
        window.selectedTags.add(tagId);
      }
      updateTagButtonState(btn);
      // ejecutar búsqueda automática cuando cambian tags
      document.getElementById('searchButton').click();
    });
  });
});
