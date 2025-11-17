// likes.js
// este archivo maneja la carga y el toggle de likes para una receta

// obtiene los elementos del DOM de los likes
const likesCountEl = document.getElementById("likesCount");
const likeBtn = document.getElementById("likeBtn");

// 1. funcion para cargar los likes del post
export function loadLikes(postId) {
    if (!likesCountEl || !likeBtn) return;
    // hace la consulta al getLikes.php para obtener el conteo de likes y el estado del like del usuario
    fetch(`../getLikes.php?postId=${postId}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.error) return;
            // actualiza el conteo de likes y el estado del botón de like
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

// 2. funcion para configurar el toggle de like
export function setupLikeToggle(postId) {
    if (!likeBtn) return;
    // agrega el evento click al boton de like
    likeBtn.addEventListener("click", () => {
        const form = new URLSearchParams();
        form.append("postId", postId);
        // envía la solicitud para alternar el like al toggleLike.php
        fetch("../toggleLike.php", {
            method: "POST",
            body: form.toString(),
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
            .then((res) => res.json())
            .then((result) => {
                if (result.success) loadLikes(postId); // recarga el estado después del toggle
                else alert(result.msj || "No se pudo actualizar el like.");
            })
            .catch((err) => console.error("Error al alternar like:", err));
    });
}