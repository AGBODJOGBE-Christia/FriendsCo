document.addEventListener('DOMContentLoaded', () => {
    const apiBase = '../../api/auth/'; // Chemin relatif depuis la vue HTML

    // --- LOGIN ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const response = await fetch(apiBase + 'login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();

            if (data.status === 'success') {
                // Stocker les infos utilisateur dans sessionStorage
                sessionStorage.setItem('user', JSON.stringify(data.user));
                // Redirection vers l'accueil ou l'admin selon le rôle
                 window.location.href = '../clients/accueil.html';  
            } else {
            document.getElementById('message').innerText = data.message;
            }
        });
    }
    
    // --- REGISTER ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const nom = document.getElementById('nom').value;
            const prenom = document.getElementById('prenom').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const response = await fetch(apiBase + 'register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nom, prenom, email, password })
            });
            const data = await response.json();
            document.getElementById('message').innerText = data.message;
            if (data.status === 'success') {
                setTimeout(() => window.location.href = 'login.html', 2000);
            }
        });
    }

    // --- FORGOT PASSWORD ---
    const forgotForm = document.getElementById('forgotForm');
    if (forgotForm) {
        forgotForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const response = await fetch(apiBase + 'forgot_password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await response.json();
            document.getElementById('message').innerText = data.message;
        });
    }

    // --- RESET PASSWORD ---
    const resetForm = document.getElementById('resetForm');
    if (resetForm) {
        resetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const token = document.getElementById('token').value;
            const new_password = document.getElementById('new_password').value;
            
            const response = await fetch(apiBase + 'reset_password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token, new_password })
            });
            const data = await response.json();
            document.getElementById('message').innerText = data.message;
            if (data.status === 'success') {
                setTimeout(() => window.location.href = 'login.html', 2000);
            }
        });
    }
});