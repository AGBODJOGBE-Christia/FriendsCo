<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) sendJson(['status' => 'error', 'message' => 'Non connecté.']);

$stmt = $pdo->prepare("SELECT nom, prenom, bio, avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // On renvoie le chemin complet de l'avatar pour l'afficher
    $user['avatar_full'] = $user['avatar'] ? '../../' . $user['avatar'] : 'https://via.placeholder.com/100';
    sendJson(['status' => 'success', 'data' => $user]);
} else {
    sendJson(['status' => 'error', 'message' => 'Utilisateur introuvable.']);
}
?>