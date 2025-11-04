// js/followAccount.js
// 1. Función de Carga de Estado de Seguimiento
function loadFollowState(followingUserId) {

    
    fetch(`../getFollow.php?followingUserId=${followingUserId}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.error) return;
            const followers = data.followersCount || 0;
            const following = data.followingCount || 0;
            document.querySelector('.followers-count').textContent = followers;
            document.querySelector('.following-count').textContent = following;
            if (!followBtn) return;
            if (data.isFollowing) {
                followBtn.classList.add("following");
                followBtn.textContent = "Dejar de seguir";
            } else {
                followBtn.classList.remove("following");
                followBtn.textContent = "Seguir";
            }
        })
    .catch((err) => console.error("Error al cargar estado de seguimiento:", err));
}
// 2. Setup del Evento para el botón
function setupFollowToggle(followingUserId) {
    if (!followBtn) return;

    followBtn.addEventListener("click", () => {
        const form = new URLSearchParams();
        form.append("followingUserId", followingUserId);

        fetch("../toggleFollow.php", {
            method: "POST",
            body: form.toString(),
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
            .then((res) => res.json())
            .then((result) => {
                // refrescar el estado real desde el servidor para evitar inconsistencias
                loadFollowState(followingUserId);
            })
            .catch((err) => {
                console.error("Error al alternar seguimiento:", err);
            });
    });
}
// Inicialización al cargar el DOM
document.addEventListener("DOMContentLoaded", () => {
    followBtn = document.getElementById("followBtn");
    

    const followingUserId = document.querySelector('.follow-stats').getAttribute('data-following-user-id');

    loadFollowState(followingUserId);
    setupFollowToggle(followingUserId);
});