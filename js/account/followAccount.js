document.addEventListener("DOMContentLoaded", () => {
    // Obtiene la referencia al botón de seguir
    followBtn = document.getElementById("followBtn");
    
    // Obtiene el ID del usuario al que se está siguiendo desde el atributo del contenedor
    const followingUserId = document.querySelector('.follow-stats').getAttribute('data-following-user-id');

    // Carga el estado inicial de seguimiento desde el servidor
    loadFollowState(followingUserId);

    // Configura el evento para alternar el seguimiento
    setupFollowToggle(followingUserId);
});

// Carga el estado actual de seguimiento y las estadísticas de seguidores/seguidos
function loadFollowState(followingUserId) {
    fetch(`../getFollow.php?followingUserId=${followingUserId}`)
        .then((res) => res.json())
        .then((data) => {
            // Si hay un error en la respuesta, no continúa
            if (data.error) return;

            // Actualiza los contadores de seguidores y seguidos
            const followers = data.followersCount || 0;
            const following = data.followingCount || 0;
            document.querySelector('.followers-count').textContent = followers;
            document.querySelector('.following-count').textContent = following;

            // Actualiza el estado visual del botón (Seguir / Dejar de seguir)
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

// Configura el evento click del botón para alternar el estado de seguimiento
function setupFollowToggle(followingUserId) {
    if (!followBtn) return;

    followBtn.addEventListener("click", () => {
        // Prepara los datos del usuario a seguir o dejar de seguir
        const form = new URLSearchParams();
        form.append("followingUserId", followingUserId);

        // Envía la solicitud para alternar el seguimiento en el servidor
        fetch("../toggleFollow.php", {
            method: "POST",
            body: form.toString(),
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
            .then((res) => res.json())
            .then((result) => {
                // Vuelve a cargar el estado desde el servidor para mantener sincronía visual
                loadFollowState(followingUserId);
            })
            .catch((err) => {
                console.error("Error al alternar seguimiento:", err);
            });
    });
}
