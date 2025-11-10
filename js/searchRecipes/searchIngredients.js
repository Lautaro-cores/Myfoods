document.addEventListener('DOMContentLoaded', () => {
    // Obtiene referencias a los elementos clave del DOM
    const input = document.getElementById('ingredientInput');
    const suggestions = document.getElementById('ingredientSuggestions');
    const selectedContainer = document.getElementById('selectedIngredients');
    const addBtn = document.getElementById('addIngredientBtn');

    // Detiene la ejecución si los elementos esenciales no existen
    if (!input || !suggestions || !selectedContainer) return;

    // Lógica para inicializar window.selectedIngredients como un objeto Set
    if (!window.selectedIngredients) {
        window.selectedIngredients = new Set();
    } else if (!(window.selectedIngredients instanceof Set)) {
        // Intenta convertir variables globales preexistentes (si no son un Set) a un Set
        try {
            if (Array.isArray(window.selectedIngredients)) {
                window.selectedIngredients = new Set(window.selectedIngredients);
            } else if (typeof window.selectedIngredients === 'object' && window.selectedIngredients !== null && Symbol.iterator in window.selectedIngredients) {
                window.selectedIngredients = new Set(Array.from(window.selectedIngredients));
            } else {
                window.selectedIngredients = new Set();
            }
        } catch (e) {
            // En caso de error de conversión, usa un Set vacío
            window.selectedIngredients = new Set();
        }
    }

    let debounceTimer = null;

    // Función para renderizar y mostrar la lista de sugerencias obtenida del servidor
    function showSuggestions(items) {
        suggestions.innerHTML = '';
        // Oculta la lista si no hay elementos
        if (!items || items.length === 0) {
            suggestions.style.display = 'none';
            return;
        }
        // Crea un botón (elemento de lista) para cada sugerencia
        items.forEach(it => {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'list-group-item list-group-item-action';
            el.textContent = it.value;
            el.dataset.id = it.id;
            // Al hacer clic, se añade el ingrediente seleccionado a la lista y se limpia el input
            el.addEventListener('click', () => {
                addIngredient(it.id, it.value);
                suggestions.style.display = 'none';
                input.value = '';
            });
            suggestions.appendChild(el);
        });
        suggestions.style.display = 'block';
    }

    // Función asíncrona para obtener sugerencias de ingredientes del servidor
    async function fetchSuggestions(term) {
        try {
            // Realiza la petición al endpoint PHP con el término de búsqueda codificado
            const res = await fetch(`../getIngredients.php?term=${encodeURIComponent(term)}`);
            if (!res.ok) throw new Error('network');
            const data = await res.json();
            showSuggestions(data);
        } catch (err) {
            // Maneja errores de la red o del servidor
            console.error('Error al obtener sugerencias de ingredientes', err);
            showSuggestions([]);
        }
    }

    // Maneja la entrada de texto para la funcionalidad de 'debounce' (retraso en la búsqueda)
    input.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        // Limpia el temporizador anterior para evitar peticiones redundantes
        if (debounceTimer) clearTimeout(debounceTimer);
        // Si el campo está vacío, oculta las sugerencias
        if (val.length === 0) {
            suggestions.style.display = 'none';
            return;
        }
        // Establece un nuevo temporizador para llamar a la función de búsqueda después de 250ms
        debounceTimer = setTimeout(() => fetchSuggestions(val), 250);
    });

    // Lógica para añadir un ingrediente usando el botón manual
    addBtn.addEventListener('click', async () => {
        const val = input.value.trim();
        if (!val) return;
        
        // Se intenta buscar el ingrediente en el servidor para verificar si existe una coincidencia exacta
        try {
            const res = await fetch(`../getIngredients.php?term=${encodeURIComponent(val)}`);
            const data = await res.json();
            // Busca una coincidencia exacta, ignorando mayúsculas/minúsculas
            const exact = data.find(d => d.value.toLowerCase() === val.toLowerCase());
            if (exact) {
                // Si existe, se añade el ingrediente con su ID real
                addIngredient(exact.id, exact.value);
            } else {
                // Si no existe, se añade como ingrediente personalizado con un ID temporal
                const tempId = `c_${Date.now()}`;
                addIngredient(tempId, val);
            }
            // Limpia el input y oculta las sugerencias
            input.value = '';
            suggestions.style.display = 'none';
        } catch (err) {
            console.error(err);
        }
    });

    // Función para dibujar los ingredientes actualmente seleccionados en el contenedor
    function renderSelected() {
        selectedContainer.innerHTML = '';
        // Itera sobre el Set de ingredientes seleccionados
        Array.from(window.selectedIngredients).forEach(entry => {
            // El formato de entry es "id||nombre"
            const [id, name] = entry.split('||');
            // Crea el 'chip' visual para el ingrediente
            const chip = document.createElement('div');
            chip.className = 'badge bg-primary text-white d-inline-flex align-items-center';
            chip.style.padding = '0.5rem';
            chip.textContent = name;
            // Crea el botón para eliminar el ingrediente
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn-close btn-close-white ms-2';
            // Configura el evento de eliminación
            remove.addEventListener('click', () => {
                window.selectedIngredients.delete(entry);
                renderSelected();
                triggerSearch(); // Dispara la búsqueda después de eliminar
            });
            chip.appendChild(remove);
            selectedContainer.appendChild(chip);
        });
    }

    // Función para añadir un ingrediente al Set de seleccionados
    function addIngredient(id, name) {
        const key = `${id}||${name}`;
        // Previene la adición si el ID ya está en el Set
        if (Array.from(window.selectedIngredients).some(e => e.split('||')[0] === String(id))) return;
        window.selectedIngredients.add(key);
        renderSelected();
        triggerSearch(); // Dispara la búsqueda después de añadir
    }

    // Simula un clic en el botón de búsqueda principal para refrescar los resultados
    function triggerSearch() {
        const searchButton = document.getElementById('searchButton');
        if (searchButton) searchButton.click();
    }

    // Event listener para cerrar las sugerencias al hacer clic fuera del input o la lista
    document.addEventListener('click', (e) => {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.style.display = 'none';
        }
    });
});