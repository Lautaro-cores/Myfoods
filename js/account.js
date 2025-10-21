// account.js
// Gestiona la subida de la imagen de perfil desde la vista de cuenta.
// Reemplaza la imagen mostrada en la página con la nueva URL devuelta por el servidor.

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formImage");
  const userImageInput = document.getElementById("subirArchivo");
  const profileImage = document.querySelector('img[alt="Imagen de perfil"]');
  const editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
  const imagePreview = document.getElementById('imagePreview');

  if (!form) return;


  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (
      !userImageInput ||
      !userImageInput.files ||
      userImageInput.files.length === 0
    ) {
      alert("Selecciona un archivo primero.");
      return;
    }

    const formData = new FormData();
    formData.append("userImage", userImageInput.files[0]);

    try {
      const response = await fetch("../uploadImage.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        // Si el servidor devuelve una URL directa, usarla; si no, forzar recarga desde getUserImage.php
        if (result.imageUrl) profileImage.src = result.imageUrl;
        else profileImage.src = "../getUserImage.php?" + new Date().getTime();

        editProfileModal.hide(); // Cerrar el modal después de subir la imagen
        alert(result.msj);
      } else {
        alert(result.msj || "Error al subir imagen");
      }
    } catch (err) {
      console.error("Error subiendo imagen:", err);
      alert("Error de red al subir la imagen.");
    }
  });

  // Preview logic: show selected file(s) as preview(s)
  if (userImageInput && imagePreview) {
    userImageInput.addEventListener('change', () => {
      // Clear previous preview
      imagePreview.innerHTML = '';
      const file = userImageInput.files && userImageInput.files[0];
      if (!file) return;

      const reader = new FileReader();
      const container = document.createElement('div');
      container.className = 'preview-image';

      reader.onload = (e) => {
        container.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
        imagePreview.appendChild(container);
      };

      reader.readAsDataURL(file);
    });
  }

  // When modal is hidden, clear preview & file input
  const modalEl = document.getElementById('editProfileModal');
  modalEl.addEventListener('hidden.bs.modal', () => {
    if (imagePreview) imagePreview.innerHTML = '';
    if (userImageInput) userImageInput.value = '';
  });

  // --- Cargar y mostrar recetas del usuario ---
  const userPostsDiv = document.getElementById('userPosts');
  if (userPostsDiv) {
    // Obtener username desde la URL (parámetro 'username')
    const params = new URLSearchParams(window.location.search);
    const username = params.get('username');
    if (username) {
      fetch(`../getUserPosts.php?username=${encodeURIComponent(username)}`)
        .then(res => res.json())
        .then(data => {
          if (!Array.isArray(data) || data.length === 0) {
            userPostsDiv.innerHTML = '<p>No hay recetas recientes.</p>';
            return;
          }

          // Render simple tarjetas similares a posts.js
          userPostsDiv.innerHTML = '<div class="posts-grid">' + data.map(post => {
            const avatarUrl = post.userImage ? `data:image/jpeg;base64,${post.userImage}` : '../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
            const firstImg = (post.images && post.images.length>0) ? `data:image/jpeg;base64,${post.images[0]}` : '';
            const likesCount = post.likesCount ? post.likesCount : 0;
            return `\
              <article class="post-card">\
                <div class="post-image">\
                  ${ firstImg ? `<img src="${firstImg}" class="d-block w-100" alt="Imagen de ${post.title}">` : '' }\
                </div>\
                <div class="post-content" onclick="location.href='viewRecipe.php?id=${post.postId}'">\
                  <div class="post-header">\
                    <div class="post-left">\
                      <img class="post-avatar" src="${avatarUrl}" alt="Avatar ${post.userName}">\
                      <div class="post-meta">\
                        <div class="post-author">${post.userName}</div>\
                        <div class="post-time">${post.postDate}</div>\
                      </div>\
                    </div>\
                    <div class="post-likes"><i class="bi bi-heart"></i> <span class="likes-count">${likesCount}</span></div>\
                  </div>\
                  <h3 class="post-title">${post.title}</h3>\
                  <p class="post-desc">${post.description ? post.description : ''}</p>\
                </div>\
              </article>`;
          }).join('') + '</div>';
        })
        .catch(err => {
          console.error('Error cargando recetas de usuario:', err);
          userPostsDiv.innerHTML = '<p>Error cargando recetas.</p>';
        });
    }
  }
});
