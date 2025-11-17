//searchPost.js
// este archivo maneja la funcionalidad de búsqueda de recetas con filtros de contenido, etiquetas e ingredientes


document.addEventListener("DOMContentLoaded", () => {
    // Obtiene referencias a los elementos clave del DOM
    const btn = document.getElementById("searchButton");
    const input = document.getElementById("searchInput");
    const postsDiv = document.getElementById("posts");

    // Verifica que todos los elementos HTML necesarios existan
    if (!btn || !input || !postsDiv) {
        console.error("Faltan elementos HTML necesarios (btn, input, o postsDiv)");
        return;
    }

    // funcion para mostrar el tiempo que se subio el post
    function timeAgo(dateString) {
        const now = new Date();
        // Reemplaza el espacio por 'T' para asegurar el correcto parsing de la fecha
        const date = new Date(dateString.replace(" ", "T"));
        const diff = Math.floor((now - date) / 1000);
        // Calcula y devuelve la diferencia de tiempo
        if (diff < 60) return "hace unos segundos";
        if (diff < 3600) return `hace ${Math.floor(diff / 60)} minutos`;
        if (diff < 86400) return `hace ${Math.floor(diff / 3600)} horas`;
        return `hace ${Math.floor(diff / 86400)} días`;
    }

    // Crea el HTML completo para una tarjeta de publicación de receta
    function createPostCardHtml(post) {
        // Determina la URL del avatar, usando una imagen por defecto si es necesario
        const avatarUrl = post.userImage 
            ? `data:image/jpeg;base64,${post.userImage}` 
            : "../img/icono-imagen-perfil-predeterminado-alta-resolucion_852381-3658.jpg";
        
        let carouselHtml = "";
        // Genera el código HTML para el carrusel de imágenes
        if (post.images && post.images.length > 0) {
            const carouselId = `carouselPost${post.postId}`;
            carouselHtml = `
                <div id="${carouselId}" class="carousel slide">
                    <div class="carousel-inner">
                        ${post.images.map((img, idx) => ` <div class="carousel-item${idx === 0 ? " active" : ""}">
                            <img src="data:image/jpeg;base64,${img}" class="d-block w-100" alt="Imagen ${idx + 1} de ${post.title}">
                            </div>
                        `).join("")}
                    </div>
                    ${post.images.length > 1 ? `
                    <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                    ` : ""}
                </div>
            `;
        }
        const likesCount = post.likesCount || 0;

        // Retorna la estructura HTML de la tarjeta de publicación
        return `
            <article class="post-card">
                <div class="post-image">${carouselHtml}</div>
                <div class="post-content" onclick="location.href='../visual/viewRecipe.php?id=${post.postId}'">
                    <div class="post-header">
                        <div class="post-left">
                            <img class="post-avatar" src="${avatarUrl}" alt="Avatar ${post.userName}">
                            <div class="post-meta">
                                <div class="post-author">${post.displayName}</div>
                                <div class="post-time">${timeAgo(post.postDate)}</div>
                            </div>
                        </div>
                        <div class="post-likes"><i class="bi bi-heart"></i> <span class="likes-count">${likesCount}</span></div>
                    </div>
                    <h3 class="post-title">${post.title}</h3>
                    <p class="post-desc">${post.description || ""}</p>
                </div>
            </article>
        `;
    }

    // Renderiza la lista de resultados en la cuadrícula
    function renderResults(data) {
        // Muestra un mensaje si no se encuentran resultados
        if (!Array.isArray(data) || data.length === 0) {
            postsDiv.innerHTML = "<p>No se encontraron resultados.</p>";
            return;
        }

        // Convierte el array de objetos de post a una sola cadena HTML
        const postsHtml = data.map(createPostCardHtml).join("");
        // Inserta el HTML en el contenedor de publicaciones
        postsDiv.innerHTML = `<div class="posts-grid">${postsHtml}</div>`;
    }

    // Realiza la búsqueda al servidor utilizando los parámetros de filtro
    function doSearch() {
        const contenido = input.value.trim();
        // Obtiene el Set de tags seleccionados de la variable global
        const selectedTags = window.selectedTags || new Set(); 
        // Obtiene el Set de ingredientes seleccionados de la variable global
        const selectedIngredients = window.selectedIngredients || new Set();
        
        // No realiza la búsqueda si no hay ningún filtro aplicado
        if (!contenido && selectedTags.size === 0 && selectedIngredients.size === 0) {
            postsDiv.innerHTML = "<p>Escribe algo para buscar o selecciona etiquetas/ingredientes.</p>";
            return;
        }

        // Construye los parámetros de la URL para la solicitud
        const params = new URLSearchParams();
        if (contenido) params.set('contenido', contenido);
        if (selectedTags.size > 0) params.set('tags', Array.from(selectedTags).join(','));
        
        // Procesa y separa los ingredientes seleccionados
        if (selectedIngredients.size > 0) {
            // Divide las entradas "id||nombre"
            const entries = Array.from(selectedIngredients).map(e => e.split('||'));
            // Filtra los IDs numéricos (ingredientes existentes en BD)
            const numericIds = entries.filter(e => !String(e[0]).startsWith('c_')).map(e => e[0]);
            // Filtra los nombres personalizados (ingredientes creados por el usuario, marcados con 'c_')
            const customNames = entries.filter(e => String(e[0]).startsWith('c_')).map(e => e[1]);
            // Añade los parámetros si hay datos que enviar
            if (numericIds.length > 0) params.set('ingredients', numericIds.join(','));
            if (customNames.length > 0) params.set('ingredientNames', customNames.join(','));
        }


        
        // Ejecuta la petición de búsqueda
        fetch(`../searchRecipes.php?${params.toString()}`)
            .then((res) => {
                // Lanza un error si la respuesta no es OK
                if (!res.ok) throw new Error("Error de red");
                // Devuelve el JSON de los resultados
                return res.json();
            })
            .then(renderResults) // Renderiza los resultados obtenidos
            .catch((err) => {
                // Maneja cualquier error durante la búsqueda
                console.error("Error al buscar recetas:", err);
                postsDiv.innerHTML = "<p>Error al realizar la búsqueda.</p>";
            });
    }

    // Configura el evento de clic en el botón de búsqueda
    btn.addEventListener("click", doSearch);
    // Configura el evento de tecla 'Enter' en el campo de búsqueda
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") doSearch();
    });

    // Búsqueda inicial al cargar la página si hay un parámetro 'search' en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialContent = urlParams.get('search');
    
    // Si existe un término inicial, lo coloca en el input y ejecuta la búsqueda
    if (initialContent) {
        input.value = initialContent;
        doSearch();
    }
});