document.addEventListener("DOMContentLoaded", async () => {
    const listContainer = document.getElementById("followingList");
    if (!listContainer) return;
    listContainer.innerHTML = '';

    const params = new URLSearchParams(window.location.search);
    const username = params.get('username');
    if (!username) {
        listContainer.innerHTML = '<div class="alert alert-warning">No se proporcionó el nombre de usuario en la URL.</div>';
        return;
    }

    // determina el tipo de lista según la página actual (siguiendo o seguidores)
    const path = window.location.pathname || '';
    const isFollowingPage = path.toLowerCase().endsWith('following.php');
    const isFollowersPage = path.toLowerCase().endsWith('followers.php');

    const mode = isFollowingPage ? 'following' : 'followers';
    const endpoint = isFollowingPage ? '../getFollowingList.php' : '../getFollowersList.php';

    try {
        // Solicita al servidor la lista correspondiente según el modo
        const res = await fetch(`${endpoint}?username=${encodeURIComponent(username)}`);
        const data = await res.json();

        // Verifica si la respuesta fue exitosa antes de continuar
        if (!data.success) {
            listContainer.innerHTML = `<div class="alert alert-danger">${data.msj || data.error || 'Error al obtener datos'}</div>`;
            return;
        }

        // Obtiene el conjunto de usuarios dependiendo del tipo de página
        const items = mode === 'following' ? (data.following || []) : (data.followers || []);

        // Si no hay resultados, muestra un mensaje apropiado
        if (!Array.isArray(items) || items.length === 0) {
            listContainer.innerHTML = mode === 'following' ? '<p>No sigue a nadie todavía.</p>' : '<p>No tiene seguidores todavía.</p>';
            return;
        }

        // Crea el contenedor visual donde se mostrarán los usuarios
        const grid = document.createElement('div');
        grid.className = 'followers-grid';

        // Recorre cada usuario y construye los elementos visuales con su información
        items.forEach(f => {
            const div = document.createElement('div');
            div.className = 'follower-item';

            // Define la imagen de perfil, usa una por defecto si el usuario no tiene
            const imgSrc = f.userImage ? `data:image/jpeg;base64,${f.userImage}` : '../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';

            div.innerHTML = `
                <div class="follower-avatar"><img src="${imgSrc}" alt="${f.userName}" width="64" height="64"></div>
                <div class="follower-info">
                    <a href="../visual/account.php?username=${encodeURIComponent(f.userName)}">${f.userName}</a>
                    <div class="follower-desc">${f.description ? f.description : ''}</div>
                </div>
            `;

            grid.appendChild(div);
        });

        // Inserta la grilla completa en el contenedor de la página
        listContainer.appendChild(grid);

    } catch (err) {
        // Captura errores de red o respuesta no válida del servidor
        console.error('Error al obtener la lista:', err);
        listContainer.innerHTML = '<div class="alert alert-danger">Error al comunicarse con el servidor.</div>';
    }
});
