//userPost.js
// este archivo maneja la carga y visualización de las publicaciones de un usuario


document.addEventListener("DOMContentLoaded", () => {
    const userPostsDiv = document.getElementById('userPosts');
    if (!userPostsDiv) return;

    // funcion que crea la estructura de los post del usuario
    const createPostCard = (post) => {
        const avatarUrl = post.userImage 
            ? `data:image/jpeg;base64,${post.userImage}` 
            : '../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg';
            
        const firstImg = (post.images && post.images.length > 0) 
            ? `data:image/jpeg;base64,${post.images[0]}` 
            : '';
            
        const likesCount = post.likesCount || 0;
        const heartIconClass = post.userLiked ? "bi bi-heart-fill" : "bi bi-heart";

        return `
            <article class="post-card">
                <div class="post-image">
                    ${ firstImg ? `<img src="${firstImg}" class="d-block w-100" alt="Imagen de ${post.title}">` : '' }
                </div>
                <div class="post-content" onclick="location.href='viewRecipe.php?id=${post.postId}'">
                    <div class="post-header">
                        <div class="post-left">
                            <img class="post-avatar" src="${avatarUrl}" alt="Avatar ${post.displayName}">
                            <div class="post-meta">
                                <div class="post-author">${post.displayName}</div>
                                <div class="post-time">${post.postDate}</div>
                            </div>
                        </div>
                        <div class="post-likes">
                            <i class="${heartIconClass}"></i> 
                            <span class="likes-count">${likesCount}</span>
                        </div>
                    </div>
                    <h3 class="post-title">${post.title}</h3>
                    <p class="post-desc">${post.description ? post.description : ''}</p>
                </div>
            </article>
        `;
    };

    // obtiene el nombre de usuario desde la URL y carga sus publicaciones
    const params = new URLSearchParams(window.location.search);
    const username = params.get('username');

    if (username) {
        // consulta al getUserPosts.php los posts del usuario
        fetch(`../getUserPosts.php?username=${encodeURIComponent(username)}`)
            .then(res => {
                if (!res.ok) throw new Error('Error en la respuesta de la API');
                return res.json();
            })
            .then(data => {
                // si no hay resultados, muestra mensaje informativo
                if (!Array.isArray(data) || data.length === 0) {
                    userPostsDiv.innerHTML = '<p>No hay recetas recientes.</p>';
                    return;
                }

                // rendenriza las publicaciones del usuario
                const postsHTML = data.map(createPostCard).join('');
                userPostsDiv.innerHTML = `<div class="posts-grid">${postsHTML}</div>`;
            })
            .catch(err => {
                // maneja errores de red o respuesta inválida
                console.error('Error cargando recetas de usuario:', err);
                userPostsDiv.innerHTML = '<p>Error cargando recetas. Inténtalo más tarde.</p>';
            });
    }
});
