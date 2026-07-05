<?php
require_once '../../config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'moderator')) {
    sendJson(['status' => 'error', 'message' => 'Accès non autorisé.']);
}

$method = $_SERVER['REQUEST_METHOD'];

// GET : Récupérer tous les posts avec le nom de l'auteur
if ($method === 'GET') {
    $sql = "SELECT p.id, p.content, p.image_path, p.created_at, u.nom, u.prenom 
            FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
    sendJson(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

// POST : Supprimer un post
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $post_id = $data['post_id'];

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    sendJson(['status' => 'success', 'message' => 'Post supprimé avec succès.']);
}
?>