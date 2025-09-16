document.addEventListener('DOMContentLoaded', () => {
    const formRegister = document.getElementById('formRegister');
    const mensajeDiv = document.getElementById('mensaje');

    if (formRegister) {
        formRegister.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(formRegister);
            if (formData.get("userName") && formData.get("userPassword") && formData.get("userEmail")) {
                fetch("../register.php", { 
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "userName=" + encodeURIComponent(formData.get("userName")) + "&userPassword=" + encodeURIComponent(formData.get("userPassword")) + "&userEmail=" + encodeURIComponent(formData.get("userEmail"))
                }) 
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        mensajeDiv.style.color = "green";
                        mensajeDiv.textContent = res.msj;
                        setTimeout(() => {
                            window.location.href = "publishRecipe.php";
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