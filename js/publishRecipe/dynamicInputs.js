document.addEventListener("DOMContentLoaded", () => {
    const ingredientsList = document.getElementById("ingredients-list");
    const addIngredienteBtn = document.getElementById("addIngrediente");
    let ingredienteCount = 0;

    const stepsList = document.getElementById("steps-list");
    const addPasoBtn = document.getElementById("addPaso");
    let pasoCount = 0;

    // Autocompleta los ingredientes al escribir
    const setupAutocomplete = (input) => {
        const wrapper = input.closest('.input-wrapper');
        input.addEventListener('input', async (e) => {
            const term = e.target.value.trim();
            if (term.length < 2) return;
            try {
                const response = await fetch(`../getIngredients.php?term=${encodeURIComponent(term)}`);
                const ingredients = await response.json();
                const existingDropdown = wrapper.querySelector('.autocomplete-dropdown');
                if (existingDropdown) existingDropdown.remove();
                if (ingredients.length > 0) {
                    const dropdown = document.createElement('div');
                    dropdown.className = 'autocomplete-dropdown';
                    ingredients.forEach(ing => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        item.textContent = ing.value;
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

    // Reasigna los números de los ingredientes
    const updateIngredientNumbers = () => {
        document.querySelectorAll('#ingredients-list .input-ingredient').forEach((input, index) => {
            input.placeholder = `Ingrediente ${index + 1}`;
        });
        ingredienteCount = document.querySelectorAll('#ingredients-list .input-ingredient').length;
    };

    // Elimina un ingrediente del formulario
    const handleDeleteIngredient = (container) => {
        const totalIngredients = document.querySelectorAll('#ingredients-list .input-container').length;
        if (totalIngredients > 1) {
            container.remove();
            updateIngredientNumbers();
        } else {
            alert('Debe haber al menos un ingrediente');
        }
    };
    
    // Asigna el botón de borrado a los ingredientes existentes
    ingredientsList.querySelectorAll('.delete-item').forEach(btn => {
        btn.onclick = function() {
            handleDeleteIngredient(btn.closest('.input-container'));
        };
    });

    // Agrega un nuevo ingrediente al formulario
    addIngredienteBtn.addEventListener("click", () => {
        const currentCount = document.querySelectorAll('#ingredients-list .input-container').length + 1;
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
        
        inputWrapper.appendChild(input);
        inputWrapper.appendChild(quantityInput);
        const buttonWrapper = document.createElement("div");
        buttonWrapper.className = "button-wrapper";
        buttonWrapper.appendChild(deleteBtn);
        
        container.appendChild(inputWrapper);
        container.appendChild(buttonWrapper);
        container.appendChild(hiddenInput);
        ingredientsList.appendChild(container);
        setupAutocomplete(input);
        updateIngredientNumbers();
    });
    
    updateIngredientNumbers();

    // Reasigna los números y nombres de los pasos e imágenes
    const updateStepNumbers = () => {
        document.querySelectorAll('#steps-list .input-step').forEach((input, index) => {
            input.placeholder = `Paso ${index + 1}`;
        });
        pasoCount = document.querySelectorAll('#steps-list .input-step').length;
        document.querySelectorAll('#steps-list .input-container').forEach((container, idx) => {
            const fileInput = container.querySelector('.step-image-input');
            if (fileInput) {
                fileInput.name = `stepImages[${idx}][]`;
                fileInput.multiple = true;
            }
            const preview = container.querySelector('.step-image-preview');
            if (preview) preview.innerHTML = '';
        });
    };

    // Elimina un paso del formulario
    const handleDeleteStep = (container) => {
        const totalSteps = document.querySelectorAll('#steps-list .input-container').length;
        if (totalSteps > 1) {
            container.remove();
            updateStepNumbers();
        } else {
            alert('Debe haber al menos un paso');
        }
    };

    // Asigna el botón de borrado a los pasos existentes
    stepsList.querySelectorAll('.delete-item').forEach(btn => {
        btn.onclick = function() {
            handleDeleteStep(btn.closest('.input-container'));
        };
    });

    // Agrega un nuevo paso con campo de texto e imagen
    addPasoBtn.addEventListener("click", () => {
        const currentCount = document.querySelectorAll('#steps-list .input-container').length + 1;
        const container = document.createElement("div");
        container.className = "input-container";
        
        const inputWrapper = document.createElement("div");
        inputWrapper.className = "input-wrapper";

        const input = document.createElement("input");
        input.type = "text";
        input.name = "pasos[]";
        input.className = "input-step input";
        input.placeholder = `Paso ${currentCount}`;
        input.required = true;

        const fileInput = document.createElement("input");
        fileInput.type = "file";
        const newIndex = currentCount - 1;
        fileInput.name = `stepImages[${newIndex}][]`;
        fileInput.accept = "image/*";
        fileInput.multiple = true;
        fileInput.className = "form-control step-image-input mt-2";

        const previewDiv = document.createElement('div');
        previewDiv.className = 'step-image-preview mt-2';

        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "delete-item buttono";
        deleteBtn.innerHTML = "&times;";
        deleteBtn.onclick = function() {
            handleDeleteStep(container);
        };
        
        inputWrapper.appendChild(input);
        inputWrapper.appendChild(fileInput);
        inputWrapper.appendChild(previewDiv);
        const buttonWrapper = document.createElement("div");
        buttonWrapper.className = "button-wrapper";
        buttonWrapper.appendChild(deleteBtn);
        
        container.appendChild(inputWrapper);
        container.appendChild(buttonWrapper);
        stepsList.appendChild(container);
        updateStepNumbers();
    });
    
    updateStepNumbers();
});
