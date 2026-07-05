<?php
require_once '../../config.php';

$post_id = $_GET['post_id'] ?? null;
if (!$post_id) sendJson(['status' => 'error', 'message' => 'ID du post manquant.']);

$stmt = $pdo->prepare("
    SELECT c.id, c.content, c.created_at, u.id as user_id, u.nom, u.prenom, u.avatar 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendJson(['status' => 'success', 'data' => $comments]);
?>