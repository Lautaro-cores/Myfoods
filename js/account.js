// account.js
// Gestiona la subida de la imagen de perfil desde la vista de cuenta.
// Reemplaza la imagen mostrada en la página con la nueva URL devuelta por el servidor.

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formImage");
  const userImageInput = document.getElementById("subirArchivo");
  const profileImage = document.querySelector('img[alt="Imagen de perfil"]');

  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!userImageInput || !userImageInput.files || userImageInput.files.length === 0) {
      alert("Selecciona un archivo primero.");
      return;
    }

    const formData = new FormData();
    formData.append("userImage", userImageInput.files[0]);

    try {
      const response = await fetch("../uploadImage.php", { method: "POST", body: formData });
      const result = await response.json();

      if (result.success) {
        // Si el servidor devuelve una URL directa, usarla; si no, forzar recarga desde getUserImage.php
        if (result.imageUrl) profileImage.src = result.imageUrl;
        else profileImage.src = "../getUserImage.php?" + new Date().getTime();

        alert(result.msj);
      } else {
        alert(result.msj || "Error al subir imagen");
      }
    } catch (err) {
      console.error("Error subiendo imagen:", err);
      alert("Error de red al subir la imagen.");
    }
  });
});
