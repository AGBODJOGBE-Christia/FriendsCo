document.addEventListener('DOMContentLoaded', () => {
    const user = JSON.parse(sessionStorage.getItem('user'));
    if (!user) window.location.href = 'login.html';

    const form = document.getElementById('profileForm');
    const messageDiv = document.getElementById('message');

    // Charger les infos du profil
    async function loadProfile() {
        const res = await fetch('../../api/users/get_profile.php');
        const data = await res.json();
        if (data.status === 'success') {
            document.getElementById('nom').value = data.data.nom;
            document.getElementById('prenom').value = data.data.prenom;
            document.getElementById('bio').value = data.data.bio || '';
            document.getElementById('avatarPreview').src = data.data.avatar_full;
        }
    }
    loadProfile();

    // Sauvegarder le profil
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const res = await fetch('../../api/users/update_profile.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        messageDiv.innerText = data.message;
        if (data.status === 'success') loadProfile();
    });
});