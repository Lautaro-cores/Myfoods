// js/publish_image_handler.js

document.addEventListener("DOMContentLoaded", () => {
    const imagePreview = document.getElementById("imagePreview");
    const imageInput = document.getElementById("imageInput");
    const mensajeDiv = document.getElementById("mensaje");

    if (!imageInput || !imagePreview || !mensajeDiv) return;

    // --- Manejo de la selección (change) ---
    imageInput.addEventListener("change", () => {
        // Limpiar preview existente
        imagePreview.innerHTML = "";
        mensajeDiv.textContent = "";

        // Validar número máximo de imágenes
        if (imageInput.files.length > 3) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Máximo 3 imágenes permitidas";
            imageInput.value = "";
            return;
        }

        // Crear previews para cada imagen
        Array.from(imageInput.files).forEach((file, index) => {
            const reader = new FileReader();
            const container = document.createElement("div");
            container.className = "preview-container";
            
            reader.onload = (e) => {
                container.innerHTML = `
                    <img src="${e.target.result}" alt="Vista previa ${index + 1}">
                    <button type="button" class="remove-image" data-index="${index}">&times;</button>
                `;
                imagePreview.appendChild(container);
            };
            reader.readAsDataURL(file);
        });
    });

    // --- Manejo de eliminación (click en el preview) ---
    imagePreview.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-image")) {
            const index = parseInt(e.target.dataset.index);
            const dt = new DataTransfer();
            const { files } = imageInput;
            
            // Reconstruir la lista de archivos omitiendo el índice a eliminar
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            imageInput.files = dt.files;
            e.target.parentElement.remove();
            
            // Reindexar los botones de eliminación restantes
            document.querySelectorAll(".remove-image").forEach((btn, idx) => {
                btn.dataset.index = idx;
            });
        }
    });
});