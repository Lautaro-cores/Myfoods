// js/comment_handler.js

// 1. Renderizado de un único comentario
export function createCommentHtml(comment, defaultImageUrl) {
    const imageUrl = comment.userImage
        ? `data:image/jpeg;base64,${comment.userImage}`
        : defaultImageUrl;

    return `
        <div class="comment">
            <img src="${imageUrl}" alt="Perfil de ${comment.userName}">
            <div class="comment-body">
                <strong class="comment-username">${comment.userName}</strong>
                <p class="comment-content">${comment.content}</p>
            </div>
        </div>
    `;
}

// 2. Función de Carga de Comentarios
export function loadComments(postId, defaultImageUrl) {
    const commentsContainer = document.getElementById("commentsContainer");
    if (!commentsContainer) return;

    commentsContainer.innerHTML = '<p class="loading-message">Cargando comentarios...</p>';

    fetch(`../getComment.php?postId=${postId}`)
        .then((response) => {
            if (!response.ok) throw new Error("Error en el servidor al obtener comentarios.");
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
                commentsContainer.innerHTML = comments
                    .map((comment) => createCommentHtml(comment, defaultImageUrl))
                    .join("");
            }
        })
        .catch((error) => {
            console.error("Error al cargar los comentarios:", error);
            commentsContainer.innerHTML = '<p class="error-message">No se pudieron cargar los comentarios. Inténtalo más tarde.</p>';
        });
}

// 3. Setup del Formulario de Comentarios (Si se usa 'type="module"' en el HTML, esto puede ir en init)
// Para simplificar, lo dejamos en un solo archivo, pero envuelto para ser llamado.
export function setupCommentForm(postId, defaultImageUrl) {
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
                        loadComments(postId, defaultImageUrl); // Recarga la lista
                    }
                })
                .catch((error) => {
                    console.error("Error al enviar el comentario:", error);
                    commentMessage.textContent = "Error de conexión al enviar el comentario.";
                    commentMessage.className = "error-message";
                });
        });
    }
}