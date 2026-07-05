document.addEventListener('DOMContentLoaded', () => {
    const feedContainer = document.getElementById('feed');
    const postForm = document.getElementById('postForm');
    const apiBase = '../../api/posts/';

    // Vérifier si l'utilisateur est connecté
    const userData = JSON.parse(sessionStorage.getItem('user'));
    if (!userData) {
        window.location.href = 'login.html';
        return;
    }
    // Si le rôle est admin ou moderator, on affiche le bouton dans la nav
    const adminLink = document.getElementById('adminAccessLink');
    if (adminLink && (userData.role === 'admin' || userData.role === 'moderator')) {
        adminLink.style.display = 'inline-block';
    }

    // Charger le feed au démarrage
    loadFeed();

    //Créer un post
    postForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('content', document.getElementById('postContent').value);
        const imageInput = document.getElementById('postImage');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }

        const response = await fetch(apiBase + 'create_post.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.status === 'success') {
            postForm.reset();
            loadFeed(); // Recharger le feed après publication
        } else {
            alert(data.message);
        }
    });

    // Fonction pour charger le feed
    async function loadFeed() {
        const response = await fetch(apiBase + 'get_posts.php');
        const data = await response.json();

        if (data.status === 'success') {
            feedContainer.innerHTML = '';
            data.data.forEach(post => {
                feedContainer.innerHTML += createPostHTML(post);
            });

            // Attacher les écouteurs d'événements aux boutons après le rendu
            attachEventListeners();
        }
    }

    // Fonction pour générer le HTML d'un post
    function createPostHTML(post) {
        const avatar = post.avatar ? '../../' + post.avatar : 'https://via.placeholder.com/50';
        const postImage = post.image_path ? `<img src="../../${post.image_path}" class="post-image">` : '';
        
        // Gestion des classes de couleurs pour les likes/dislikes
        const likeClass = post.user_reaction === 'like' ? 'active-like' : '';
        const dislikeClass = post.user_reaction === 'dislike' ? 'active-dislike' : '';

        return `
            <div class="post-card" data-post-id="${post.post_id}">
                <div class="post-header">
                    <img src="${avatar}" class="avatar">
                    <div>
                        <strong>${post.prenom} ${post.nom}</strong>
                        <small>${post.created_at}</small>
                    </div>
                </div>
                <div class="post-content">
                    <p>${post.content}</p>
                    ${postImage}
                </div>
                <div class="post-actions">
                    <button class="like-btn ${likeClass}" data-type="like">
                        👍 Like (<span class="like-count">${post.like_count}</span>)
                    </button>
                    <button class="like-btn ${dislikeClass}" data-type="dislike">
                        👎 Dislike (<span class="dislike-count">${post.dislike_count}</span>)
                    </button>
                    <button class="toggle-comments-btn">💬 Commentaires</button>
                </div>
                <div class="comments-section" style="display:none;">
                    <div class="comments-list"></div>
                    <div class="add-comment-box">
                        <input type="text" class="comment-input" placeholder="Écrire un commentaire...">
                        <button class="send-comment-btn">Envoyer</button>
                    </div>
                </div>
            </div>
        `;
    }

    // Gestion des événements dynamiques
    function attachEventListeners() {
        // Likes / Dislikes
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postId = this.closest('.post-card').dataset.postId;
                const type = this.dataset.type;

                const response = await fetch(apiBase + 'toggle_like.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: postId, type: type })
                });
                if (response.ok) {
                    loadFeed(); // Recharger le post pour mettre à jour les compteurs et les couleurs
                }
            });
        });

        // Toggle commentaires
        document.querySelectorAll('.toggle-comments-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const commentsSection = this.closest('.post-card').querySelector('.comments-section');
                const postId = this.closest('.post-card').dataset.postId;

                if (commentsSection.style.display === 'none') {
                    commentsSection.style.display = 'block';
                    await loadComments(postId, commentsSection.querySelector('.comments-list'));
                } else {
                    commentsSection.style.display = 'none';
                }
            });
        });

        // Envoi de commentaire
        document.querySelectorAll('.send-comment-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postCard = this.closest('.post-card');
                const postId = postCard.dataset.postId;
                const input = postCard.querySelector('.comment-input');
                const content = input.value.trim();

                if (!content) return;

                const response = await fetch(apiBase + 'add_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: postId, content: content })
                });
                const data = await response.json();

                if (data.status === 'success') {
                    input.value = '';
                    // Recharger uniquement la liste des commentaires
                    loadComments(postId, postCard.querySelector('.comments-list'));
                }
            });
        });
    }

    // Charger les commentaires
    async function loadComments(postId, container) {
        const response = await fetch(apiBase + `get_comments.php?post_id=${postId}`);
        const data = await response.json();

        if (data.status === 'success') {
            container.innerHTML = '';
            data.data.forEach(comment => {
                const avatar = comment.avatar ? '../../' + comment.avatar : 'https://via.placeholder.com/30';
                container.innerHTML += `
                    <div class="comment-item">
                        <img src="${avatar}" class="mini-avatar">
                        <div>
                            <strong>${comment.prenom} ${comment.nom}</strong>
                            <p>${comment.content}</p>
                            <small>${comment.created_at}</small>
                        </div>
                    </div>
                `;
            });
        }
    }
});