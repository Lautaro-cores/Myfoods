document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('formLogin');
    const mensajeDiv = document.getElementById('mensaje');

    if (formLogin) {
        formLogin.addEventListener("submit", (e) => {
            e.preventDefault();
            const userName = document.getElementById("loginUserName").value;
            const userPassword = document.getElementById("loginUserPassword").value;

            if (userName && userPassword) {
                fetch("logIn.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "userName=" + encodeURIComponent(userName) + "&userPassword=" + encodeURIComponent(userPassword)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        mensajeDiv.style.color = "green";
                        mensajeDiv.textContent = res.msj;
                        setTimeout(() => {
                            window.location.href = "publishRecipe.html";
                        }, 1000);
                    } else {
                        mensajeDiv.style.color = "red";
                        mensajeDiv.textContent = res.msj;
                    }
                });
            } else {
                mensajeDiv.style.color = "red";
                mensajeDiv.textContent = "Completa todos los campos.";
            }
        });
    }
});
