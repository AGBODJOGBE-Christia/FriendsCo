<?php
require_once '../../config.php';

// Vérification du rôle (admin ou modérateur)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'moderator')) {
    sendJson(['status' => 'error', 'message' => 'Accès non autorisé.']);
}

$stats = [];

$stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['total_posts'] = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$stats['total_comments'] = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$stats['total_messages'] = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Stats pour l'admin (rôles)
$stats['admins'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$stats['moderators'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'moderator'")->fetchColumn();

sendJson(['status' => 'success', 'data' => $stats]);
?>