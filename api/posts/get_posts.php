<?php
require_once '../../config.php';

$user_id = $_SESSION['user_id'] ?? null;

$sql = "SELECT 
            p.id as post_id, p.content, p.image_path, p.created_at,
            u.id as user_id, u.nom, u.prenom, u.avatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND type = 'like') as like_count,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND type = 'dislike') as dislike_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque post, on vérifie si l'utilisateur connecté a déjà liké/disliké
foreach ($posts as &$post) {
    $post['user_reaction'] = null;
    if ($user_id) {
        $reactionStmt = $pdo->prepare("SELECT type FROM likes WHERE post_id = ? AND user_id = ?");
        $reactionStmt->execute([$post['post_id'], $user_id]);
        $reaction = $reactionStmt->fetch();
        if ($reaction) {
            $post['user_reaction'] = $reaction['type'];
        }
    }
}

sendJson(['status' => 'success', 'data' => $posts]);
?>