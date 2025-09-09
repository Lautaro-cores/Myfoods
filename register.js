document.addEventListener('DOMContentLoaded', () => {
    const formRegister = document.getElementById('formRegister');
    const mensajeDiv = document.getElementById('mensaje');

    if (formRegister) {
        formRegister.addEventListener("submit", (e) => {
            e.preventDefault();
            const userName = document.getElementById("regUserName").value;
            const userPassword = document.getElementById("regUserPassword").value;
            const userEmail = document.getElementById("regUserEmail").value;
            if (userName && userPassword && userEmail) {
                fetch("register.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "userName=" + encodeURIComponent(userName) + "&userPassword=" + encodeURIComponent(userPassword) + "&userEmail=" + encodeURIComponent(userEmail)
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