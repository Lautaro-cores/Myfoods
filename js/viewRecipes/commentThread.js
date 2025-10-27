// js/comment_thread.js

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

    // Cargar comentarios hijos del comentario principal
    loadCommentChildren(commentId, postId, defaultImageUrl);
    
    // Configurar formulario de comentario principal
    setupMainCommentForm(postId, defaultImageUrl);
    
    // Configurar likes (setupCommentHandlers ya maneja los likes)
    setupCommentHandlers(postId, defaultImageUrl);
});

// Función para cargar comentarios hijos de un comentario específico
function loadCommentChildren(commentId, postId, defaultImageUrl) {
    console.log('Cargando comentarios hijos para:', { commentId, postId, defaultImageUrl });
    
    const commentsContainer = document.getElementById("commentsContainer");
    if (!commentsContainer) {
        console.error('No se encontró el contenedor de comentarios');
        return;
    }

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
                
                // Configurar handlers después de renderizar
                setupCommentHandlers(postId, defaultImageUrl);
            }
        })
        .catch(error => {
            console.error("Error al cargar los comentarios hijos:", error);
            commentsContainer.innerHTML = '<p class="error-message">No se pudieron cargar los comentarios hijos. Inténtalo más tarde.</p>';
        });
}

// Función para configurar el formulario de comentario principal
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

            fetch("../postComment.php", {
                method: "POST",
                body: formData, // Usar FormData directamente para enviar archivos
            })
                .then((response) => response.json())
                .then((result) => {
                    commentMessage.textContent = result.msj;
                    commentMessage.className = result.success ? "alert alert-success" : "alert alert-danger";

                    if (result.success) {
                        commentForm.reset();
                        // Limpiar vista previa de imágenes
                        const imagePreview = document.getElementById('replyImagePreview');
                        if (imagePreview) {
                            imagePreview.innerHTML = '';
                        }
                        // Limpiar input de archivos
                        const imageInput = document.getElementById('replyImages');
                        if (imageInput) {
                            imageInput.value = '';
                        }
                        // Recargar comentarios hijos
                        const commentId = formData.get('parentId');
                        loadCommentChildren(commentId, postId, defaultImageUrl);
                        // Actualizar contador de comentarios
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


// Función para actualizar el contador de comentarios
function updateCommentCount() {
    const commentCountElement = document.querySelector('.comment-count');
    if (commentCountElement) {
        const commentsContainer = document.getElementById("commentsContainer");
        const commentElements = commentsContainer.querySelectorAll('.comment');
        const count = commentElements.length;
        commentCountElement.innerHTML = `<i class="bi bi-chat"></i> ${count} comentarios`;
    }
}