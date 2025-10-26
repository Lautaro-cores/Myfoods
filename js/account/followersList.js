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

    // Detect whether this page is the followers or following page
    const path = window.location.pathname || '';
    const isFollowingPage = path.toLowerCase().endsWith('following.php');
    const isFollowersPage = path.toLowerCase().endsWith('followers.php');

    const mode = isFollowingPage ? 'following' : 'followers';
    const endpoint = isFollowingPage ? '../getFollowingList.php' : '../getFollowersList.php';

    try {
        const res = await fetch(`${endpoint}?username=${encodeURIComponent(username)}`);
        const data = await res.json();

        if (!data.success) {
            listContainer.innerHTML = `<div class="alert alert-danger">${data.msj || data.error || 'Error al obtener datos'}</div>`;
            return;
        }

        const items = mode === 'following' ? (data.following || []) : (data.followers || []);

        if (!Array.isArray(items) || items.length === 0) {
            listContainer.innerHTML = mode === 'following' ? '<p>No sigue a nadie todavía.</p>' : '<p>No tiene seguidores todavía.</p>';
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'followers-grid';

        items.forEach(f => {
            const div = document.createElement('div');
            div.className = 'follower-item';

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

        listContainer.appendChild(grid);

    } catch (err) {
        console.error('Error fetching list:', err);
        listContainer.innerHTML = '<div class="alert alert-danger">Error al comunicarse con el servidor.</div>';
    }
});