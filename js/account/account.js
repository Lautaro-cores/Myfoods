// js/account.js 
document.addEventListener("DOMContentLoaded", () => {
    // 1. Definición de Elementos
    const form = document.getElementById("formImage");
    const userImageInput = document.getElementById("subirArchivo");
    const profileImage = document.querySelector('img[alt="Imagen de perfil"]');
    const editProfileModalEl = document.getElementById('editProfileModal');
    // Si usas Bootstrap, inicializar el modal solo si el elemento existe
    const editProfileModal = editProfileModalEl ? new bootstrap.Modal(editProfileModalEl) : null;
    const imagePreview = document.getElementById('imagePreview');
    const descriptionField = document.getElementById('description');
    
    // Si el formulario no existe (ej. usuario no logeado viendo perfil público), salimos.
    if (!form) return; 

    // 2. Lógica de Vista Previa de la Imagen (Preview handler)
    if (userImageInput && imagePreview) {
        userImageInput.addEventListener('change', () => {
            imagePreview.innerHTML = '';
            const file = userImageInput.files && userImageInput.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Vista previa';
                img.style.maxWidth = '200px';
                img.style.borderRadius = '8px';
                imagePreview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    // 3. Limpieza del Modal al cerrarse
    if (editProfileModalEl) {
        editProfileModalEl.addEventListener('hidden.bs.modal', () => {
            if (imagePreview) imagePreview.innerHTML = '';
            if (userImageInput) userImageInput.value = '';
        });
    }

    // 4. Manejo del Submit del Formulario
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.set('description', descriptionField ? descriptionField.value.trim() : '');

        try {
            const response = await fetch("../upload.php", {
                method: "POST",
                body: formData,
            });
            
            // Verificación explícita de la respuesta HTTP
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const result = await response.json();

            if (result.success) {
                // Actualizar imagen forzando recarga (Cache-Busting)
                // Esto es robusto, ya que siempre se refresca la imagen
                profileImage.src = `../getUserImage.php?ts=${new Date().getTime()}`; 
                
                // Actualizar descripción sin recargar
                const descriptionElement = document.querySelector(".user-description");
                const newDescription = descriptionField ? descriptionField.value.trim() : '';
                if (descriptionElement) {
                    descriptionElement.textContent = newDescription || "Sin descripción";
                }

                if (editProfileModal) editProfileModal.hide();
                alert(result.msj || 'Perfil actualizado');
            } else {
                alert(result.msj || 'Error al actualizar perfil');
            }
        } catch (err) {
            console.error('Error en fetch upload:', err);
            alert('Error al comunicarse con el servidor o JSON inválido.');
        }
    });
});