<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) sendJson(['status' => 'error', 'message' => 'Non connecté.']);
$current_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$target_id = $data['target_id'];
$action = $data['action']; // 'send', 'accept', 'reject', 'cancel'

if ($action === 'send') {
    $stmt = $pdo->prepare("INSERT INTO friendships (sender_id, receiver_id, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$current_id, $target_id]);
    sendJson(['status' => 'success', 'message' => 'Invitation envoyée.']);
} elseif ($action === 'accept') {
    $stmt = $pdo->prepare("UPDATE friendships SET status = 'accepted' WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
    $stmt->execute([$target_id, $current_id, $current_id, $target_id]);
    sendJson(['status' => 'success', 'message' => 'Demande acceptée.']);
} elseif ($action === 'reject' || $action === 'cancel') {
    $stmt = $pdo->prepare("DELETE FROM friendships WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
    $stmt->execute([$target_id, $current_id, $current_id, $target_id]);
    sendJson(['status' => 'success', 'message' => 'Action effectuée.']);
}
?>