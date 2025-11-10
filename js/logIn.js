document.addEventListener("DOMContentLoaded", () => {
  const formLogin = document.getElementById("formLogin");
  const mensajeDiv = document.getElementById("mensaje");

  // Si el formulario no existe en la página, se detiene la ejecución
  if (!formLogin) return;

  formLogin.addEventListener("submit", (e) => {
    // Evita que el formulario se envíe por el método predeterminado
    e.preventDefault();

    const userName = document.getElementById("loginUserName").value;
    const userPassword = document.getElementById("loginUserPassword").value;

    // Verifica que ambos campos estén completos antes de continuar
    if (!userName || !userPassword) {
      mensajeDiv.style.color = "red";
      mensajeDiv.textContent = "Completa todos los campos.";
      return;
    }

    // Envía los datos del formulario al archivo logIn.php mediante una solicitud POST
    fetch("../logIn.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:
        "userName=" +
        encodeURIComponent(userName) +
        "&userPassword=" +
        encodeURIComponent(userPassword),
    })
      // Convierte la respuesta de logIn.php a formato JSON
      .then((res) => res.json())
      .then((res) => {
        // Si logIn.php devuelve éxito, muestra el mensaje y redirige a la página principal
        if (res.success) {
          mensajeDiv.style.color = "green";
          mensajeDiv.textContent = res.msj;
          setTimeout(() => {
            window.location.href = "index.php";
          }, 1000);
        } else {
          // Si logIn.php devuelve error, muestra el mensaje correspondiente
          mensajeDiv.style.color = "red";
          mensajeDiv.textContent = res.msj;
        }
      })
      // Captura errores de red o fallos al intentar obtener la respuesta
      .catch((err) => {
        console.error("Error en login fetch:", err);
        mensajeDiv.style.color = "red";
        mensajeDiv.textContent = "Error de red al iniciar sesión.";
      });
  });
});
