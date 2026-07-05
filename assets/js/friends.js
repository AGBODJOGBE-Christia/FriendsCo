document.addEventListener('DOMContentLoaded', () => {
    if (!sessionStorage.getItem('user')) window.location.href = 'login.html';

    const list = document.getElementById('usersList');
    loadUsers();

    async function loadUsers() {
        const res = await fetch('../../api/users/list_users.php');
        const data = await res.json();
        list.innerHTML = '';

        data.data.forEach(u => {
            let actionBtn = '';
            const avatar = u.avatar ? '../../' + u.avatar : 'https://via.placeholder.com/40';

            if (u.friendship_status === 'accepted') {
                actionBtn = `<button class="friend-action" disabled>✅ Amis</button>`;
            } else if (u.friendship_status === 'pending') {
                if (u.direction === 'sent') {
                    actionBtn = `<button class="friend-action" onclick="manageFriend(${u.id}, 'cancel')">❌ Annuler</button>`;
                } else if (u.direction === 'received') {
                    actionBtn = `
                        <button class="friend-action" onclick="manageFriend(${u.id}, 'accept')">✅ Accepter</button>
                        <button class="friend-action" onclick="manageFriend(${u.id}, 'reject')">❌ Refuser</button>
                    `;
                }
            } else {
                actionBtn = `<button class="friend-action" onclick="manageFriend(${u.id}, 'send')">➕ Ajouter</button>`;
            }

            list.innerHTML += `
                <div class="user-row">
                    <img src="${avatar}" class="mini-avatar">
                    <span>${u.prenom} ${u.nom}</span>
                    <div>${actionBtn}</div>
                </div>
            `;
        });
    }

    window.manageFriend = async (targetId, action) => {
        const res = await fetch('../../api/users/manage_friend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ target_id: targetId, action: action })
        });
        if (res.ok){
            document.getElementById('usersList').innerText = `<p style="text-align: center; color: #94a3b8;">Chargement...</p>`;
            loadUsers();
        }
    };
});