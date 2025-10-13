// publish.js
// Controla el formulario de publicación de recetas: valida campos, envía la imagen opcional y redirige según la respuesta.

document.addEventListener('DOMContentLoaded', () => {
    const formPublish = document.getElementById('formPublish');
    const mensajeDiv = document.getElementById('mensaje');

    // --- Ingredientes dinámicos ---
    const ingredientesList = document.getElementById('ingredientesList');
    const addIngredienteBtn = document.getElementById('addIngrediente');
    let ingredienteCount = 1;
    addIngredienteBtn.addEventListener('click', () => {
        ingredienteCount++;
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'ingredientes[]';
        input.className = 'ingredienteInput';
        input.placeholder = `Ingrediente ${ingredienteCount}`;
        input.required = true;
        ingredientesList.appendChild(input);
    });

    // --- Pasos dinámicos ---
    const pasosList = document.getElementById('pasosList');
    const addPasoBtn = document.getElementById('addPaso');
    let pasoCount = 1;
    addPasoBtn.addEventListener('click', () => {
        pasoCount++;
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'pasos[]';
        input.className = 'pasoInput';
        input.placeholder = `Paso ${pasoCount}`;
        input.required = true;
        pasosList.appendChild(input);
    });

    if (!formPublish) return;

    formPublish.addEventListener("submit", (e) => {
        e.preventDefault();

        const title = document.getElementById("recipeTitle").value.trim();
        const description = document.getElementById("recipeDescription").value.trim();
        const imageInput = document.getElementById('imageInput');

        // Validación básica de campos obligatorios
        if (!title || !description) {
            mensajeDiv.style.color = 'red';
            mensajeDiv.textContent = 'Completa título y descripción.';
            return;
        }

        // Obtener ingredientes y pasos
        const ingredientes = Array.from(document.querySelectorAll('.ingredienteInput')).map(i => i.value.trim()).filter(Boolean);
        const pasos = Array.from(document.querySelectorAll('.pasoInput')).map(i => i.value.trim()).filter(Boolean);

        if (ingredientes.length === 0 || pasos.length === 0) {
            mensajeDiv.style.color = 'red';
            mensajeDiv.textContent = 'Agrega al menos un ingrediente y un paso.';
            return;
        }

        // Construir FormData para envío, incl. archivo si existe
        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        ingredientes.forEach(ing => formData.append('ingredientes[]', ing));
        pasos.forEach(paso => formData.append('pasos[]', paso));
        if (imageInput && imageInput.files && imageInput.files[0]) {
            formData.append('image', imageInput.files[0]);
        }

        fetch("../publishRecipe.php", { method: 'POST', body: formData })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                mensajeDiv.style.color = "green";
                mensajeDiv.textContent = res.msj;
                formPublish.reset();
                // Redirigir a la receta publicada si se devuelve el postId
                if (res.postId) {
                    setTimeout(() => { window.location.href = `../visual/viewRecipe.php?id=${res.postId}`; }, 600);
                } else {
                    setTimeout(() => { window.location.href = 'index.php'; }, 800);
                }
            } else {
                console.error('Error del servidor:', res);
                mensajeDiv.style.color = "red";
                mensajeDiv.textContent = res.msj || 'Error al publicar.';
            }
        })
        .catch(err => {
            console.error('Error en la publicación:', err);
            mensajeDiv.style.color = 'red';
            mensajeDiv.textContent = 'Error de red al publicar. Intenta más tarde.';
        });
    });
});