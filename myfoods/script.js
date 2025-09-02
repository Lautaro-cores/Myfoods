document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('form-login');
    const formRegistro = document.getElementById('form-registro');
    const mensajeDiv = document.getElementById('mensaje');

    // Lógica para el formulario de registro
    if (formRegistro) {
        formRegistro.addEventListener("submit", (e) => {
            e.preventDefault();
            const nombre = document.getElementById("reg-nombre").value;
            const contrasena = document.getElementById("reg-contrasena").value;
            const correo = document.getElementById("reg-correo").value;
        
            if (nombre && contrasena && correo) {
                fetch("/Registrarse.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    // Los nombres de los parámetros aquí coinciden con las columnas de la BD
                    body: "nombre=" + encodeURIComponent(nombre) + "&contrasena=" + encodeURIComponent(contrasena) + "&correo=" + encodeURIComponent(correo)
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
                            window.location.href = 'visual/publicar_receta.html';
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
            const nombre = document.getElementById("login-nombre").value;
            const contrasena = document.getElementById("login-contrasena").value;

            if (nombre && contrasena) {
                fetch("InicioSesion.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    // Los nombres de los parámetros aquí coinciden con las columnas de la BD
                    body: "nombre=" + encodeURIComponent(nombre) + "&contrasena=" + encodeURIComponent(contrasena)
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
                            window.location.href = 'myfoods/visual/publicar_receta.html';
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