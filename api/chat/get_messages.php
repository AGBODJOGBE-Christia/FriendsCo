<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) sendJson(['status' => 'error']);
$user_id = $_SESSION['user_id'];
$contact_id = $_GET['contact_id'] ?? null;

if (!$contact_id) sendJson(['status' => 'error', 'message' => 'Contact manquant.']);

// marque les messages comme lus
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?")->execute([$contact_id, $user_id]);

//récupère l'historique des messages entre l'utilisateur et le contact
$stmt = $pdo->prepare("SELECT sender_id, message, image_path, created_at 
                        FROM messages 
                        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                        ORDER BY created_at ASC");
$stmt->execute([$user_id, $contact_id, $contact_id, $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendJson(['status' => 'success', 'data' => $messages]);
?>