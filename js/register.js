document.addEventListener("DOMContentLoaded", () => {
  const formRegister = document.getElementById("formRegister");
  const mensajeDiv = document.getElementById("mensaje");

  // Si el formulario de registro está presente, se configura el evento de envío
  if (formRegister) {
    formRegister.addEventListener("submit", (e) => {
      // Evita que el formulario se envíe por el método predeterminado
      e.preventDefault();

      const formData = new FormData(formRegister);

      // Verifica que los tres campos requeridos tengan valores
      if (
        formData.get("userName") &&
        formData.get("userPassword") &&
        formData.get("userEmail")
      ) {
        // Envía los datos al archivo register.php mediante una solicitud POST
        fetch("../register.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body:
            "userName=" +
            encodeURIComponent(formData.get("userName")) +
            "&userPassword=" +
            encodeURIComponent(formData.get("userPassword")) +
            "&userEmail=" +
            encodeURIComponent(formData.get("userEmail")),
        })
          // Convierte la respuesta de register.php en formato JSON
          .then((res) => res.json())
          .then((res) => {
            // Si register.php devuelve éxito, muestra el mensaje y redirige a index.php
            if (res.success) {
              mensajeDiv.style.color = "green";
              mensajeDiv.textContent = res.msj;
              setTimeout(() => {
                window.location.href = "index.php";
              }, 1000);
            } else {
              // Si register.php devuelve error, muestra el mensaje de error
              mensajeDiv.style.color = "red";
              mensajeDiv.textContent = res.msj;
            }
          })
          // Captura errores de red o fallos en la solicitud
          .catch((err) => {
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Error de red al registrar.";
            console.error("register fetch error:", err);
          });
      } else {
        // Si faltan campos, muestra un mensaje indicándolo
        mensajeDiv.style.color = "red";
        mensajeDiv.textContent = "Completa todos los campos.";
      }
    });
  }
});
