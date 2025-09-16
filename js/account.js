document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('formImage');
    const userImageInput = document.getElementById('userImageInput');
    const profileImage = document.querySelector('img[alt="Imagen de perfil"]');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('userImage', userImageInput.files[0]);

        const response = await fetch('uploadImage.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            profileImage.src = 'getUserImage.php?' + new Date().getTime();
            alert(result.msj);
        } else {
            alert(result.msj);
        }
    });
});