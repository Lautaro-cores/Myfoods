// account.js
// Gestiona la subida de la imagen de perfil desde la vista de cuenta.
// Reemplaza la imagen mostrada en la página con la nueva URL devuelta por el servidor.

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formImage");
  const userImageInput = document.getElementById("subirArchivo");
  const profileImage = document.querySelector('img[alt="Imagen de perfil"]');
  const editProfileModalEl = document.getElementById('editProfileModal');
  const editProfileModal = new bootstrap.Modal(editProfileModalEl);
  const imagePreview = document.getElementById('imagePreview');
  const descriptionField = document.getElementById('description');
  if (!form) return;

  // Preview handler (outside submit) - immediate preview when file selected
  if (userImageInput && imagePreview) {
    userImageInput.addEventListener('change', () => {
      imagePreview.innerHTML = '';
      const file = userImageInput.files && userImageInput.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.alt = 'Vista previa';
        img.style.maxWidth = '200px';
        img.style.borderRadius = '8px';
        imagePreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }

  // When modal is hidden, clear preview & file input
  editProfileModalEl.addEventListener('hidden.bs.modal', () => {
    if (imagePreview) imagePreview.innerHTML = '';
    if (userImageInput) userImageInput.value = '';
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    // Ensure description is appended even if empty string
    formData.set('description', descriptionField ? descriptionField.value.trim() : '');

    try {
      const response = await fetch("../upload.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        // Actualizar imagen si viene imageUrl o forzar recarga del endpoint que da la imagen
        if (result.imageUrl) {
          profileImage.src = result.imageUrl;
        } else {
          // add cache-busting
          profileImage.src = "../getUserImage.php?ts=" + new Date().getTime();
        }

        // Actualizar descripción sin recargar
        const descriptionElement = document.querySelector(".user-description");
        const newDescription = descriptionField ? descriptionField.value.trim() : '';
        if (descriptionElement) {
          descriptionElement.textContent = newDescription !== "" ? newDescription : "Sin descripción";
        }

        editProfileModal.hide();
        // Mostrar mensaje de éxito (puedes reemplazar por un toast)
        alert(result.msj || 'Perfil actualizado');
      } else {
        alert(result.msj || 'Error al actualizar perfil');
      }
    } catch (err) {
      console.error('Error en fetch upload:', err);
      alert('Error al comunicarse con el servidor.');
    }
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
