// js/publish_dynamic_fields.js

document.addEventListener("DOMContentLoaded", () => {
    const ingredientsList = document.getElementById("ingredients-list");
    const addIngredienteBtn = document.getElementById("addIngrediente");
    let ingredienteCount = 0; // Usaremos el conteo de elementos existentes

    const stepsList = document.getElementById("steps-list");
    const addPasoBtn = document.getElementById("addPaso");
    let pasoCount = 0; // Usaremos el conteo de elementos existentes

    // --- Lógica de Ingredientes ---

    const updateIngredientNumbers = () => {
        document.querySelectorAll('#ingredients-list .input-ingredient').forEach((input, index) => {
            input.placeholder = `Ingrediente ${index + 1}`;
        });
        ingredienteCount = document.querySelectorAll('#ingredients-list .input-ingredient').length;
    };

    const handleDeleteIngredient = (container) => {
        const totalIngredients = document.querySelectorAll('#ingredients-list .input-container').length;
        if (totalIngredients > 1) {
            container.remove();
            updateIngredientNumbers();
        } else {
            alert('Debe haber al menos un ingrediente');
        }
    };
    
    // Inicializar listeners de borrado para campos existentes
    ingredientsList.querySelectorAll('.delete-item').forEach(btn => {
        btn.onclick = function() {
            handleDeleteIngredient(btn.closest('.input-container'));
        };
    });


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
        
        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "delete-item buttono";
        deleteBtn.innerHTML = "&times;";
        deleteBtn.onclick = function() {
            handleDeleteIngredient(container);
        };
        
        inputWrapper.appendChild(input);
        
        const buttonWrapper = document.createElement("div");
        buttonWrapper.className = "button-wrapper";
        buttonWrapper.appendChild(deleteBtn);
        
        container.appendChild(inputWrapper);
        container.appendChild(buttonWrapper);
        ingredientsList.appendChild(container);
        updateIngredientNumbers();
    });
    
    updateIngredientNumbers(); // Establecer números iniciales

    // --- Lógica de Pasos ---

    const updateStepNumbers = () => {
        document.querySelectorAll('#steps-list .input-step').forEach((input, index) => {
            input.placeholder = `Paso ${index + 1}`;
        });
        pasoCount = document.querySelectorAll('#steps-list .input-step').length;
    };

    const handleDeleteStep = (container) => {
        const totalSteps = document.querySelectorAll('#steps-list .input-container').length;
        if (totalSteps > 1) {
            container.remove();
            updateStepNumbers();
        } else {
            alert('Debe haber al menos un paso');
        }
    };

    // Inicializar listeners de borrado para campos existentes
    stepsList.querySelectorAll('.delete-item').forEach(btn => {
        btn.onclick = function() {
            handleDeleteStep(btn.closest('.input-container'));
        };
    });


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

        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "delete-item buttono";
        deleteBtn.innerHTML = "&times;";
        deleteBtn.onclick = function() {
            handleDeleteStep(container);
        };
        
        inputWrapper.appendChild(input);

        const buttonWrapper = document.createElement("div");
        buttonWrapper.className = "button-wrapper";
        buttonWrapper.appendChild(deleteBtn);
        
        container.appendChild(inputWrapper);
        container.appendChild(buttonWrapper);
        stepsList.appendChild(container);
        updateStepNumbers();
    });
    
    updateStepNumbers(); // Establecer números iniciales
});