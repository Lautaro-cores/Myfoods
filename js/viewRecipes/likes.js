// js/like_handler.js

const likesCountEl = document.getElementById("likesCount");
const likeBtn = document.getElementById("likeBtn");

// 1. Función de Carga de Likes (Obtiene el estado actual)
export function loadLikes(postId) {
    if (!likesCountEl || !likeBtn) return;
    
    fetch(`../getLikes.php?postId=${postId}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.error) return;
            
            likesCountEl.textContent = data.likesCount || 0;
            if (data.userLiked) {
                likeBtn.classList.add("liked");
                likeBtn.innerHTML = '<i class="bi bi-heart-fill"></i>';
            } else {
                likeBtn.classList.remove("liked");
                likeBtn.innerHTML = '<i class="bi bi-heart"></i>';
            }
        })
        .catch((err) => console.error("Error cargando likes:", err));
}

// 2. Setup del Evento para el botón
export function setupLikeToggle(postId) {
    if (!likeBtn) return;
    
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
                if (result.success) loadLikes(postId); // Recarga el estado después del toggle
                else alert(result.msj || "No se pudo actualizar el like.");
            })
            .catch((err) => console.error("Error al alternar like:", err));
    });
}