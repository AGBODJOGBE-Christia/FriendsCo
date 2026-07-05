<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) sendJson(['status' => 'error']);
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
$receiver_id = $data['receiver_id'];
$message = $data['message'] ?? '';

if (!$receiver_id) sendJson(['status' => 'error', 'message' => 'Destinataire manquant.']);

$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $receiver_id, $message]);

sendJson(['status' => 'success', 'message' => 'Message envoyé.']);
?>