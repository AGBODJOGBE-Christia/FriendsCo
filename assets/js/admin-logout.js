document.getElementById('adminLogoutBtn').addEventListener('click', async () => {
    await fetch('../../api/auth/logout.php');
    sessionStorage.clear();
    window.location.href = 'admin-login.html';
});