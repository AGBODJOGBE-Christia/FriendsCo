<?php
require_once '../../config.php';
if (!isset($_SESSION['user_id'])) sendJson(['status' => 'error']);

$user_id = $_SESSION['user_id'];

$sql = "SELECT u.id, u.nom, u.prenom, u.avatar,
        (SELECT MAX(created_at) FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id)) as last_msg_time
        FROM users u
        JOIN friendships f ON (u.id = f.sender_id OR u.id = f.receiver_id)
        WHERE (f.sender_id = ? OR f.receiver_id = ?) AND f.status = 'accepted' AND u.id != ?
        ORDER BY last_msg_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendJson(['status' => 'success', 'data' => $contacts]);
?>