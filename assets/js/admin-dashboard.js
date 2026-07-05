document.addEventListener('DOMContentLoaded', () => {
    const userData = JSON.parse(sessionStorage.getItem('user'));
    if (!userData || (userData.role !== 'admin' && userData.role !== 'moderator')) {
        window.location.href = 'admin-login.html';
        return;
    }

    document.getElementById('roleDisplay').innerText = `Rôle : ${userData.role}`;

    // Charger les stats
    loadStats();
    // Charger les utilisateurs
    loadUsers();
    // Charger les posts
    loadPosts();

    async function loadStats() {
        const res = await fetch('../../api/admin/get_stats.php');
        const data = await res.json();
        if (data.status === 'success') {
            document.getElementById('statUsers').innerText = data.data.total_users;
            document.getElementById('statPosts').innerText = data.data.total_posts;
            document.getElementById('statComments').innerText = data.data.total_comments;
            document.getElementById('statMessages').innerText = data.data.total_messages;
        }
    }

    async function loadUsers() {
        const res = await fetch('../../api/admin/manage_users.php');
        const data = await res.json();
        const tbody = document.querySelector('#usersTable tbody');
        tbody.innerHTML = '';

        data.data.forEach(u => {
            let actionBtns = `<button onclick="deleteUser(${u.id})" class="btn-danger">Supprimer</button>`;
            
            // Si l'utilisateur connecté est ADMIN, il peut promouvoir
            if (userData.role === 'admin' && u.role === 'user') {
                actionBtns += `<button onclick="promoteUser(${u.id})" class="btn-success">Promouvoir Modérateur</button>`;
            }

            tbody.innerHTML += `
                <tr>
                    <td>${u.id}</td>
                    <td>${u.prenom} ${u.nom}</td>
                    <td>${u.email}</td>
                    <td><span class="role-badge">${u.role}</span></td>
                    <td>${actionBtns}</td>
                </tr>
            `;
        });
    }

    async function loadPosts() {
        const res = await fetch('../../api/admin/manage_posts.php');
        const data = await res.json();
        const tbody = document.querySelector('#postsTable tbody');
        tbody.innerHTML = '';

        data.data.forEach(p => {
            const content = p.content.length > 50 ? p.content.substring(0, 50) + '...' : p.content;
            tbody.innerHTML += `
                <tr>
                    <td>${p.prenom} ${p.nom}</td>
                    <td>${content}</td>
                    <td>${p.created_at}</td>
                    <td><button onclick="deletePost(${p.id})" class="btn-danger">Supprimer</button></td>
                </tr>
            `;
        });
    }

    // Fonctions exposées au global pour les événements onclick
    window.deleteUser = async (id) => {
        if (confirm('Voulez-vous vraiment supprimer cet utilisateur et toutes ses données ?')) {
            await fetch('../../api/admin/manage_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ target_id: id, action: 'delete_user' })
            });
            loadUsers();
            loadStats(); // Mettre à jour les stats
        }
    };

    window.promoteUser = async (id) => {
        if (confirm('Promouvoir cet utilisateur en Modérateur ?')) {
            await fetch('../../api/admin/manage_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ target_id: id, action: 'promote_moderator' })
            });
            loadUsers();
        }
    };

    window.deletePost = async (id) => {
        if (confirm('Supprimer cette publication ?')) {
            await fetch('../../api/admin/manage_posts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: id })
            });
            loadPosts();
            loadStats();
        }
    };
    // --- GESTION POP-UP AJOUT STAFF (ADMIN UNIQUEMENT) ---

const modal = document.getElementById('addStaffModal');
const openBtn = document.getElementById('openAddStaffBtn');
const closeBtn = document.getElementById('closeModalBtn');
const staffForm = document.getElementById('addStaffForm');
const staffMsg = document.getElementById('staffMessage');

//  pour afficher le bouton "+" uniquement si l'utilisateur est ADMIN
if (userData.role === 'admin') {
    openBtn.style.display = 'block';
}

// pour ouvrir la modale
openBtn.addEventListener('click', () => {
    modal.style.display = 'flex';
    staffMsg.style.display = 'none'; // Cacher les vieux messages
    staffForm.reset();
});

//pour fermer la modale 
closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });
modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none'; // Fermer si clic sur le fond noir
});

//pour gérer la soumission du formulaire
staffForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nom = document.getElementById('new_nom').value;
    const prenom = document.getElementById('new_prenom').value;
    const email = document.getElementById('new_email').value;
    const role = document.getElementById('new_role').value;

    // Le backend fera le hash du mot de passe par défaut "admin123"
    const action = role === 'admin' ? 'add_admin' : 'add_moderator';

    const res = await fetch('../../api/admin/manage_users.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: action, nom: nom, prenom: prenom, email: email })
    });
    const data = await res.json();

    // Affichage du message de succès/erreur DANS la pop-up
    staffMsg.innerText = data.message;
    staffMsg.className = 'message-box ' + (data.status === 'success' ? 'success' : 'error');
    staffMsg.style.display = 'block';

    if (data.status === 'success') {
        staffForm.reset();
        loadUsers(); // Recharger le tableau des utilisateurs en arrière-plan
        setTimeout(() => {
            modal.style.display = 'none'; // Fermer la pop-up automatiquement après 2 secondes
        }, 2000);
    }
});
});