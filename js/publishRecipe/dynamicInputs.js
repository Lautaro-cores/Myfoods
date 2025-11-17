// dynamicInputs.js
// este archivo maneja los inputs dinámicos de ingredientes y pasos de la pagina de publicar post, incluyendo la funcionalidad de autocompletado

document.addEventListener("DOMContentLoaded", () => {
    // obtiene los contenedores y botones principales del DOM
    const ingredientsList = document.getElementById("ingredients-list");
    const addIngredienteBtn = document.getElementById("addIngrediente");
    let ingredienteCount = 0;

    const stepsList = document.getElementById("steps-list");
    const addPasoBtn = document.getElementById("addPaso");
    let pasoCount = 0;

    // funcion para configurar el autocompletado en el campo de ingrediente
    const setupAutocomplete = (input) => {
        const wrapper = input.closest('.input-wrapper');
        // añade el evento de 'input' para disparar la búsqueda
        input.addEventListener('input', async (e) => {
            const term = e.target.value.trim();
            if (term.length < 2) return;
            try {
                // consulta al getIngredients.php para obtener sugerencias
                const response = await fetch(`../getIngredients.php?term=${encodeURIComponent(term)}`);
                const ingredients = await response.json();
                // remueve la lista existente antes de crear uno nuevo
                const existingDropdown = wrapper.querySelector('.autocomplete-dropdown');
                if (existingDropdown) existingDropdown.remove();
                // si hay resultados, crea y muestra la lista desplegable
                if (ingredients.length > 0) {
                    const dropdown = document.createElement('div');
                    dropdown.className = 'autocomplete-dropdown';
                    ingredients.forEach(ing => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        item.textContent = ing.value;
                        // maneja la selección de un ingrediente sugerido
                        item.addEventListener('click', () => {
                            input.value = ing.value;
                            input.dataset.ingredientId = ing.id;
                            dropdown.remove();
                            wrapper.querySelector('.input-quantity').focus();
                        });
                        dropdown.appendChild(item);
                    });
                    wrapper.appendChild(dropdown);
                }
            } catch (error) {
                console.error('Error al obtener ingredientes:', error);
            }
        });
    };

    // funcion que reasigna los números de los ingredientes después agregar/eliminar uno
    const updateIngredientNumbers = () => {
        document.querySelectorAll('#ingredients-list .input-ingredient').forEach((input, index) => {
            input.placeholder = `Ingrediente ${index + 1}`;
        });
        ingredienteCount = document.querySelectorAll('#ingredients-list .input-ingredient').length;
    };

    // funcion para eliminar un ingrediente del formulario
    const handleDeleteIngredient = (container) => {
        const totalIngredients = document.querySelectorAll('#ingredients-list .input-container').length;
        // solo permite eliminar si quedan más de uno
        if (totalIngredients > 1) {
            container.remove();
            updateIngredientNumbers();
        } else {
            alert('Debe haber al menos un ingrediente');
        }
    };
    
    // asigna el botón de borrado a los ingredientes que ya están cargados
    ingredientsList.querySelectorAll('.delete-item').forEach(btn => {
        btn.onclick = function() {
            handleDeleteIngredient(btn.closest('.input-container'));
        };
    });

    // evento que agrega un nuevo campo de ingrediente al hacer clic
    addIngredienteBtn.addEventListener("click", () => {
        const currentCount = document.querySelectorAll('#ingredients-list .input-container').length + 1;
        // crea los elementos del DOM necesarios para el nuevo ingrediente (input, cantidad, botón de borrar)
        const container = document.createElement("div");
        container.className = "input-container";
        
        const inputWrapper = document.createElement("div");
        inputWrapper.className = "input-wrapper";
        
        const input = document.createElement("input");
        input.type = "text";
        input.name = "ingredientes[]";
        input.className = "input-ingredient input";
        input.placeholder = `Ingrediente ${currentCount}`;
        input.required = true;
        
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "ingredientIds[]";
        hiddenInput.className = "ingredient-id";
        
        const quantityInput = document.createElement("input");
        quantityInput.type = "text";
        quantityInput.name = "cantidades[]";
        quantityInput.className = "input-quantity input";
        quantityInput.placeholder = "Cantidad";
        
        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "delete-item buttono";
        deleteBtn.innerHTML = "&times;";
        deleteBtn.onclick = function() {
            handleDeleteIngredient(container);
        };
        
        // ensambla los elementos en la estructura correcta
        inputWrapper.appendChild(input);
        inputWrapper.appendChild(quantityInput);
        const buttonWrapper = document.createElement("div");
        buttonWrapper.className = "button-wrapper";
        buttonWrapper.appendChild(deleteBtn);
        
        container.appendChild(inputWrapper);
        container.appendChild(buttonWrapper);
        container.appendChild(hiddenInput);
        ingredientsList.appendChild(container);
        
        // aplica el autocompletado y actualiza los números
        setupAutocomplete(input);
        updateIngredientNumbers();
    });
    
    updateIngredientNumbers();

    // funcion que reasigna los números y atributos de los pasos e imágenes
    const updateStepNumbers = () => {
        // actualiza los placeholders de los inputs de texto
        document.querySelectorAll('#steps-list .input-step').forEach((input, index) => {
            input.placeholder = `Paso ${index + 1}`;
        });
        pasoCount = document.querySelectorAll('#steps-list .input-step').length;
        
        // actualiza los nombres de los inputs de archivos (para el backend)
        document.querySelectorAll('#steps-list .input-container').forEach((container, idx) => {
            const fileInput = container.querySelector('.step-image-input');
            if (fileInput) {
                fileInput.name = `stepImages[${idx}][]`;
                fileInput.multiple = true;
            }
            // limpia la vista previa de las imágenes al reasignar
            const preview = container.querySelector('.step-image-preview');
            if (preview) preview.innerHTML = '';
        });
    };

    // funcion para eliminar un paso del formulario
    const handleDeleteStep = (container) => {
        const totalSteps = document.querySelectorAll('#steps-list .input-container').length;
        // solo permite eliminar si quedan más de uno
        if (totalSteps > 1) {
            container.remove();
            updateStepNumbers();
        } else {
            alert('Debe haber al menos un paso');
        }
    };

    // asigna el botón de borrado a los pasos que ya están cargados
    stepsList.querySelectorAll('.delete-item').forEach(btn => {
        btn.onclick = function() {
            handleDeleteStep(btn.closest('.input-container'));
        };
    });

    // evento que agrega un nuevo campo de paso al hacer clic
    addPasoBtn.addEventListener("click", () => {
        const currentCount = document.querySelectorAll('#steps-list .input-container').length + 1;
        // crea el contenedor principal del paso
        const container = document.createElement("div");
        container.className = "input-container";
        
        const inputWrapper = document.createElement("div");
        inputWrapper.className = "input-wrapper";

        // crea el input de texto del paso
        const input = document.createElement("input");
        input.type = "text";
        input.name = "pasos[]";
        input.className = "input-step input";
        input.placeholder = `Paso ${currentCount}`;
        input.required = true;

        // crea el input para subir imágenes del paso
        const fileInput = document.createElement("input");
        fileInput.type = "file";
        const newIndex = currentCount - 1;
        fileInput.name = `stepImages[${newIndex}][]`;
        fileInput.accept = "image/*";
        fileInput.multiple = true;
        fileInput.className = "form-control step-image-input mt-2";

        const previewDiv = document.createElement('div');
        previewDiv.className = 'step-image-preview mt-2';

        // crea el botón de borrado
        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "delete-item buttono";
        deleteBtn.innerHTML = "&times;";
        deleteBtn.onclick = function() {
            handleDeleteStep(container);
        };
        
        // ensambla los elementos
        inputWrapper.appendChild(input);
        inputWrapper.appendChild(fileInput);
        inputWrapper.appendChild(previewDiv);
        const buttonWrapper = document.createElement("div");
        buttonWrapper.className = "button-wrapper";
        buttonWrapper.appendChild(deleteBtn);
        
        container.appendChild(inputWrapper);
        container.appendChild(buttonWrapper);
        stepsList.appendChild(container);
        
        // actualiza los números de los pasos
        updateStepNumbers();
    });
    
    updateStepNumbers();
});