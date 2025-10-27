// js/comment_handler.js

// 1. Renderizado de un único comentario con soporte para comentarios hijos
export function createCommentHtml(comment, defaultImageUrl, isChild = false, postId = null) {
    const imageUrl = comment.userImage
        ? `data:image/jpeg;base64,${comment.userImage}`
        : defaultImageUrl;

  
    
    // Botón para ver comentarios hijos si los tiene (todos los comentarios tienen este botón)
    const viewCommentsButton = `<a href="commentThread.php?commentId=${comment.commentId}" class="author-link   me-2">
         <i class="bi bi-chat"></i>${comment.childCount && comment.childCount > 0 ? ` (${comment.childCount})` : ''}
       </a>`;
    
    // Botón de like para cada comentario
    // Usar la misma lógica que los likes de las recetas
    const likeButton = `<button class="comment-like-btn ${comment.userLiked ? 'liked' : ''}" data-comment-id="${comment.commentId}">
        <i class="bi ${comment.userLiked ? 'bi-heart-fill' : 'bi-heart'}"></i>
        <span class="like-count">${comment.likeCount || 0}</span>
    </button>`;
    
    // Usar el postId pasado como parámetro o el del comentario
    const currentPostId = postId || comment.postId;

    return `
        <div class="comment " data-comment-id="${comment.commentId}">
            <img class="author-image" src="${imageUrl}" alt="Perfil de ${comment.userName}">
             <div class="comment-body">
                 <strong class="comment-username">${comment.userName}</strong>
                 <p class="comment-content">${comment.content}</p>
                 
                 <!-- Mostrar imágenes del comentario -->
                 ${comment.images && comment.images.length > 0 ? `
                     <div class="comment-images mt-2">
                         ${comment.images.map(image => `
                             <img src="data:${image.imageType};base64,${image.imageData}" 
                                  alt="Imagen del comentario" 
                                  class="comment-image img-thumbnail me-2 mb-2" 
                                  style="max-width: 150px; max-height: 150px; cursor: pointer;"
                                  onclick="openImageModal('data:${image.imageType};base64,${image.imageData}')">
                         `).join('')}
                     </div>
                 ` : ''}
                 
                 <div class="comment-actions">
                     ${likeButton}
                     ${viewCommentsButton}
                 </div>
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
                    .map((comment) => createCommentHtml(comment, defaultImageUrl, false, postId))
                    .join("");
                
                // IMPORTANTE: Configurar handlers después de renderizar comentarios
                setupCommentHandlers(postId, defaultImageUrl);
            }
        })
        .catch((error) => {
            console.error("Error al cargar los comentarios:", error);
            commentsContainer.innerHTML = '<p class="error-message">No se pudieron cargar los comentarios. Inténtalo más tarde.</p>';
        });
}

// 3. Función para manejar likes en comentarios
let commentHandlersSetup = false;

// Función para configurar handlers de comentarios (botones de responder y likes)
export function setupCommentHandlers(postId, defaultImageUrl) {
    // Solo configurar una vez a nivel global
    if (commentHandlersSetup) {
        console.log('Handlers ya configurados, saltando...');
        return;
    }
    commentHandlersSetup = true;
    
    console.log('Configurando handlers de comentarios');
    
    // Delegación de eventos para likes a nivel de document (solo una vez)
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('comment-like-btn') || e.target.closest('.comment-like-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const likeBtn = e.target.closest('.comment-like-btn');
            if (likeBtn) {
                const commentId = likeBtn.getAttribute('data-comment-id');
                console.log('Like clicked:', commentId);
                handleCommentLike(commentId, likeBtn);
            }
        }
    });
}

// 4. Función para manejar likes de comentarios (exportada para uso en otras páginas)
export function handleCommentLike(commentId, likeBtn) {
    const formData = new FormData();
    formData.append('commentId', commentId);
    
    fetch("../toggleCommentLike.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Actualizar el botón visualmente - misma lógica que likes de recetas
            const icon = likeBtn.querySelector('i');
            const countSpan = likeBtn.querySelector('.like-count');
            
            // Actualizar contador
            countSpan.textContent = result.likeCount;
            
            // Cambiar entre corazón lleno y vacío según el estado
            if (result.liked) {
                likeBtn.classList.add("liked");
                icon.className = 'bi bi-heart-fill';
            } else {
                likeBtn.classList.remove("liked");
                icon.className = 'bi bi-heart';
            }
        } else {
            console.error('Error al dar like:', result.msj);
        }
    })
    .catch(error => {
        console.error('Error de conexión:', error);
    });
}

// 5. Setup del Formulario de Comentarios
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
                body: formData, // Usar FormData directamente para enviar archivos
            })
                .then((response) => response.json())
                .then((result) => {
                    commentMessage.textContent = result.msj;
                    commentMessage.className = result.success ? "success-message" : "error-message";

                    if (result.success) {
                        commentContent.value = "";
                        // Limpiar vista previa de imágenes
                        const imagePreview = document.getElementById('imagePreview');
                        if (imagePreview) {
                            imagePreview.innerHTML = '';
                        }
                        // Limpiar input de archivos
                        const imageInput = document.getElementById('commentImages');
                        if (imageInput) {
                            imageInput.value = '';
                        }
                        loadComments(postId, defaultImageUrl); // Recarga la lista
                        
                        // Disparar evento para recargar puntuación
                        window.dispatchEvent(new CustomEvent('commentPosted'));
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

// Función global para abrir imágenes en modal
window.openImageModal = function(imageSrc) {
    // Crear modal si no existe
    let modal = document.getElementById('imageModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Imagen del comentario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" class="img-fluid" alt="Imagen ampliada">
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Configurar la imagen
    document.getElementById('modalImage').src = imageSrc;
    
    // Mostrar modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
};

// Función global para vista previa de imágenes de comentarios
window.previewCommentImages = function(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        const maxFiles = Math.min(input.files.length, 3);
        
        for (let i = 0; i < maxFiles; i++) {
            const file = input.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail me-1 mb-1';
                img.style.maxWidth = '80px';
                img.style.maxHeight = '80px';
                preview.appendChild(img);
            };
            
            reader.readAsDataURL(file);
        }
        
        if (input.files.length > 3) {
            const warning = document.createElement('div');
            warning.className = 'text-warning small';
            warning.textContent = 'Solo las primeras 3 imágenes se mostrarán.';
            preview.appendChild(warning);
        }
    }
};