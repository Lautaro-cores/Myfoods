// publish.js
// este archivo maneja la lógica de validación, recolección de datos y envío del formulario de publicacion de recetas

document.addEventListener("DOMContentLoaded", () => {
    const formPublish = document.getElementById("formPublish");
    const mensajeDiv = document.getElementById("mensaje");
    const imagePreview = document.getElementById("imagePreview");
    const imageInput = document.getElementById("imageInput");
    const stepImageInputsSelector = '.step-image-input';

    if (!formPublish) return;

    // lógica para gestionar la selección de tags (solo se permite uno por categoría)
    function setupTagSelection() {
        const getAllTagCheckboxes = () => Array.from(document.querySelectorAll('input[name="tags[]"]'));

        document.addEventListener('change', (e) => {
            const target = e.target;
            // verifica si el elemento que disparó el evento es un checkbox de tag
            if (!target || !target.matches || !target.matches('input[name="tags[]"]')) return;

            const currentCheckbox = target;
            const categoryId = currentCheckbox.dataset.category 

            // si se marcó un checkbox, desmarca los demás de su misma categoría
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

    setupTagSelection();


    // manejo del evento de envío del formulario de publicación
    formPublish.addEventListener("submit", (e) => {
        e.preventDefault();

        const title = document.getElementById("recipeTitle").value.trim();
        const description = document.getElementById("recipeDescription").value.trim();
        
        // recolección de ingredientes
        const ingredientContainers = document.querySelectorAll(".input-container");
        const ingredientes = [];
        const cantidades = [];
        const ingredientIds = [];
        
        ingredientContainers.forEach(container => {
            const ingredientInput = container.querySelector(".input-ingredient");
            const quantityInput = container.querySelector(".input-quantity");
            if (ingredientInput && quantityInput) {
                const ingredientValue = ingredientInput.value.trim();
                const quantityValue = quantityInput.value.trim();
                // solo se agregan si ambos campos tienen valor
                if (ingredientValue && quantityValue) {
                    ingredientes.push(ingredientValue);
                    cantidades.push(quantityValue);
                    // se guarda el id, usando 'custom' si no existe (nuevo ingrediente)
                    ingredientIds.push(ingredientInput.dataset.ingredientId || 'custom');
                }
            }
        });

        // se recolectan los pasos, limpiando espacios y eliminando vacíos
        const pasos = Array.from(document.querySelectorAll(".input-step"))
            .map((i) => i.value.trim())
            .filter(Boolean);
        

        // valida los inputs del formulario
        if (!title || !description) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "completa título y descripción";
            return;
        }
        if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "debes seleccionar al menos una imagen";
            return;
        }
        if (imageInput.files.length > 3) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "máximo 3 imágenes permitidas";
            return;
        }
        if (ingredientes.length === 0 || pasos.length === 0) {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "agrega al menos un ingrediente y un paso";
            return;
        }

        // construir formdata para el envío de datos
        const formData = new FormData();
        formData.append("title", title);
        formData.append("description", description);
        
        // se añaden los arrays para ingredientes, cantidades e ids
        ingredientes.forEach((ing) => formData.append("ingredientes[]", ing));
        cantidades.forEach((cant) => formData.append("cantidades[]", cant));
        ingredientIds.forEach((id) => formData.append("ingredientIds[]", id));
        pasos.forEach((paso) => formData.append("pasos[]", paso));
        
        // se añaden los tags seleccionados
        const selectedTags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked')).map(i => i.value);
        selectedTags.forEach(tid => formData.append('tags[]', tid));

        // se añaden las imágenes principales
        Array.from(imageInput.files).forEach((file) => {
            formData.append("recipeImages[]", file);
        });

        // recolección y validación de imágenes de pasos
        const stepImageInputs = Array.from(document.querySelectorAll(stepImageInputsSelector));
        
        // se itera sobre los inputs de imagen por paso (su índice en el array coincide con la posición del paso)
        for (let idx = 0; idx < stepImageInputs.length; idx++) {
            const inp = stepImageInputs[idx];
            if (inp.files && inp.files.length > 0) {
                // validación de límite de 3 imágenes por paso
                if (inp.files.length > 3) {
                    mensajeDiv.style.color = 'red';
                    mensajeDiv.textContent = `máximo 3 imágenes permitidas por paso (paso ${idx + 1})`;
                    return; // detiene el envío si falla
                }
                // se adjuntan las imágenes de este paso al formdata, con el índice para agruparlas en el servidor
                Array.from(inp.files).forEach((file) => {
                    formData.append(`stepImages[${idx}][]`, file);
                });
            }
        }

        // envía al publishRecipe.php para publicar el post 
        fetch("../publishRecipe.php", { method: "POST", body: formData })
            // se procesa la respuesta para manejar json o texto plano (errores)
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                // si la respuesta es json, la parsea
                if (contentType && contentType.includes("application/json")) {
                    return res.json().then(data => ({ isJson: true, data, status: res.status, ok: res.ok }));
                } else {
                    // si no es json, lee el texto como error
                    return res.text().then(text => ({ isJson: false, data: text, status: res.status, ok: res.ok }));
                }
            })
            .then((result) => {
                // verifica el estado http
                if (!result.ok) {
                    throw new Error(`http error! status: ${result.status}`);
                }
                // verifica formato json
                if (!result.isJson) {
                    console.error("respuesta no json:", result.data);
                    throw new Error("el servidor no devolvió json válido");
                }
                
                const res = result.data;
                
                // lógica de éxito
                if (res.success) {
                    mensajeDiv.style.color = "green";
                    mensajeDiv.textContent = res.msj;
                    // resetea el formulario y la vista previa
                    formPublish.reset();
                    imagePreview.innerHTML = "";
                    
                    // redirige al usuario a la receta publicada
                    const redirectUrl = res.postId ? `viewRecipe.php?id=${res.postId}` : "index.php";
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 800);
                } else {
                    console.error("error del servidor:", res);
                    mensajeDiv.style.color = "red";
                    mensajeDiv.textContent = res.msj || "error al publicar la receta";
                }
            })
            .catch((err) => {
                console.error("error en la publicación:", err);
                mensajeDiv.style.color = "red";
                if (err.message.includes("no devolvió json")) {
                    mensajeDiv.innerHTML = "error del servidor: <br><pre class='error-details'></pre>";
                    mensajeDiv.querySelector('.error-details').textContent = err.message;
                } else {
                    mensajeDiv.textContent = "error: " + err.message;
                }
            });
    });
});