document.addEventListener("DOMContentLoaded", () => {
    // Se obtienen las referencias a los elementos clave del DOM
    const formPublish = document.getElementById("formPublish");
    const mensajeDiv = document.getElementById("mensaje");
    const imagePreview = document.getElementById("imagePreview");
    const imageInput = document.getElementById("imageInput");
    // Selector para identificar los inputs de imagen asociados a cada paso de la receta
    const stepImageInputsSelector = '.step-image-input';

    // Termina la ejecución si el formulario de publicación no existe en la página
    if (!formPublish) return;

    // Lógica para gestionar la selección de tags
    function setupTagSelection() {
        // Función auxiliar para obtener todos los checkboxes de tags
        const getAllTagCheckboxes = () => Array.from(document.querySelectorAll('input[name="tags[]"]'));

        // Se escucha el evento 'change' en todo el documento
        document.addEventListener('change', (evt) => {
            const target = evt.target;
            // Se verifica si el elemento que disparó el evento es un checkbox de tag
            if (!target || !target.matches || !target.matches('input[name="tags[]"]')) return;

            const currentCheckbox = target;
            const categoryId = currentCheckbox.dataset.category 

            // Se ejecuta la lógica solo si se marcó un checkbox y tiene un ID de categoría
            if (currentCheckbox.checked && categoryId) {
                // Se recorren todos los checkboxes de tags
                getAllTagCheckboxes().forEach(cb => {
                    const cbCategory = cb.dataset.category 
                    // Se desmarca cualquier otro checkbox que pertenezca a la misma categoría y esté marcado, 
                    // asegurando una única selección por categoría
                    if (cb !== currentCheckbox && cbCategory === categoryId && cb.checked) {
                        cb.checked = false; 
                    }
                });
            }
        });
    }

    // Se inicializa la lógica de selección de tags
    setupTagSelection();


    // Manejo del evento de envío del formulario de publicación
    formPublish.addEventListener("submit", (e) => {
        // Previene el comportamiento por defecto del formulario (recarga de página)
        e.preventDefault();

        const title = document.getElementById("recipeTitle").value.trim();
        const description = document.getElementById("recipeDescription").value.trim();
        
        // 1. Recolección y validación de datos de ingredientes
        const ingredientContainers = document.querySelectorAll(".input-container");
        const ingredientes = [];
        const cantidades = [];
        const ingredientIds = [];
        
        // Se itera sobre cada contenedor de ingrediente para obtener su nombre, cantidad y ID
        ingredientContainers.forEach(container => {
            const ingredientInput = container.querySelector(".input-ingredient");
            const quantityInput = container.querySelector(".input-quantity");
            if (ingredientInput && quantityInput) {
                const ingredientValue = ingredientInput.value.trim();
                const quantityValue = quantityInput.value.trim();
                // Solo se agregan si ambos campos (ingrediente y cantidad) tienen valor
                if (ingredientValue && quantityValue) {
                    ingredientes.push(ingredientValue);
                    cantidades.push(quantityValue);
                    // Se guarda el ID del ingrediente o 'custom' si es un ingrediente nuevo
                    ingredientIds.push(ingredientInput.dataset.ingredientId || 'custom');
                }
            }
        });

        // Se recolectan los pasos de la receta, eliminando espacios y filtrando vacíos
        const pasos = Array.from(document.querySelectorAll(".input-step"))
            .map((i) => i.value.trim())
            .filter(Boolean);
        

        // Validaciones del formulario
        if (!title || !description) {
            // Muestra error si falta título o descripción
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Completa título y descripción";
            return;
        }
        if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
            // Muestra error si no se selecciona ninguna imagen principal
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Debes seleccionar al menos una imagen";
            return;
        }
        if (imageInput.files.length > 3) {
            // Muestra error si se seleccionan más de 3 imágenes principales
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Máximo 3 imágenes permitidas";
            return;
        }
        if (ingredientes.length === 0 || pasos.length === 0) {
            // Muestra error si faltan ingredientes o pasos
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Agrega al menos un ingrediente y un paso";
            return;
        }

        // 2. Construir FormData para el envío de datos
        const formData = new FormData();
        // Se añaden los datos de texto principales
        formData.append("title", title);
        formData.append("description", description);
        // Se añaden los arrays de ingredientes, cantidades e IDs
        ingredientes.forEach((ing) => formData.append("ingredientes[]", ing));
        cantidades.forEach((cant) => formData.append("cantidades[]", cant));
        ingredientIds.forEach((id) => formData.append("ingredientIds[]", id));
        // Se añade el array de pasos
        pasos.forEach((paso) => formData.append("pasos[]", paso));
        
        // Se recolectan y añaden los IDs de los tags seleccionados (checkboxes marcados)
        const selectedTags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked')).map(i => i.value);
        selectedTags.forEach(tid => formData.append('tags[]', tid));

        // Se añaden las imágenes principales de la receta
        Array.from(imageInput.files).forEach((file) => {
            formData.append("recipeImages[]", file);
        });

        // Recolección y validación de imágenes de pasos
        const stepImageInputs = Array.from(document.querySelectorAll(stepImageInputsSelector));
        
        // Se itera sobre los inputs de imagen por paso (el índice 'idx' coincide con la posición del paso)
        for (let idx = 0; idx < stepImageInputs.length; idx++) {
            const inp = stepImageInputs[idx];
            if (inp.files && inp.files.length > 0) {
                // Validación: Máximo 3 imágenes por paso
                if (inp.files.length > 3) {
                    mensajeDiv.style.color = 'red';
                    mensajeDiv.textContent = `Máximo 3 imágenes permitidas por paso (paso ${idx + 1})`;
                    return; // Detiene el envío si la validación falla
                }
                // Se añaden las imágenes de cada paso, agrupadas por su índice de paso
                Array.from(inp.files).forEach((file) => {
                    formData.append(`stepImages[${idx}][]`, file);
                });
            }
        }

        // 3. Envío al servidor usando Fetch API
        fetch("../publishRecipe.php", { method: "POST", body: formData })
            // Se procesa la respuesta: se verifica si es JSON o texto plano
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    // Si es JSON, se parsea y se devuelve el objeto de resultado
                    return res.json().then(data => ({
                        isJson: true,
                        data: data,
                        status: res.status,
                        ok: res.ok
                    }));
                } else {
                    // Si no es JSON (ej. error PHP), se lee el texto y se devuelve como error
                    return res.text().then(text => ({
                        isJson: false,
                        data: text,
                        status: res.status,
                        ok: res.ok
                    }));
                }
            })
            .then((result) => {
                // Se verifica el estado HTTP de la respuesta
                if (!result.ok) {
                    throw new Error(`HTTP error! status: ${result.status}`);
                }
                // Se verifica que la respuesta sea JSON si se esperaba
                if (!result.isJson) {
                    console.error("Respuesta no JSON:", result.data);
                    throw new Error("El servidor no devolvió JSON válido");
                }
                
                const res = result.data;
                
                // Lógica si la publicación fue exitosa (indicada por 'success: true' en el JSON)
                if (res.success) {
                    mensajeDiv.style.color = "green";
                    mensajeDiv.textContent = res.msj;
                    // Se resetea el formulario y la vista previa de imágenes
                    formPublish.reset();
                    imagePreview.innerHTML = "";
                    
                    // Se determina la URL de redirección (a la receta publicada o al índice)
                    const redirectUrl = res.postId ? `viewRecipe.php?id=${res.postId}` : "index.php";
                    // Se espera un breve momento antes de redirigir al usuario
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 800);
                } else {
                    // Lógica si la publicación falló (indicada por 'success: false')
                    console.error("Error del servidor:", res);
                    mensajeDiv.style.color = "red";
                    mensajeDiv.textContent = res.msj || "Error al publicar la receta";
                }
            })
            .catch((err) => {
                // Manejo de errores de la red o del proceso de Fetch
                console.error("Error en la publicación:", err);
                mensajeDiv.style.color = "red";
                // Muestra detalles si el error fue que el servidor no devolvió JSON
                if (err.message.includes("no devolvió JSON")) {
                    mensajeDiv.innerHTML = "Error del servidor: <br><pre class='error-details'></pre>";
                    mensajeDiv.querySelector('.error-details').textContent = err.message;
                } else {
                    // Muestra el mensaje de error general
                    mensajeDiv.textContent = "Error: " + err.message;
                }
            });
    });
});