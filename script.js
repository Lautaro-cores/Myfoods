document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('form-login');
    const formRegistro = document.getElementById('form-registro');
    const mensajeDiv = document.getElementById('mensaje');

    // Lógica para el formulario de registro
    if (formRegistro) {
        formRegistro.addEventListener("submit", (e) => {
            e.preventDefault();
            console.log("Formulario de registro enviado");
            const name = document.getElementById("reg-name").value;
            const password = document.getElementById("reg-password").value;
            const mail = document.getElementById("reg-mail").value;
            if (name && password && mail) {
                fetch("register.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    // Los nombres de los parámetros aquí coinciden con las columnas de la BD
                    body: "name=" + encodeURIComponent(name) + "&password=" + encodeURIComponent(password) + "&mail=" + encodeURIComponent(mail)
                }) 
                .then(res => res.json())
                .then(res => {
                    if (res.error) {
                        mensajeDiv.style.color = "red";
                        mensajeDiv.textContent = res.msj;
                    } else {
                        mensajeDiv.style.color = "green";
                        mensajeDiv.textContent = res.msj;
                        setTimeout(() => {
                            window.location.href = 'publishRecipe.html';
                        }, 1000);
                    }
                });
            } else {
                mensajeDiv.style.color = "red";
                mensajeDiv.textContent = "Completa todos los campos.";
            }
        });
    }

    // Lógica para el formulario de inicio de sesión
    if (formLogin) {
        formLogin.addEventListener("submit", (e) => {
            e.preventDefault();
            const name = document.getElementById("login-name").value;
            const password = document.getElementById("login-password").value;

            if (name && password) {
                fetch("logIn.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    // Los nombres de los parámetros aquí coinciden con las columnas de la BD
                    body: "name=" + encodeURIComponent(name) + "&password=" + encodeURIComponent(password)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.error) {
                        mensajeDiv.style.color = "red";
                        mensajeDiv.textContent = res.msj;
                    } else {
                        mensajeDiv.style.color = "green";
                        mensajeDiv.textContent = res.msj;
                        setTimeout(() => {
                            window.location.href = 'myfoods/visual/publishRecipe.html';
                        }, 1000);
                    }
                });
            } else {
                mensajeDiv.style.color = "red";
                mensajeDiv.textContent = "Completa todos los campos.";
            }
        });
    }
});