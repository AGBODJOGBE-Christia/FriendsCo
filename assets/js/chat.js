document.addEventListener('DOMContentLoaded', () => {
    const user = JSON.parse(sessionStorage.getItem('user'));
    if (!user) window.location.href = 'login.html';

    let currentContactId = null;

    //Charger les contacts
    function loadContacts() {
        fetch('../../api/chat/get_contacts.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const list = document.getElementById('contactsList');
                    list.innerHTML = '';
                    data.data.forEach(c => {
                        const avatar = c.avatar ? '../../' + c.avatar : 'https://via.placeholder.com/40';
                        const div = document.createElement('div');
                        div.className = 'contact-item';
                        div.innerHTML = `<img src="${avatar}"> ${c.prenom} ${c.nom}`;
                        div.onclick = () => selectContact(c.id, c.prenom, c.nom);
                        list.appendChild(div);
                    });
                }
            });
    }
    loadContacts();

    //Sélectionner un contact
    function selectContact(id, prenom, nom) {
        currentContactId = id;
        document.getElementById('chatHeader').innerText = `Discussion avec ${prenom} ${nom}`;
        document.querySelector('.message-input-box').style.display = 'flex';
        loadMessages();
    }

    //Charger les messages (Polling toutes les 3s si un contact est sélectionné)
    function loadMessages() {
        if (!currentContactId) return;
        fetch(`../../api/chat/get_messages.php?contact_id=${currentContactId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const container = document.getElementById('messagesContainer');
                    container.innerHTML = '';
                    data.data.forEach(msg => {
                        const div = document.createElement('div');
                        div.className = msg.sender_id === user.id ? 'msg-sent' : 'msg-received';
                        div.innerText = msg.message;
                        container.appendChild(div);
                    });
                    container.scrollTop = container.scrollHeight; // Auto-scroll
                }
            });
    }

    // Envoyer un message
    document.getElementById('sendMsgBtn').onclick = async () => {
        const input = document.getElementById('msgInput');
        const message = input.value.trim();
        if (!message || !currentContactId) return;

        const res = await fetch('../../api/chat/send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ receiver_id: currentContactId, message: message })
        });
        if (res.ok) {
            input.value = '';
            loadMessages();
        }
    };

    // POLLING TOUTES LES 3 SECONDES 
    setInterval(() => {
        if (currentContactId) {
            loadMessages();
        }
    }, 3000);
});