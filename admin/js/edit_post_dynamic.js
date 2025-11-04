document.addEventListener("DOMContentLoaded", () => {
    const ingredientsList = document.getElementById("ingredients-list");
    const addIngredienteBtn = document.getElementById("addIngrediente");

    const stepsList = document.getElementById("steps-list");
    const addPasoBtn = document.getElementById("addPaso");

    // --- Ingredientes ---
        const updateIngredientNumbers = () => {
            document.querySelectorAll('#ingredients-list .input-container').forEach((container, index) => {
                const input = container.querySelector('.input-ingredient');
                if (input) input.placeholder = `Nombre ingrediente ${index + 1}`;
                const quantity = container.querySelector('.input-quantity');
                if (quantity) quantity.placeholder = `Cantidad`;
            });
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
            // Select para tipo de ingrediente
            const select = document.createElement("select");
            select.name = "ingredientIds[]";
            select.className = "input-ingredient-select";
            select.innerHTML = `<option value="custom" selected>Personalizado</option>`;
            // Opciones de ingredientes estructurados (rellenar dinámicamente si se desea)
            if (window.ingredientOptions) {
                window.ingredientOptions.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.id;
                    option.textContent = opt.name;
                    select.appendChild(option);
                });
            }
            inputWrapper.appendChild(select);
            // Input nombre
            const input = document.createElement("input");
            input.type = "text";
            input.name = "ingredientes[]";
            input.className = "input-ingredient input";
            input.placeholder = `Nombre ingrediente ${currentCount}`;
            input.required = true;
            inputWrapper.appendChild(input);
            // Input cantidad
            const quantity = document.createElement("input");
            quantity.type = "text";
            quantity.name = "cantidades[]";
            quantity.className = "input-quantity input";
            quantity.placeholder = `Cantidad`;
            quantity.required = true;
            inputWrapper.appendChild(quantity);
            // Botón borrar
            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.className = "delete-item buttono";
            deleteBtn.innerHTML = "&times;";
            deleteBtn.onclick = function() {
                handleDeleteIngredient(container);
            };
            const buttonWrapper = document.createElement("div");
            buttonWrapper.className = "button-wrapper";
            buttonWrapper.appendChild(deleteBtn);
            container.appendChild(inputWrapper);
            container.appendChild(buttonWrapper);
            ingredientsList.appendChild(container);
            updateIngredientNumbers();
    });

    updateIngredientNumbers();

    // Inicializar selects existentes: si tienen valor distinto a 'custom', asignar nombre y readonly
    document.querySelectorAll('.input-ingredient-select').forEach(sel => {
        const container = sel.closest('.input-container');
        if (!container) return;
        const nameInput = container.querySelector('input.input-ingredient');
        if (!nameInput) return;
        const val = sel.value;
        if (val && val !== 'custom') {
            let found = null;
            if (window.ingredientOptions) {
                found = window.ingredientOptions.find(i => (''+i.id) === (''+val));
            }
            nameInput.readOnly = true;
            nameInput.value = found ? found.name : sel.options[sel.selectedIndex].text;
        } else {
            nameInput.readOnly = false;
        }
    });

    // --- Pasos ---
        const updateStepNumbers = () => {
            document.querySelectorAll('#steps-list .input-container').forEach((container, index) => {
                const input = container.querySelector('.input-step');
                if (input) input.placeholder = `Paso ${index + 1}`;
            });
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
            // Input paso
            const input = document.createElement("input");
            input.type = "text";
            input.name = "pasos[]";
            input.className = "input-step input";
            input.placeholder = `Paso ${currentCount}`;
            input.required = true;
            inputWrapper.appendChild(input);
            // Input imagen
            const imgInput = document.createElement("input");
            imgInput.type = "file";
            imgInput.name = `stepImages[]`;
            imgInput.accept = "image/*";
            imgInput.className = "input-step-image";
            inputWrapper.appendChild(imgInput);
            // Botón borrar
            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.className = "delete-item buttono";
            deleteBtn.innerHTML = "&times;";
            deleteBtn.onclick = function() {
                handleDeleteStep(container);
            };
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

// --- Funciones para agregar/borrar ingredientes en la tabla de ingredientes desde el index ---
window.addEventListener('DOMContentLoaded', function() {
    const addIngredientBtn = document.getElementById('addIngredientToTable');
    const ingredientTable = document.getElementById('ingredients-table');
    if (addIngredientBtn && ingredientTable) {
        addIngredientBtn.addEventListener('click', function() {
            const name = prompt('Nombre del nuevo ingrediente:');
            if (name && name.trim() !== '') {
                fetch('add_ingredient.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'name=' + encodeURIComponent(name)
                }).then(res => res.json()).then(data => {
                    if (data.success) location.reload();
                    else alert('Error: ' + data.msj);
                });
            }
        });
        ingredientTable.querySelectorAll('.delete-ingredient-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('¿Eliminar este ingrediente?')) {
                    const id = btn.dataset.id;
                    fetch('delete_ingredient.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'id=' + encodeURIComponent(id)
                    }).then(res => res.json()).then(data => {
                        if (data.success) location.reload();
                        else alert('Error: ' + data.msj);
                    });
                }
            });
        });
    }
});

// Delegated handler: cuando cambia el select de ingrediente, actualizar el input de nombre
document.addEventListener('change', function(e) {
    if (e.target && e.target.classList && e.target.classList.contains('input-ingredient-select')) {
        const select = e.target;
        const container = select.closest('.input-container');
        if (!container) return;
        const nameInput = container.querySelector('input.input-ingredient');
        if (!nameInput) return;
        const val = select.value;
        if (val === 'custom') {
            nameInput.readOnly = false;
            nameInput.value = '';
            nameInput.focus();
        } else {
            // buscar nombre en ingredientOptions si existe
            let found = null;
            if (window.ingredientOptions) {
                found = window.ingredientOptions.find(i => (''+i.id) === (''+val));
            }
            nameInput.readOnly = true;
            nameInput.value = found ? found.name : select.options[select.selectedIndex].text;
        }
    }
});
