<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) {
    sendJson(['status' => 'error', 'message' => 'Non connecté.']);
}

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$type = $data['type']; // 'like' ou 'dislike'
$user_id = $_SESSION['user_id'];

// Vérifier si l'utilisateur a déjà réagi au post
$stmt = $pdo->prepare("SELECT id, type FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$existing = $stmt->fetch();

if ($existing) {
    if ($existing['type'] === $type) {
        // Si c'est la même réaction, on la supprime (toggle off)
        $delete = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $delete->execute([$existing['id']]);
        sendJson(['status' => 'success', 'action' => 'removed']);
    } else {
        // Si c'est une réaction différente, on met à jour (passer de like à dislike vice versa)
        $update = $pdo->prepare("UPDATE likes SET type = ? WHERE id = ?");
        $update->execute([$type, $existing['id']]);
        sendJson(['status' => 'success', 'action' => 'updated']);
    }
} else {
    // Nouvelle réaction
    $insert = $pdo->prepare("INSERT INTO likes (post_id, user_id, type) VALUES (?, ?, ?)");
    $insert->execute([$post_id, $user_id, $type]);
    sendJson(['status' => 'success', 'action' => 'added']);
}
?>