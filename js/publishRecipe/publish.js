// js/publish_form_handler.js

document.addEventListener("DOMContentLoaded", () => {
    const formPublish = document.getElementById("formPublish");
    const mensajeDiv = document.getElementById("mensaje");
    const imagePreview = document.getElementById("imagePreview");
    const imageInput = document.getElementById("imageInput");
    // los inputs de imagen por paso tienen la clase .step-image-input
    const stepImageInputsSelector = '.step-image-input';

    if (!formPublish) return;

    // --- Lógica de tags separada para mantener el DOMChange handler limpio ---
    function setupTagSelection() {
        const getAllTagCheckboxes = () => Array.from(document.querySelectorAll('input[name="tags[]"]'));

        document.addEventListener('change', (evt) => {
            const target = evt.target;
            if (!target || !target.matches || !target.matches('input[name="tags[]"]')) return;

            const currentCheckbox = target;
            const categoryId = currentCheckbox.dataset.category 

            // Solo si marcó un checkbox y tiene categoría, desmarcamos el anterior
            if (currentCheckbox.checked && categoryId) {
                getAllTagCheckboxes().forEach(cb => {
                    const cbCategory = cb.dataset.category 
                    if (cb !== currentCheckbox && cbCategory === categoryId && cb.checked) {
                        cb.checked = false; 
                    }
                });
            }
        });
    }


    // --- Manejo del Submit ---
    formPublish.addEventListener("submit", (e) => {
        e.preventDefault();

        const title = document.getElementById("recipeTitle").value.trim();
        const description = document.getElementById("recipeDescription").value.trim();
        
        // 1. Recolección y validación de datos
        const ingredientes = Array.from(document.querySelectorAll(".input-ingredient"))
            .map((i) => i.value.trim())
            .filter(Boolean);
        const pasos = Array.from(document.querySelectorAll(".input-step"))
            .map((i) => i.value.trim())
            .filter(Boolean);
        

        // Validaciones del formulario
        if (!title || !description) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Completa título y descripción.";
            return;
        }
        if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Debes seleccionar al menos una imagen.";
            return;
        }
        if (imageInput.files.length > 3) {
             // Esta validación también está en image_preview_handler, pero es bueno tenerla aquí también
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Máximo 3 imágenes permitidas.";
            return;
        }
        if (ingredientes.length === 0 || pasos.length === 0) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Agrega al menos un ingrediente y un paso.";
            return;
        }

        // 2. Construir FormData
        const formData = new FormData();
        formData.append("title", title);
        formData.append("description", description);
        ingredientes.forEach((ing) => formData.append("ingredientes[]", ing));
        pasos.forEach((paso) => formData.append("pasos[]", paso));
        
        const selectedTags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked')).map(i => i.value);
        selectedTags.forEach(tid => formData.append('tags[]', tid));

        Array.from(imageInput.files).forEach((file) => {
            formData.append("recipeImages[]", file);
        });

        // Recoger imágenes de pasos en el orden DOM (coincide con el orden de los pasos)
        const stepImageInputs = Array.from(document.querySelectorAll(stepImageInputsSelector));
        // Validar y añadir imágenes por paso (máx. 3 por paso)
        for (let idx = 0; idx < stepImageInputs.length; idx++) {
            const inp = stepImageInputs[idx];
            if (inp.files && inp.files.length > 0) {
                if (inp.files.length > 3) {
                    mensajeDiv.style.color = 'red';
                    mensajeDiv.textContent = `Máximo 3 imágenes permitidas por paso (paso ${idx + 1}).`;
                    return;
                }
                Array.from(inp.files).forEach((file) => {
                    formData.append(`stepImages[${idx}][]`, file);
                });
            }
        }

        // 3. Envío al servidor
        fetch("../publishRecipe.php", { method: "POST", body: formData })
            .then((res) => {
                if (!res.ok) throw new Error('Respuesta de red fallida');
                return res.json();
            })
            .then((res) => {
                if (res.success) {
                    mensajeDiv.style.color = "green";
                    mensajeDiv.textContent = res.msj;
                    formPublish.reset();
                    imagePreview.innerHTML = "";
                    
                    // Redirigir
                    const redirectUrl = res.postId ? `viewRecipe.php?id=${res.postId}` : "index.php";
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 800);
                } else {
                    console.error("Error del servidor:", res);
                    mensajeDiv.style.color = "red";
                    mensajeDiv.textContent = res.msj || "Error al publicar la receta.";
                }
            })
            .catch((err) => {
                console.error("Error en la publicación:", err);
                mensajeDiv.style.color = "red";
                mensajeDiv.textContent = "Error de conexión o JSON inválido.";
            });
    });
});