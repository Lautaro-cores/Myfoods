// account.js
// este archivo maneja la edicion del perfil del usuario


document.addEventListener("DOMContentLoaded", () => {
  // Obtiene los elementos del DOM necesarios para manejar la edición del perfil
  const form = document.getElementById("formImage");
  const userImageInput = document.getElementById("subirArchivo");
  const profileImage = document.querySelector('img[alt="Imagen de perfil"]');
  const editProfileModalEl = document.getElementById("editProfileModal");
  const editProfileModal = editProfileModalEl ? new bootstrap.Modal(editProfileModalEl) : null;
  const imagePreview = document.getElementById("imagePreview");
  const descriptionField = document.getElementById("description");
  const displayNameField = document.getElementById("displayName");

  if (!form) return;

  // muestra una vista previa de la imagen seleccionada antes de subirla
  if (userImageInput && imagePreview) {
    userImageInput.addEventListener("change", () => {
      imagePreview.innerHTML = "";
      const file = userImageInput.files && userImageInput.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.alt = "Vista previa";
        img.style.maxWidth = "200px";
        img.style.borderRadius = "8px";
        imagePreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }

  // limpia los campos del modal al cerrarse
  if (editProfileModalEl) {
    editProfileModalEl.addEventListener("hidden.bs.modal", () => {
      if (imagePreview) imagePreview.innerHTML = "";
      if (userImageInput) userImageInput.value = "";
    });
  }

  // al hacer submit en el formulario crea un formdata
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    formData.set("description", descriptionField ? descriptionField.value.trim() : "");
    formData.set("displayName", displayNameField ? displayNameField.value.trim() : "");

    try {
      // envia el formulario al uploadProfile.php para actualizar los datos
      const response = await fetch("../uploadProfile.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      // si responde con éxito, actualiza la interfaz sin recargar la página
      if (result.success) {
        profileImage.src = `../getUserImage.php?ts=${new Date().getTime()}`;

        const displayNameElement = document.querySelector(".profile-details h3");
        const newDisplayName = displayNameField ? displayNameField.value.trim() : "";
        if (displayNameElement && newDisplayName) {
          displayNameElement.textContent = newDisplayName;
        }

        const descriptionElement = document.querySelector(".user-description");
        const newDescription = descriptionField ? descriptionField.value.trim() : "";
        if (descriptionElement) {
          descriptionElement.textContent = newDescription || "Sin descripción";
        }

        // oculta el modal al finalizar la actualización
        if (editProfileModal) editProfileModal.hide();
      } else {
        alert(result.message || "Error al actualizar perfil");
      }
    } catch (err) {
      // maneja errores de red o de formato JSON
      console.error("Error en fetch upload:", err);
      alert("Error al comunicarse con el servidor o JSON inválido.");
    }
  });
});
