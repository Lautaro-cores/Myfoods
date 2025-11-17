// logIn.js
// este archivo maneja la funcionalidad de inicio de sesión de usuarios

document.addEventListener("DOMContentLoaded", () => {
  // obtiene referencias al formulario de inicio de sesión y al div de mensajes
  const formLogin = document.getElementById("formLogin");
  const mensajeDiv = document.getElementById("mensaje");

  if (!formLogin) return;

  formLogin.addEventListener("submit", (e) => {
    e.preventDefault();

    // obtiene los valores de los campos de nombre de usuario y contraseña
    const userName = document.getElementById("loginUserName").value;
    const userPassword = document.getElementById("loginUserPassword").value;

    // verifica que ambos campos tengan valor
    if (!userName || !userPassword) {
      mensajeDiv.style.color = "red";
      mensajeDiv.textContent = "Completa todos los campos.";
      return;
    }

    // envia al logIn.php los valores para iniciar sesión
    fetch("../logIn.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:
        "userName=" +
        encodeURIComponent(userName) +
        "&userPassword=" +
        encodeURIComponent(userPassword),
    })
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
