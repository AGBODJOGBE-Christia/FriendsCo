<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) {
    sendJson(['status' => 'error', 'message' => 'Non connecté.']);
}

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
if ($stmt->execute([$data['post_id'], $_SESSION['user_id'], $data['content']])) {
    // On retourne l'ID du commentaire et la date pour l'afficher dynamiquement
    $newId = $pdo->lastInsertId();
    sendJson(['status' => 'success', 'comment_id' => $newId, 'created_at' => date('Y-m-d H:i:s')]);
} else {
    sendJson(['status' => 'error', 'message' => 'Erreur lors de l\'ajout du commentaire.']);
}
?>