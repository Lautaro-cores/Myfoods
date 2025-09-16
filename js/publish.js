document.addEventListener('DOMContentLoaded', () => {
    const formPublish = document.getElementById('formPublish');
    const mensajeDiv = document.getElementById('mensaje');

    if (formPublish) {
        formPublish.addEventListener("submit", (e) => {
            e.preventDefault();
            const title = document.getElementById("recipeTitle").value;
            fetch("../publishRecipe.php", { 
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "title=" + encodeURIComponent(title)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    mensajeDiv.style.color = "green";
                    mensajeDiv.textContent = res.msj;
                    formPublish.reset();
                } else {
                    mensajeDiv.style.color = "red";
                    mensajeDiv.textContent = res.msj;
                }
            });
        });
    }
});