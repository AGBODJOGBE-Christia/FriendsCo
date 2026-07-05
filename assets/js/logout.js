document.getElementById('logoutBtn').addEventListener('click', async () => {
    await fetch('../../api/auth/logout.php');
    sessionStorage.clear();
    window.location.href = 'login.html';
});