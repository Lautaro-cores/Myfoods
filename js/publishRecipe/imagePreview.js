// imagePreview.js
// este archivo maneja la vista previa de las imágenes antes de subirlas en la página de publicar receta

document.addEventListener("DOMContentLoaded", () => {
    // obtiene los elementos clave del DOM
    const imagePreview = document.getElementById("imagePreview");
    const imageInput = document.getElementById("imageInput");
    const mensajeDiv = document.getElementById("mensaje");

    if (!imageInput || !imagePreview || !mensajeDiv) return;

    if (imageInput) {
        imageInput.addEventListener("change", () => {
            // limpia la vista previa y los mensajes de error existentes
            imagePreview.innerHTML = "";
            mensajeDiv.textContent = "";

            // valida el número máximo de imágenes permitidas
            if (imageInput.files.length > 3) {
                mensajeDiv.style.color = "red";
                mensajeDiv.textContent = "Máximo 3 imágenes permitidas";
                imageInput.value = "";
                return;
            }

            // pasa sobre cada archivo seleccionado para crear su vista previa
            Array.from(imageInput.files).forEach((file, index) => {
                const reader = new FileReader();
                const container = document.createElement("div");
                container.className = "preview-container";
                
                // cuando el archivo se carga, inserta la imagen y el botón de borrado
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

        //Maneja la eliminacion de las prewiews de las imagenes
        imagePreview.addEventListener("click", (e) => {
            // verifica que el clic fue en el botón de eliminar
            if (e.target.classList.contains("remove-image")) {
                const index = parseInt(e.target.dataset.index);
                const dt = new DataTransfer();
                const { files } = imageInput;
                
                // reconstruye la lista de archivos (FileList) omitiendo el índice a eliminar
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }
                
                // actualiza el input de archivo con la nueva lista
                imageInput.files = dt.files;
                e.target.parentElement.remove();
                document.querySelectorAll(".remove-image").forEach((btn, idx) => {
                    btn.dataset.index = idx;
                });
            }
        });
    }

    // Maneja los prewiew de las imagene en los pasos de la receta
    // usamos weakmap para almacenar archivos, ya que los file inputs por defecto no acumulan
    const stepFilesMap = new WeakMap();
    document.addEventListener('change', (e) => {
        const target = e.target;
        if (!target || !target.matches) return;
        // verifica si el input es un input de imagen de paso
        if (target.matches('.step-image-input')) {
            const newFiles = target.files ? Array.from(target.files) : [];
            const container = target.closest('.input-container');
            if (!container) return;
            // recupera los archivos almacenados para este input
            let stored = stepFilesMap.get(target) || [];

            // combina archivos previos con los nuevos, eliminando duplicados
            const combined = stored.concat(newFiles).reduce((acc, file) => {
                const exists = acc.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                if (!exists) acc.push(file);
                return acc;
            }, []);

            const maxPerStep = 3;
            // verifica el límite de archivos por paso y trunca si es necesario
            if (combined.length > maxPerStep) {
                combined.length = maxPerStep;
            }

            // reconstruye el filelist del input para enviarlo al servidor
            const dt = new DataTransfer();
            combined.forEach(f => dt.items.add(f));
            try {
                target.files = dt.files;
            } catch (err) {
                // maneja la excepción si target.files es read-only
                console.warn('No se pudo asignar target.files programáticamente:', err);
            }

            // guarda la lista combinada en el weakmap
            stepFilesMap.set(target, combined);

            // renderiza la vista previa de las imágenes combinadas
            let preview = container.querySelector('.step-image-preview');
            // si no existe el contenedor de preview, lo crea
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'step-image-preview';
                target.parentElement.appendChild(preview);
            }
            preview.innerHTML = '';

            // crea y adjunta el elemento img para cada archivo
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
            // muestra una advertencia si se alcanzó el límite máximo
            if (combined.length >= maxPerStep) {
                const warn = document.createElement('div');
                warn.className = 'text-warning small mt-2';
                warn.textContent = `Se mostrarán solo las primeras ${maxPerStep} imágenes para este paso.`;
                preview.appendChild(warn);
            }

            // añade el botón para eliminar todas las imágenes del paso
            const removeAllBtn = document.createElement('button');
            removeAllBtn.type = 'button';
            removeAllBtn.className = 'remove-step-image btn btn-sm btn-outline-danger mt-2';
            removeAllBtn.textContent = 'Eliminar imágenes';
            preview.appendChild(removeAllBtn);
        }
    });

    // manejador delegado (click) para eliminar todas las imágenes de un paso
    document.addEventListener('click', (e) => {
        const target = e.target;
        // verifica si el clic fue en el botón de eliminar imágenes del paso
        if (target && target.classList && target.classList.contains('remove-step-image')) {
            const container = target.closest('.input-container');
            if (!container) return;
            const fileInput = container.querySelector('.step-image-input');
            const preview = container.querySelector('.step-image-preview');
            if (fileInput) {
                // limpia el valor del input y borra la referencia en el weakmap
                fileInput.value = '';
                stepFilesMap.delete(fileInput);
            }
            // limpia la vista previa
            if (preview) preview.innerHTML = '';
        }
    });
});