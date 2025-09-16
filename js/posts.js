function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString.replace(' ', 'T'));
    const diff = Math.floor((now - date) / 1000);
    if (diff < 60) return "hace unos segundos";
    if (diff < 3600) return `hace ${Math.floor(diff/60)} minutos`;
    if (diff < 86400) return `hace ${Math.floor(diff/3600)} horas`;
    return `hace ${Math.floor(diff/86400)} días`;
}

document.addEventListener('DOMContentLoaded', () => {
    fetch("../getPosts.php")
        .then(res => res.json())
        .then(posts => {
            const postsDiv = document.getElementById("posts");
            if (!posts || posts.length === 0) {
                postsDiv.innerHTML = "<p>No hay recetas publicadas aún.</p>";
                return;
            }
            postsDiv.innerHTML = posts.map(post => `
                <div onclick="location.href='../viewRecipe.php?id=${post.postId}'" style="border:1px solid #ccc; margin:10px 0; padding:10px; cursor:pointer;">
                    <strong>${post.title}</strong><br>
                    <small>Por ${post.userName} - ${timeAgo(post.postDate)}</small>
                </div>
            `).join('');
        });
});