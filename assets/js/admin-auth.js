document.getElementById('adminLoginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const res = await fetch('../../api/admin/admin_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });
    const data = await res.json();

    if (data.status === 'success') {
        sessionStorage.setItem('user', JSON.stringify(data.user));
        window.location.href = 'dashboard.html';
    } else {
        document.getElementById('loginMessage').innerText = data.message;
    }
});