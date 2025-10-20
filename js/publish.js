// publish.js
// Controla el formulario de publicación de recetas: valida campos, envía las imágenes y redirige según la respuesta.

document.addEventListener("DOMContentLoaded", () => {
  const formPublish = document.getElementById("formPublish");
  const mensajeDiv = document.getElementById("mensaje");
  const imagePreview = document.getElementById("imagePreview");
  const imageInput = document.getElementById("imageInput");

  // --- Ingredientes dinámicos ---
  const ingredientsList = document.getElementById("ingredients-list");
  const addIngredienteBtn = document.getElementById("addIngrediente");
  let ingredienteCount = 1;

  // Función para actualizar los placeholders de ingredientes
  const updateIngredientNumbers = () => {
    document.querySelectorAll('#ingredients-list .ingredienteInput').forEach((input, index) => {
      input.placeholder = `Ingrediente ${index + 1}`;
    });
    ingredienteCount = document.querySelectorAll('#ingredients-list .ingredienteInput').length;
  };

  // Función para manejar el borrado de ingredientes
  const handleDeleteIngredient = (container) => {
    const totalIngredients = document.querySelectorAll('#ingredients-list .input-container').length;
    if (totalIngredients > 1) {
      container.remove();
      updateIngredientNumbers();
    } else {
      alert('Debe haber al menos un ingrediente');
    }
  };

  addIngredienteBtn.addEventListener("click", () => {
    ingredienteCount++;
    const container = document.createElement("div");
    container.className = "input-container";
    
    const inputWrapper = document.createElement("div");
    inputWrapper.className = "input-wrapper";
    
    const input = document.createElement("input");
    input.type = "text";
    input.name = "ingredientes[]";
    input.className = "input-ingredient input";
    input.placeholder = `Ingrediente ${ingredienteCount}`;
    input.required = true;
    
    const buttonWrapper = document.createElement("div");
    buttonWrapper.className = "button-wrapper";
    
    const deleteBtn = document.createElement("button");
    deleteBtn.type = "button";
    deleteBtn.className = "delete-item buttono";
    deleteBtn.innerHTML = "&times;";
    deleteBtn.onclick = function() {
      handleDeleteIngredient(container);
    };
    
    inputWrapper.appendChild(input);
    buttonWrapper.appendChild(deleteBtn);
    container.appendChild(inputWrapper);
    container.appendChild(buttonWrapper);
    ingredientsList.appendChild(container);
  });

  // --- Pasos dinámicos ---
  const stepsList = document.getElementById("steps-list");
  const addPasoBtn = document.getElementById("addPaso");
  let pasoCount = 1;

  // Función para actualizar los números de los pasos
  const updateStepNumbers = () => {
    document.querySelectorAll('#steps-list .pasoInput').forEach((input, index) => {
      input.placeholder = `Paso ${index + 1}`;
    });
    pasoCount = document.querySelectorAll('#steps-list .pasoInput').length;
  };

  // Función para manejar el borrado de pasos
  const handleDeleteStep = (container) => {
    const totalSteps = document.querySelectorAll('#steps-list .input-container').length;
    if (totalSteps > 1) {
      container.remove();
      updateStepNumbers();
    } else {
      alert('Debe haber al menos un paso');
    }
  };

  addPasoBtn.addEventListener("click", () => {
    pasoCount++;
    const container = document.createElement("div");
    container.className = "input-container";
    
    const inputWrapper = document.createElement("div");
    inputWrapper.className = "input-wrapper";
    
    const input = document.createElement("input");
    input.type = "text";
    input.name = "pasos[]";
    input.className = "input-step input";
    input.placeholder = `Paso ${pasoCount}`;
    input.required = true;
    
    const buttonWrapper = document.createElement("div");
    buttonWrapper.className = "button-wrapper";
    
    const deleteBtn = document.createElement("button");
    deleteBtn.type = "button";
    deleteBtn.className = "delete-item buttono";
    deleteBtn.innerHTML = "&times;";
    deleteBtn.onclick = function() {
      handleDeleteStep(container);
    };
    
    inputWrapper.appendChild(input);
    buttonWrapper.appendChild(deleteBtn);
    container.appendChild(inputWrapper);
    container.appendChild(buttonWrapper);
    stepsList.appendChild(container);
  });

  // --- Manejo de imágenes ---
  if (imageInput) {
    imageInput.addEventListener("change", () => {
      // Limpiar preview existente
      imagePreview.innerHTML = "";

      // Validar número máximo de imágenes
      if (imageInput.files.length > 3) {
        mensajeDiv.style.color = "red";
        mensajeDiv.textContent = "Máximo 3 imágenes permitidas";
        imageInput.value = "";
        return;
      }

      // Crear previews para cada imagen
      Array.from(imageInput.files).forEach((file, index) => {
        const reader = new FileReader();
        const container = document.createElement("div");
        container.className = "preview-container";
        
        reader.onload = (e) => {
          container.innerHTML = `
            <img src="${e.target.result}" alt="Vista previa ${index + 1}">
            <button type="button" class="remove-image" data-index="${index}">&times;</button>
          `;
          imagePreview.appendChild(container);
        };
        reader.readAsDataURL(file);
      });
    });

    // Manejar eliminación de imágenes
    imagePreview.addEventListener("click", (e) => {
      if (e.target.classList.contains("remove-image")) {
        const index = parseInt(e.target.dataset.index);
        const dt = new DataTransfer();
        const { files } = imageInput;
        
        for (let i = 0; i < files.length; i++) {
          if (i !== index) {
            dt.items.add(files[i]);
          }
        }
        
        imageInput.files = dt.files;
        e.target.parentElement.remove();
        
        // Reindexar los botones de eliminación restantes
        document.querySelectorAll(".remove-image").forEach((btn, idx) => {
          btn.dataset.index = idx;
        });
      }
    });
  }

  if (!formPublish) return;

  formPublish.addEventListener("submit", (e) => {
    e.preventDefault();

    const title = document.getElementById("recipeTitle").value.trim();
    const description = document.getElementById("recipeDescription").value.trim();

    // Validación básica de campos obligatorios
    if (!title || !description) {
      mensajeDiv.style.color = "red";
      mensajeDiv.textContent = "Completa título y descripción.";
      return;
    }

    // Validar que exista al menos una imagen
    if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
      mensajeDiv.style.color = "red";
      mensajeDiv.textContent = "Debes seleccionar al menos una imagen para la receta.";
      return;
    }

    // Validar máximo de imágenes
    if (imageInput.files.length > 3) {
      mensajeDiv.style.color = "red";
      mensajeDiv.textContent = "Máximo 3 imágenes permitidas.";
      return;
    }

    // Obtener ingredientes y pasos
    const ingredientes = Array.from(document.querySelectorAll(".input-ingredient"))
      .map((i) => i.value.trim())
      .filter(Boolean);
    const pasos = Array.from(document.querySelectorAll(".input-step"))
      .map((i) => i.value.trim())
      .filter(Boolean);

    if (ingredientes.length === 0 || pasos.length === 0) {
      mensajeDiv.style.color = "red";
      mensajeDiv.textContent = "Agrega al menos un ingrediente y un paso.";
      return;
    }

    // Construir FormData para envío
    const formData = new FormData();
    formData.append("title", title);
    formData.append("description", description);
    ingredientes.forEach((ing) => formData.append("ingredientes[]", ing));
    pasos.forEach((paso) => formData.append("pasos[]", paso));
    
    // Agregar todas las imágenes seleccionadas
    Array.from(imageInput.files).forEach((file) => {
      formData.append("recipeImages[]", file);
    });

    fetch("../publishRecipe.php", { method: "POST", body: formData })
      .then((res) => res.json())
      .then((res) => {
        if (res.success) {
          mensajeDiv.style.color = "green";
          mensajeDiv.textContent = res.msj;
          formPublish.reset();
          imagePreview.innerHTML = "";
          
          // Redirigir a la receta publicada
          if (res.postId) {
            setTimeout(() => {
              window.location.href = `viewRecipe.php?id=${res.postId}`;
            }, 600);
          } else {
            setTimeout(() => {
              window.location.href = "index.php";
            }, 800);
          }
        } else {
          console.error("Error del servidor:", res);
          mensajeDiv.style.color = "red";
          mensajeDiv.textContent = res.msj || "Error al publicar.";
        }
      })
      .catch((err) => {
        console.error("Error en la publicación:", err);
        mensajeDiv.style.color = "red";
        mensajeDiv.textContent = "Error de red al publicar. Intenta más tarde.";
      });
  });
});
