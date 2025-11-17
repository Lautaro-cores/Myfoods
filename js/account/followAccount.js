//followAccount.js
// este archivo maneja la funcionalidad de seguir y dejar de seguir a otros usuarios


document.addEventListener("DOMContentLoaded", () => {
    followBtn = document.getElementById("followBtn");

    // obtiene el ID del usuario al que se está siguiendo desde el atributo del contenedor
    const followingUserId = document.querySelector('.follow-stats').getAttribute('data-following-user-id');

    // carga el estado inicial de seguimiento desde el servidor
    loadFollowState(followingUserId);

    // configura el evento para alternar el seguimiento
    setupFollowToggle(followingUserId);
});

// funcion que carga el estado actual de seguimiento 
function loadFollowState(followingUserId) {
    // consulta al getFollow.php para saber el estado de seguimiento
    fetch(`../getFollow.php?followingUserId=${followingUserId}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.error) return;

            // actualiza los contadores de seguidores y seguidos
            const followers = data.followersCount || 0;
            const following = data.followingCount || 0;
            document.querySelector('.followers-count').textContent = followers;
            document.querySelector('.following-count').textContent = following;

            // actualiza el estado visual del botón (Seguir / Dejar de seguir)
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

// funcion que configura el evento click del botón para alternar el estado de seguimiento
function setupFollowToggle(followingUserId) {
    if (!followBtn) return;

    followBtn.addEventListener("click", () => {
        // prepara los datos del usuario a seguir o dejar de seguir
        const form = new URLSearchParams();
        form.append("followingUserId", followingUserId);

        // envía la solicitud para alternar el seguimiento al toggleFollow.php
        fetch("../toggleFollow.php", {
            method: "POST",
            body: form.toString(),
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
            .then((res) => res.json())
            .then((result) => {
                // vuelve a cargar el estado de seguimiento
                loadFollowState(followingUserId);
            })
            .catch((err) => {
                console.error("Error al alternar seguimiento:", err);
            });
    });
}
