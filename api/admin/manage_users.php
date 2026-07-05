<?php
require_once '../../config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'moderator')) {
    sendJson(['status' => 'error', 'message' => 'Accès non autorisé.']);
}

$method = $_SERVER['REQUEST_METHOD'];
$current_admin_id = $_SESSION['user_id'];

// GET : Récupérer la liste des utilisateurs
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, nom, prenom, email, role, created_at FROM users ORDER BY created_at DESC");
    sendJson(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

// POST : Suppression, Changement de rôle, ou Ajout d'un nouveau membre 
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'];
    
    // Vérification de sécurité : Seul un ADMIN peut ajouter ou promouvoir
    if (($action === 'add_admin' || $action === 'add_moderator') && $_SESSION['role'] !== 'admin') {
        sendJson(['status' => 'error', 'message' => 'Action réservée aux administrateurs.']);
    }

    // ACTIONS ADMIN 
    if ($action === 'add_admin') {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
        }
        // Création du compte admin avec mot de passe par défaut (admin123), à changer par l'admin plus tard
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password_hash, role) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $hash]);
        sendJson(['status' => 'success', 'message' => 'Nouvel administrateur ajouté avec succès.']);

    } elseif ($action === 'add_moderator') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            sendJson(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
        }
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password_hash, role) VALUES (?, ?, ?, ?, 'moderator')");
        $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $hash]);
        sendJson(['status' => 'success', 'message' => 'Nouveau modérateur ajouté avec succès.']);

    } elseif ($action === 'delete_user') {
        // garde le code existant ici
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
        $stmt->execute([$data['target_id'], $_SESSION['user_id']]);
        sendJson(['status' => 'success', 'message' => 'Utilisateur supprimé.']);

    } elseif ($action === 'promote_moderator') {
        
        $stmt = $pdo->prepare("UPDATE users SET role = 'moderator' WHERE id = ? AND role != 'admin'");
        $stmt->execute([$data['target_id']]);
        sendJson(['status' => 'success', 'message' => 'Utilisateur promu Modérateur.']);
    }
}
?>