// account.js
// Gestiona la subida de la imagen de perfil desde la vista de cuenta.
// Reemplaza la imagen mostrada en la página con la nueva URL devuelta por el servidor.

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formImage");
  const userImageInput = document.getElementById("subirArchivo");
  const profileImage = document.querySelector('img[alt="Imagen de perfil"]');
  const editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
  const imagePreview = document.getElementById('imagePreview');

  if (!form) return;


  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (
      !userImageInput ||
      !userImageInput.files ||
      userImageInput.files.length === 0
    ) {
      alert("Selecciona un archivo primero.");
      return;
    }

    const formData = new FormData();
    formData.append("userImage", userImageInput.files[0]);

    try {
      const response = await fetch("../uploadImage.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        // Si el servidor devuelve una URL directa, usarla; si no, forzar recarga desde getUserImage.php
        if (result.imageUrl) profileImage.src = result.imageUrl;
        else profileImage.src = "../getUserImage.php?" + new Date().getTime();

        editProfileModal.hide(); // Cerrar el modal después de subir la imagen
        alert(result.msj);
      } else {
        alert(result.msj || "Error al subir imagen");
      }
    } catch (err) {
      console.error("Error subiendo imagen:", err);
      alert("Error de red al subir la imagen.");
    }
  });

  // Preview logic: show selected file(s) as preview(s)
  if (userImageInput && imagePreview) {
    userImageInput.addEventListener('change', () => {
      // Clear previous preview
      imagePreview.innerHTML = '';
      const file = userImageInput.files && userImageInput.files[0];
      if (!file) return;

      const reader = new FileReader();
      const container = document.createElement('div');
      container.className = 'preview-image';

      reader.onload = (e) => {
        container.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
        imagePreview.appendChild(container);
      };

      reader.readAsDataURL(file);
    });
  }

  // When modal is hidden, clear preview & file input
  const modalEl = document.getElementById('editProfileModal');
  modalEl.addEventListener('hidden.bs.modal', () => {
    if (imagePreview) imagePreview.innerHTML = '';
    if (userImageInput) userImageInput.value = '';
  });
});
