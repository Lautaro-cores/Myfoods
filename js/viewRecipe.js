// Función para construir el HTML de un comentario.
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

// Función que se encarga de cargar y mostrar los comentarios.
function loadComments(postId, defaultImageUrl) {
    const commentsContainer = document.getElementById('commentsContainer');
    // Usa una clase para el mensaje de carga
    commentsContainer.innerHTML = '<p class="loading-message">Cargando comentarios...</p>'; 

    // *** CORRECCIÓN CLAVE ***: Se usa 'getComment.php' (singular) y sin '../'
    fetch(`getComment.php?postId=${postId}`) 
        .then(response => {
            // Añadir una comprobación de error de red o servidor antes de intentar JSON
            if (!response.ok) {
                // Si el servidor devuelve 404, 500, etc., se lanza un error
                throw new Error('Error en el servidor al obtener comentarios.');
            }
            return response.json();
        })
        .then(comments => {
            if (comments.error) {
                // Usa una clase para el mensaje de error
                commentsContainer.innerHTML = `<p class="error-message">Error al cargar comentarios: ${comments.error}</p>`;
                return;
            }

            if (comments.length === 0) {
                commentsContainer.innerHTML = '<p class="no-comments-message">Sé el primero en comentar esta receta.</p>';
            } else {
                commentsContainer.innerHTML = comments.map(comment => {
                    return createCommentHtml(comment, defaultImageUrl);
                }).join('');
            }
        })
        .catch(error => {
            console.error('Error al cargar los comentarios:', error);
            // Mensaje de error general si falla la conexión o el formato JSON
            commentsContainer.innerHTML = '<p class="error-message">No se pudieron cargar los comentarios. Asegúrate de que "getComment.php" esté funcionando.</p>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    // Obtener la URL de la imagen por defecto del atributo data-
    const scriptTag = document.querySelector('script[src*="viewRecipe.js"]');
    const defaultImageUrl = scriptTag ? scriptTag.getAttribute('data-default-image-url') : 'img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
    
    // Obtener el postId de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get('id');

    if (postId) {
        // Cargar los comentarios al inicio
        loadComments(postId, defaultImageUrl);

        const commentForm = document.getElementById('commentForm');
        const commentContent = document.getElementById('commentContent');
        const commentMessage = document.getElementById('commentMessage');

        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const formData = new FormData(commentForm);
                const contentValue = formData.get('content').trim();

                // Validación simple
                if (contentValue === "") {
                    commentMessage.textContent = 'El comentario no puede estar vacío.';
                    commentMessage.className = 'error-message';
                    return;
                }
                
                // Mensaje de estado mientras se publica
                commentMessage.textContent = 'Publicando...';
                commentMessage.className = 'info-message';
                
                // *** CORRECCIÓN CLAVE ***: Se usa 'postComment.php' sin '../'
                fetch('postComment.php', { 
                    method: 'POST',
                    body: new URLSearchParams(formData).toString(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    commentMessage.textContent = result.msj;
                    // Asigna una clase diferente según si fue exitoso o fallido
                    commentMessage.className = result.success ? 'success-message' : 'error-message';
                    
                    if (result.success) {
                        commentContent.value = ''; // Limpiar el textarea
                        loadComments(postId, defaultImageUrl); // Recargar la lista
                    }
                })
                .catch(error => {
                    console.error('Error al enviar el comentario:', error);
                    commentMessage.textContent = 'Error de conexión al enviar el comentario.';
                    commentMessage.className = 'error-message';
                });
            });
        }
    }
});