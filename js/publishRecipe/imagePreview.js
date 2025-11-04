// js/publish_image_handler.js

document.addEventListener("DOMContentLoaded", () => {
    const imagePreview = document.getElementById("imagePreview");
    const imageInput = document.getElementById("imageInput");
    const mensajeDiv = document.getElementById("mensaje");

    if (!imageInput || !imagePreview || !mensajeDiv) return;

    // --- Manejo de la selección (change) para imágenes principales ---
    if (imageInput) {
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
    }

    // --- Manejo de previews para inputs de imagen de pasos (delegación)
    // Mantenemos un map por input para acumular selecciones sucesivas (los file inputs reemplazan por defecto)
    const stepFilesMap = new WeakMap();
    document.addEventListener('change', (e) => {
        const target = e.target;
        if (!target || !target.matches) return;
        if (target.matches('.step-image-input')) {
            const newFiles = target.files ? Array.from(target.files) : [];
            const container = target.closest('.input-container');
            if (!container) return;
            let stored = stepFilesMap.get(target) || [];

            // Combinar archivos previos con los nuevos, evitando duplicados por name+size
            const combined = stored.concat(newFiles).reduce((acc, file) => {
                const exists = acc.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                if (!exists) acc.push(file);
                return acc;
            }, []);

            const maxPerStep = 3;
            if (combined.length > maxPerStep) {
                // Truncar y avisar
                combined.length = maxPerStep;
            }

            // Reconstruir FileList usando DataTransfer
            const dt = new DataTransfer();
            combined.forEach(f => dt.items.add(f));
            try {
                target.files = dt.files;
            } catch (err) {
                // En algunos entornos target.files es read-only; en ese caso dejamos el input tal como está
                console.warn('No se pudo asignar target.files programáticamente:', err);
            }

            // Guardar en el map
            stepFilesMap.set(target, combined);

            // Renderizar preview
            let preview = container.querySelector('.step-image-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'step-image-preview';
                target.parentElement.appendChild(preview);
            }
            preview.innerHTML = '';

            combined.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.alt = `Vista previa del paso ${index + 1}`;
                    img.style.maxWidth = '140px';
                    img.style.marginRight = '8px';
                    img.style.borderRadius = '6px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });

            if (combined.length === 0) return;
            if (combined.length >= maxPerStep) {
                const warn = document.createElement('div');
                warn.className = 'text-warning small mt-2';
                warn.textContent = `Se mostrarán solo las primeras ${maxPerStep} imágenes para este paso.`;
                preview.appendChild(warn);
            }

            const removeAllBtn = document.createElement('button');
            removeAllBtn.type = 'button';
            removeAllBtn.className = 'remove-step-image btn btn-sm btn-outline-danger mt-2';
            removeAllBtn.textContent = 'Eliminar imágenes';
            preview.appendChild(removeAllBtn);
        }
    });

    // Manejador delegado (click) para eliminar imágenes de un paso
    document.addEventListener('click', (e) => {
        const target = e.target;
        if (target && target.classList && target.classList.contains('remove-step-image')) {
            const container = target.closest('.input-container');
            if (!container) return;
            const fileInput = container.querySelector('.step-image-input');
            const preview = container.querySelector('.step-image-preview');
            if (fileInput) {
                fileInput.value = '';
                stepFilesMap.delete(fileInput);
            }
            if (preview) preview.innerHTML = '';
        }
    });
});
