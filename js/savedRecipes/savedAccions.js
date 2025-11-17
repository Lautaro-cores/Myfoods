// savedAccions.js
// este archivo maneja las acciones de las recetas guardadas, como ver y quitar de guardados

export function setupPostActions(container) {
    
    // Configuración de la lógica para los botones "Ver"
    // Busca todos los botones de 'Ver' dentro del contenedor dado
    container.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Obtiene el ID de la receta guardado en el atributo data-id
            const id = btn.getAttribute('data-id');
            // Redirige al usuario a la página de vista de la receta
            window.location.href = `viewRecipe.php?id=${id}`;
        });
    });

    // Configuración de la lógica para el botón "Quitar de Guardados"
    // Busca todos los botones de 'Quitar de Guardados'
    container.querySelectorAll('.remove-fav').forEach(btn => {
        btn.addEventListener('click', () => {
            // Obtiene el ID de la receta a eliminar
            const id = btn.getAttribute('data-id');
            // Crea un objeto para enviar los datos como x-www-form-urlencoded
            const form = new URLSearchParams();
            form.append('postId', id);
            
            // Envía la solicitud al endpoint que alterna el estado de favorito
            fetch('../toggleFavorite.php', {
                method: 'POST',
                body: form.toString(),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            // Procesa la respuesta JSON del servidor
            .then(res => res.json())
            .then(r => {
                // Si la acción fue exitosa
                if (r.success) {
                    // Busca el elemento padre (tarjeta de la receta) para eliminarlo del DOM
                    const postCard = btn.closest('article');
                    if(postCard) postCard.remove(); 
                    
                    // Verifica si el contenedor ha quedado vacío después de la eliminación
                    if (container.querySelectorAll('article').length === 0) {
                        // Si no quedan tarjetas, muestra un mensaje indicando que la lista está vacía
                        container.innerHTML = '<p>Ya no tienes recetas guardadas.</p>';
                    }
                } else {
                    // Muestra una alerta si el servidor devuelve un error
                    alert(r.msj || 'Error al quitar de guardados');
                }
            })
            .catch(err => {
                // Maneja los errores de red o del proceso de Fetch
                console.error('Error al alternar favorito:', err);
                alert('Error de red al intentar quitar el favorito');
            });
        });
    });
}