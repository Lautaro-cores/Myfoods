document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('ingredientInput');
    const suggestions = document.getElementById('ingredientSuggestions');
    const selectedContainer = document.getElementById('selectedIngredients');
    const addBtn = document.getElementById('addIngredientBtn');

    if (!input || !suggestions || !selectedContainer) return;

    // Ensure window.selectedIngredients is a Set. If another script set it to
    // an array or plain object (persisted across reloads), convert it.
    if (!window.selectedIngredients) {
        window.selectedIngredients = new Set();
    } else if (!(window.selectedIngredients instanceof Set)) {
        try {
            if (Array.isArray(window.selectedIngredients)) {
                window.selectedIngredients = new Set(window.selectedIngredients);
            } else if (typeof window.selectedIngredients === 'object' && window.selectedIngredients !== null && Symbol.iterator in window.selectedIngredients) {
                window.selectedIngredients = new Set(Array.from(window.selectedIngredients));
            } else {
                window.selectedIngredients = new Set();
            }
        } catch (e) {
            // Fallback to empty Set on any error
            window.selectedIngredients = new Set();
        }
    }

    let debounceTimer = null;

    function showSuggestions(items) {
        suggestions.innerHTML = '';
        if (!items || items.length === 0) {
            suggestions.style.display = 'none';
            return;
        }
        items.forEach(it => {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'list-group-item list-group-item-action';
            el.textContent = it.value;
            el.dataset.id = it.id;
            el.addEventListener('click', () => {
                addIngredient(it.id, it.value);
                suggestions.style.display = 'none';
                input.value = '';
            });
            suggestions.appendChild(el);
        });
        suggestions.style.display = 'block';
    }

    async function fetchSuggestions(term) {
        try {
            const res = await fetch(`../getIngredients.php?term=${encodeURIComponent(term)}`);
            if (!res.ok) throw new Error('network');
            const data = await res.json();
            showSuggestions(data);
        } catch (err) {
            console.error('Error fetching ingredient suggestions', err);
            showSuggestions([]);
        }
    }

    input.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        if (debounceTimer) clearTimeout(debounceTimer);
        if (val.length === 0) {
            suggestions.style.display = 'none';
            return;
        }
        debounceTimer = setTimeout(() => fetchSuggestions(val), 250);
    });

    // Añadir con el botón
    addBtn.addEventListener('click', async () => {
        const val = input.value.trim();
        if (!val) return;
        // intentar buscar si hay coincidencias exactas
        try {
            const res = await fetch(`../getIngredients.php?term=${encodeURIComponent(val)}`);
            const data = await res.json();
            const exact = data.find(d => d.value.toLowerCase() === val.toLowerCase());
            if (exact) {
                addIngredient(exact.id, exact.value);
            } else {
                // Si no existe, añadir como ingrediente "custom" usando nombre como id negativo timestamp
                const tempId = `c_${Date.now()}`;
                addIngredient(tempId, val);
            }
            input.value = '';
            suggestions.style.display = 'none';
        } catch (err) {
            console.error(err);
        }
    });

    function renderSelected() {
        selectedContainer.innerHTML = '';
        Array.from(window.selectedIngredients).forEach(entry => {
            // entry can be either numeric id or custom id like c_...
            const [id, name] = entry.split('||');
            const chip = document.createElement('div');
            chip.className = 'badge bg-primary text-white d-inline-flex align-items-center';
            chip.style.padding = '0.5rem';
            chip.textContent = name;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn-close btn-close-white ms-2';
            remove.style.width = '1rem';
            remove.style.height = '1rem';
            remove.addEventListener('click', () => {
                window.selectedIngredients.delete(entry);
                renderSelected();
                triggerSearch();
            });
            chip.appendChild(remove);
            selectedContainer.appendChild(chip);
        });
    }

    function addIngredient(id, name) {
        const key = `${id}||${name}`;
        if (Array.from(window.selectedIngredients).some(e => e.split('||')[0] === String(id))) return;
        window.selectedIngredients.add(key);
        renderSelected();
        triggerSearch();
    }

    function triggerSearch() {
        const searchButton = document.getElementById('searchButton');
        if (searchButton) searchButton.click();
    }

    // Cerrar sugerencias al pulsar fuera
    document.addEventListener('click', (e) => {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.style.display = 'none';
        }
    });
});
