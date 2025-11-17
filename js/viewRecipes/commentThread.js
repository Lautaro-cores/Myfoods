// commentThread.js
// este archivo maneja la carga y el envío de comentarios hijos en un hilo de comentarios

// importa las funciones necesarias desde comments.js
import { createCommentHtml, setupCommentHandlers } from './comments.js';

document.addEventListener("DOMContentLoaded", () => {

    const script = document.querySelector('script[data-comment-id]');
    const commentId = script.getAttribute('data-comment-id');
    const postId = script.getAttribute('data-post-id');
    const defaultImageUrl = script.getAttribute('data-default-image-url');
    
    console.log('Datos cargados:', { commentId, postId, defaultImageUrl });
    
    if (!commentId || !postId) {
        console.error('Faltan datos requeridos:', { commentId, postId });
        return;
    }

    // se cargan los comentarios hijos de este comentario
    loadCommentChildren(commentId, postId, defaultImageUrl);
    
    // se configura el formulario de comentario principal
    setupMainCommentForm(postId, defaultImageUrl);
    
    // se configuran los likes 
    setupCommentHandlers(postId, defaultImageUrl);
});

// funcion para cargar los comentarios hijos de un comentario dado
function loadCommentChildren(commentId, postId, defaultImageUrl) {
    console.log('Cargando comentarios hijos para:', { commentId, postId, defaultImageUrl });
    
    const commentsContainer = document.getElementById("commentsContainer");
    if (!commentsContainer) {
        console.error('No se encontró el contenedor de comentarios');
        return;
    }
    // hace la consulta al getCommentReplies.php para obtener los comentarios hijos
    fetch(`../getCommentReplies.php?commentId=${commentId}`)
        .then(response => {
            if (!response.ok) throw new Error("Error en el servidor al obtener comentarios hijos.");
            return response.json();
        })
        .then(comments => {
            console.log('Comentarios hijos recibidos:', comments);
            
            if (comments.error) {
                console.error('Error del servidor:', comments.error);
                commentsContainer.innerHTML = `<p class="error-message">Error al cargar comentarios hijos: ${comments.error}</p>`;
                return;
            }

            if (!comments || comments.length === 0) {
                commentsContainer.innerHTML = '<p class="text-muted">No hay comentarios aún. ¡Sé el primero en comentar!</p>';
            } else {
                console.log('Renderizando comentarios hijos:', comments.length);
                commentsContainer.innerHTML = comments
                    .map((comment) => createCommentHtml(comment, defaultImageUrl, true, postId))
                    .join("");
                
                // configura los handlers para los comentarios cargados
                setupCommentHandlers(postId, defaultImageUrl);
            }
        })
        .catch(error => {
            console.error("Error al cargar los comentarios hijos:", error);
            commentsContainer.innerHTML = '<p class="error-message">No se pudieron cargar los comentarios hijos. Inténtalo más tarde.</p>';
        });
}

// funcon para configurar el formulario de comentario principal
function setupMainCommentForm(postId, defaultImageUrl) {
    const commentForm = document.getElementById("replyToMainForm");
    const commentMessage = document.getElementById("replyMessage");

    if (commentForm) {
        commentForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const formData = new FormData(commentForm);
            const contentValue = formData.get("content").trim();

            if (contentValue === "") {
                commentMessage.textContent = "El comentario no puede estar vacío.";
                commentMessage.className = "alert alert-danger";
                return;
            }
            
            commentMessage.textContent = "Publicando...";
            commentMessage.className = "alert alert-info";
            // envia al postComment.php para publicar el comentario
            fetch("../postComment.php", {
                method: "POST",
                body: formData, 
            })
                .then((response) => response.json())
                .then((result) => {
                    
                    commentMessage.textContent = result.msj;
                    commentMessage.className = result.success ? "alert alert-success" : "alert alert-danger";

                    if (result.success) {
                        commentForm.reset();
                        // limpiar vista previa de imágenes
                        const imagePreview = document.getElementById('replyImagePreview');
                        if (imagePreview) {
                            imagePreview.innerHTML = '';
                        }
                        // limpiar input de archivos
                        const imageInput = document.getElementById('replyImages');
                        if (imageInput) {
                            imageInput.value = '';
                        }
                        // recargar comentarios hijos
                        const commentId = formData.get('parentId');
                        loadCommentChildren(commentId, postId, defaultImageUrl);
                        // actualizar contador de comentarios
                        updateCommentCount();
                    }
                })
                .catch((error) => {
                    console.error("Error al enviar el comentario:", error);
                    commentMessage.textContent = "Error de conexión al enviar el comentario.";
                    commentMessage.className = "alert alert-danger";
                });
        });
    }
}


// funcion para actualizar el contador de comentarios en la interfaz
function updateCommentCount() {
    const commentCountElement = document.querySelector('.comment-count');
    if (commentCountElement) {
        const commentsContainer = document.getElementById("commentsContainer");
        const commentElements = commentsContainer.querySelectorAll('.comment');
        const count = commentElements.length;
        commentCountElement.innerHTML = `<i class="bi bi-chat"></i> ${count} comentarios`;
    }
}