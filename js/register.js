// register.js
// Maneja el envío del formulario de registro.
// Entrada: los campos del formulario con los nombres userName, userPassword, userEmail.
// Salida: muestra mensajes en el elemento con id "mensaje" y redirige al index en caso de éxito.
// Errores: muestra mensajes de error en el mismo elemento y registra errores de red en la consola.

document.addEventListener("DOMContentLoaded", () => {
  const formRegister = document.getElementById("formRegister");
  const mensajeDiv = document.getElementById("mensaje");

  // Si existe el formulario, interceptamos el submit para enviarlo por fetch
  if (formRegister) {
    formRegister.addEventListener("submit", (e) => {
      e.preventDefault();

      const formData = new FormData(formRegister);

      // Validación básica: todos los campos obligatorios deben estar presentes
      if (
        formData.get("userName") &&
        formData.get("userPassword") &&
        formData.get("userEmail")
      ) {
        // Enviamos los datos al endpoint PHP usando application/x-www-form-urlencoded
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
          .then((res) => res.json())
          .then((res) => {
            // Mostrar resultado devuelto por el servidor
            if (res.success) {
              mensajeDiv.style.color = "green";
              mensajeDiv.textContent = res.msj;
              // Redirigir al index después de un breve retraso
              setTimeout(() => {
                window.location.href = "index.php";
              }, 1000);
            } else {
              mensajeDiv.style.color = "red";
              mensajeDiv.textContent = res.msj;
            }
          })
          .catch((err) => {
            // Manejo de errores de red
            mensajeDiv.style.color = "red";
            mensajeDiv.textContent = "Error de red al registrar.";
            console.error("register fetch error:", err);
          });
      } else {
        // Mensaje cuando faltan campos
        mensajeDiv.style.color = "red";
        mensajeDiv.textContent = "Completa todos los campos.";
      }
    });
  }
});
