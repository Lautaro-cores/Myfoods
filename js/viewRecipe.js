// viewRecipe.js
// Funciones relacionadas con la vista de una receta: cargar comentarios, likes y enviar comentarios.
// Este archivo asume que los endpoints PHP están en el directorio padre (../).

// Construye el HTML de un comentario dado un objeto comment.
// Parámetros:
// - comment: objeto con campos userName, content, userImage (base64 opcional)
// - defaultImageUrl: URL de la imagen por defecto cuando no hay userImage
function createCommentHtml(comment, defaultImageUrl) {
  const imageUrl = comment.userImage
    ? `data:image/jpeg;base64,${comment.userImage}`
    : defaultImageUrl;

  return `
        <div class="comment-item">
            <img src="${imageUrl}" alt="Perfil de ${comment.userName}" 
                 class="comment-user-image" 
                 style="width: 40px; height: 40px;"> 
            <div class="comment-body">
                <strong class="comment-username">${comment.userName}</strong>
                <p class="comment-content">${comment.content}</p>
            </div>
        </div>
    `;
}

// Carga los comentarios para un postId y los renderiza en el contenedor #commentsContainer.
// Maneja errores de red y respuestas vacías.
function loadComments(postId, defaultImageUrl) {
  const commentsContainer = document.getElementById("commentsContainer");
  commentsContainer.innerHTML = '<p class="loading-message">Cargando comentarios...</p>';

  fetch(`../getComment.php?postId=${postId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en el servidor al obtener comentarios.");
      }
      return response.json();
    })
    .then((comments) => {
      if (comments.error) {
        commentsContainer.innerHTML = `<p class="error-message">Error al cargar comentarios: ${comments.error}</p>`;
        return;
      }

      if (!comments || comments.length === 0) {
        commentsContainer.innerHTML = '<p class="no-comments-message">Sé el primero en comentar esta receta.</p>';
      } else {
        commentsContainer.innerHTML = comments.map((comment) => createCommentHtml(comment, defaultImageUrl)).join("");
      }
    })
    .catch((error) => {
      console.error("Error al cargar los comentarios:", error);
      commentsContainer.innerHTML = '<p class="error-message">No se pudieron cargar los comentarios. Asegúrate de que "getComment.php" esté funcionando.</p>';
    });
}

// Inicialización: obtiene postId desde la URL, carga comentarios y likes, y configura handlers.
document.addEventListener("DOMContentLoaded", () => {
  const scriptTag = Array.from(document.getElementsByTagName("script")).find((s) => s.src && s.src.includes("viewRecipe.js"));
  const defaultImageUrl = scriptTag ? scriptTag.getAttribute("data-default-image-url") : "img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg";

  const urlParams = new URLSearchParams(window.location.search);
  const postId = urlParams.get("id");

  if (!postId) return; // No hay receta seleccionada

  // Cargar comentarios y likes inicialmente
  loadComments(postId, defaultImageUrl);

  const likesCountEl = document.getElementById("likesCount");
  const likeBtn = document.getElementById("likeBtn");

  // Carga el estado de likes para este post
  function loadLikes() {
    fetch(`../getLikes.php?postId=${postId}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.error) return;
        likesCountEl.textContent = data.likesCount || 0;
        if (data.userLiked) {
          likeBtn.classList.add("liked");
          likeBtn.textContent = "💔 Quitar like";
        } else {
          likeBtn.classList.remove("liked");
          likeBtn.textContent = "❤ Me gusta";
        }
      })
      .catch((err) => console.error("Error cargando likes:", err));
  }

  loadLikes();

  // Handler para alternar like
  if (likeBtn) {
    likeBtn.addEventListener("click", () => {
      const form = new URLSearchParams();
      form.append("postId", postId);

      fetch("../toggleLike.php", {
        method: "POST",
        body: form.toString(),
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
      })
        .then((res) => res.json())
        .then((result) => {
          if (result.success) loadLikes();
          else alert(result.msj || "No se pudo actualizar el like.");
        })
        .catch((err) => console.error("Error al alternar like:", err));
    });
  }

  // Manejo del formulario de comentarios: validación básica, envío y recarga de la lista
  const commentForm = document.getElementById("commentForm");
  const commentContent = document.getElementById("commentContent");
  const commentMessage = document.getElementById("commentMessage");

  if (commentForm) {
    commentForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const formData = new FormData(commentForm);
      const contentValue = formData.get("content").trim();

      if (contentValue === "") {
        commentMessage.textContent = "El comentario no puede estar vacío.";
        commentMessage.className = "error-message";
        return;
      }

      commentMessage.textContent = "Publicando...";
      commentMessage.className = "info-message";

      fetch("../postComment.php", {
        method: "POST",
        body: new URLSearchParams(formData).toString(),
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
      })
        .then((response) => response.json())
        .then((result) => {
          commentMessage.textContent = result.msj;
          commentMessage.className = result.success ? "success-message" : "error-message";

          if (result.success) {
            commentContent.value = "";
            loadComments(postId, defaultImageUrl);
          }
        })
        .catch((error) => {
          console.error("Error al enviar el comentario:", error);
          commentMessage.textContent = "Error de conexión al enviar el comentario.";
          commentMessage.className = "error-message";
        });
    });
  }
});
